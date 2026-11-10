<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
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
        $revenue = (float) DB::table('donhang_chitiet')
            ->selectRaw('COALESCE(SUM(soluongban * dongiaban), 0) AS total')
            ->value('total');

        $stats = [
            'sanpham' => SanPham::count(),
            'donhang' => DonHang::count(),
            'nguoidung' => User::count(),
            'doanhthu' => $revenue,
            'donhang_moi' => DonHang::where('created_at', '>=', now()->startOfMonth())->count(),
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

        $hoatDong = $this->recentActivity();

        return view('admin.home', compact('stats', 'donhangMoi', 'sapHetHang', 'chart', 'hoatDong'));
    }

    /**
     * Revenue per month for the current year, sourced from order line items.
     *
     * @return array<int, float>
     */
    private function monthlyRevenue(): array
    {
        $rows = DB::table('donhang_chitiet')
            ->join('donhang', 'donhang.id', '=', 'donhang_chitiet.donhang_id')
            ->whereYear('donhang.created_at', now()->year)
            ->selectRaw('MONTH(donhang.created_at) AS thang, SUM(soluongban * dongiaban) AS total')
            ->groupByRaw('MONTH(donhang.created_at)')
            ->pluck('total', 'thang');

        $values = [];
        for ($month = 1; $month <= 12; $month++) {
            $values[] = (float) ($rows[$month] ?? 0);
        }

        return $values;
    }

    /**
     * Count pending job applications when the recruitment feature exists.
     */
    private function pendingApplicationsCount(): int
    {
        if (! class_exists(\App\Models\DonUngTuyen::class)) {
            return 0;
        }

        return \App\Models\DonUngTuyen::where('trangthai', 'cho_duyet')->count();
    }

    /**
     * Latest activity entries for the dashboard timeline.
     *
     * @return array<int, array{text: string, time: string}>
     */
    private function recentActivity(): array
    {
        if (! class_exists(\Spatie\Activitylog\Models\Activity::class)) {
            return [];
        }

        return \Spatie\Activitylog\Models\Activity::query()
            ->latest()
            ->take(8)
            ->get()
            ->map(fn ($activity): array => [
                'text' => $activity->description,
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