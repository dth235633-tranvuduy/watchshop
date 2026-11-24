<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Banner;
use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\DonUngTuyen;
use App\Models\SanPham;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanTriVienController extends Controller
{
    /**
     * Render the VIP admin dashboard.
     *
     * Activity log is optional: if spatie/laravel-activitylog is installed it
     * feeds the timeline, otherwise the view renders a graceful empty state.
     */
    public function getHome()
    {
        $activeOrders = DonHang::query();
        $revenueQuery = DB::table('donhang_chitiet')
            ->join('donhang', 'donhang.id', '=', 'donhang_chitiet.donhang_id');
        $activeOrders->whereDoesntHave('TinhTrang', function ($query): void {
            $query->where('tinhtrang', 'Đã hủy');
        });
        $revenueQuery->join('tinhtrang', 'tinhtrang.id', '=', 'donhang.tinhtrang_id')
            ->where('tinhtrang.tinhtrang', '!=', 'Đã hủy');

        $revenue = (float) (clone $revenueQuery)
            ->selectRaw('COALESCE(SUM(soluongban * dongiaban), 0) AS total')
            ->value('total');

        $stats = [
            'sanpham' => SanPham::count(),
            'donhang' => (clone $activeOrders)->count(),
            'nguoidung' => User::count(),
            'doanhthu' => $revenue,
            'donhang_moi' => (clone $activeOrders)->where('created_at', '>=', now()->startOfMonth())->count(),
            'khachhang_moi' => User::where('role', 'user')->where('created_at', '>=', now()->startOfMonth())->count(),
            'ton_kho_thap' => SanPham::where('soluong', '<', 3)->count(),
            'ungvien' => $this->pendingApplicationsCount(),
        ];

        $donhangMoi = DonHang::with('TinhTrang')
            ->latest()
            ->take(8)
            ->get();

        $sapHetHang = SanPham::where('soluong', '<=', 5)
            ->orderBy('soluong')
            ->take(8)
            ->get();

        $chart = [
            'labels' => ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            'values' => $this->monthlyRevenue(),
        ];
        $dailyChart = $this->dailyRevenue();

        $topProducts = DonHang_ChiTiet::query()
            ->select('sanpham_id', DB::raw('SUM(soluongban) AS total_quantity'), DB::raw('SUM(soluongban * dongiaban) AS total_revenue'))
            ->join('donhang', 'donhang.id', '=', 'donhang_chitiet.donhang_id')
            ->join('tinhtrang', 'tinhtrang.id', '=', 'donhang.tinhtrang_id')
            ->where('tinhtrang.tinhtrang', '!=', 'Đã hủy')
            ->with('SanPham')
            ->groupBy('sanpham_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();
        $orderStatuses = DB::table('tinhtrang')
            ->leftJoin('donhang', 'donhang.tinhtrang_id', '=', 'tinhtrang.id')
            ->select('tinhtrang.tinhtrang', DB::raw('COUNT(donhang.id) AS total'))
            ->groupBy('tinhtrang.id', 'tinhtrang.tinhtrang')
            ->get();
        $banners = Banner::where('trang_thai', true)->orderBy('thu_tu')->get();

        $hoatDong = $this->recentActivity();

        return view('admin.home', compact('stats', 'donhangMoi', 'sapHetHang', 'chart', 'dailyChart', 'hoatDong', 'topProducts', 'orderStatuses', 'banners'));
    }

    /**
     * Revenue per month for the current year, sourced from order line items.
     *
     * @return array<int, float>
     */
    private function monthlyRevenue(): array
    {
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', donhang.created_at) AS INTEGER)"
            : 'MONTH(donhang.created_at)';
        $rows = DB::table('donhang_chitiet')
            ->join('donhang', 'donhang.id', '=', 'donhang_chitiet.donhang_id')
            ->join('tinhtrang', 'tinhtrang.id', '=', 'donhang.tinhtrang_id')
            ->whereYear('donhang.created_at', now()->year)
            ->where('tinhtrang.tinhtrang', '!=', 'Đã hủy')
            ->selectRaw($monthExpression.' AS thang, SUM(soluongban * dongiaban) AS total')
            ->groupByRaw($monthExpression)
            ->pluck('total', 'thang');

        $values = [];
        for ($month = 1; $month <= 12; $month++) {
            $values[] = (float) ($rows[$month] ?? 0);
        }

        return $values;
    }

    /**
     * Revenue totals for the last seven days.
     *
     * @return array{labels: array<int, string>, values: array<int, float>}
     */
    private function dailyRevenue(): array
    {
        $dateExpression = DB::connection()->getDriverName() === 'sqlite'
            ? 'date(donhang.created_at)'
            : 'DATE(donhang.created_at)';
        $rows = DB::table('donhang_chitiet')
            ->join('donhang', 'donhang.id', '=', 'donhang_chitiet.donhang_id')
            ->join('tinhtrang', 'tinhtrang.id', '=', 'donhang.tinhtrang_id')
            ->where('donhang.created_at', '>=', now()->subDays(6)->startOfDay())
            ->where('tinhtrang.tinhtrang', '!=', 'Đã hủy')
            ->selectRaw($dateExpression.' AS ngay, SUM(soluongban * dongiaban) AS total')
            ->groupByRaw($dateExpression)
            ->pluck('total', 'ngay');
        $labels = [];
        $values = [];

        for ($offset = 6; $offset >= 0; $offset--) {
            $date = now()->subDays($offset);
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $values[] = (float) ($rows[$key] ?? 0);
        }

        return compact('labels', 'values');
    }

    /**
     * Count pending job applications when the recruitment feature exists.
     */
    private function pendingApplicationsCount(): int
    {
        if (! class_exists(DonUngTuyen::class)) {
            return 0;
        }

        return DonUngTuyen::where('trangthai', 'cho_duyet')->count();
    }

    /**
     * Latest activity entries for the dashboard timeline.
     *
     * @return array<int, array{text: string, time: string}>
     */
    private function recentActivity(): array
    {
        return ActivityLog::query()
            ->with('user')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($activity): array => [
                'text' => ($activity->user?->name ?? 'Hệ thống').' · '.$activity->description,
                'time' => $activity->created_at?->diffForHumans() ?? '',
            ])
            ->all();
    }

    public function getDanhSach()
    {
        return view('admin.quantrivien.danhsach');
    }

    public function getThem()
    {
        return view('admin.quantrivien.them');
    }

    public function postThem(Request $request)
    {
        return redirect()->route('admin.home');
    }

    public function getSua($id)
    {
        return view('admin.quantrivien.sua');
    }

    public function postSua(Request $request, $id)
    {
        return redirect()->route('admin.home');
    }

    public function getXoa($id)
    {
        return redirect()->route('admin.home');
    }
}
