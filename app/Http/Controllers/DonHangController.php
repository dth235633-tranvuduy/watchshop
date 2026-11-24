<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\DonHang_ChiTiet;
use App\Models\TinhTrang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonHangController extends Controller
{
    public function getDanhSach(Request $request)
    {
        $this->ensureDefaultStatuses();
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'status' => ['nullable', 'integer', 'exists:tinhtrang,id'],
            'payment' => ['nullable', 'string', 'in:cod,bank'],
        ]);

        $donhang = $this->filteredOrders($filters)->get();
        $tinhtrang = TinhTrang::orderBy('id')->get();

        return view('admin.donhang.danhsach', compact('donhang', 'tinhtrang', 'filters'));
    }

    public function getThem()
    {
        abort(404);
    }

    public function postThem(Request $request)
    {
        // TODO: Xử lý thêm đơn hàng
        return redirect()->route('admin.donhang.danhsach');
    }

    public function getSua(int $id)
    {
        $relations = ['User', 'TinhTrang', 'DonHang_ChiTiet.SanPham'];
        if (DonHang::coBangLichSu()) {
            $relations[] = 'LichSu.User';
        }
        $donhang = DonHang::with($relations)->findOrFail($id);
        $this->ensureDefaultStatuses();
        $tinhtrang = TinhTrang::all();

        return view('admin.donhang.sua', compact('donhang', 'tinhtrang'));
    }

    public function postSua(Request $request, int $id)
    {
        $donhang = DonHang::findOrFail($id);

        $validated = $request->validate([
            'dienthoaigiaohang' => ['required', 'string', 'max:20'],
            'diachigiaohang' => ['required', 'string', 'max:255'],
            'tennguoinhan' => ['required', 'string', 'max:255'],
            'ghichu' => ['nullable', 'string', 'max:1000'],
        ]);

        $donhang->tennguoinhan = $validated['tennguoinhan'];
        $donhang->dienthoaigiaohang = $validated['dienthoaigiaohang'];
        $donhang->diachigiaohang = $validated['diachigiaohang'];
        $donhang->ghichu = $validated['ghichu'] ?? null;
        $donhang->save();

        return redirect()->route('admin.donhang.danhsach')->with('success', 'Cập nhật đơn hàng thành công.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'tinhtrang_id' => ['required', 'integer', 'exists:tinhtrang,id'],
        ]);
        $donhang = DonHang::findOrFail($id);

        if ((int) $validated['tinhtrang_id'] !== (int) $donhang->tinhtrang_id) {
            $donhang->update(['tinhtrang_id' => $validated['tinhtrang_id']]);
            $donhang->ghiLichSu(Auth::id(), 'Quản trị viên cập nhật trạng thái đơn hàng.');
        }

        return back()->with('success', 'Đã cập nhật tình trạng đơn hàng.');
    }

    private function ensureDefaultStatuses(): void
    {
        foreach (['Chưa thanh toán', 'Đã thanh toán', 'Đã hủy'] as $status) {
            TinhTrang::firstOrCreate(['tinhtrang' => $status]);
        }
    }

    private function filteredOrders(array $filters)
    {
        $query = DonHang::with(['User', 'TinhTrang', 'DonHang_ChiTiet.SanPham'])
            ->latest();

        if (! empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (! empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }
        if (! empty($filters['status'])) {
            $query->where('tinhtrang_id', $filters['status']);
        }
        if (! empty($filters['payment'])) {
            $query->where('phuongthucthanhtoan', $filters['payment']);
        }

        return $query;
    }

    public function getIn(int $id)
    {
        $donhang = DonHang::with(['User', 'TinhTrang', 'Voucher', 'DonHang_ChiTiet.SanPham'])
            ->findOrFail($id);

        return view('admin.donhang.in', compact('donhang'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'status' => ['nullable', 'integer', 'exists:tinhtrang,id'],
            'payment' => ['nullable', 'string', 'in:cod,bank'],
        ]);

        $orders = $this->filteredOrders($filters)->get();
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hoa don');

        $headers = [
            'Mã đơn',
            'Ngày đặt',
            'Khách hàng',
            'Người nhận',
            'Điện thoại',
            'Địa chỉ',
            'Phương thức thanh toán',
            'Tên sản phẩm',
            'Số lượng',
            'Đơn giá',
            'Thành tiền',
            'Giảm giá',
            'Tổng thanh toán',
            'Tình trạng',
            'Ghi chú',
            'Lý do hủy',
        ];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($orders as $order) {
            $details = $order->DonHang_ChiTiet;
            if ($details->isEmpty()) {
                $details = collect([null]);
            }

            foreach ($details as $detail) {
                $productName = $detail?->tensanpham_snapshot
                    ?: $detail?->SanPham?->tensanpham
                    ?: 'Sản phẩm đã xóa';
                $quantity = $detail ? (int) $detail->soluongban : 0;
                $unitPrice = $detail ? (float) $detail->dongiaban : 0;
                $lineTotal = $quantity * $unitPrice;

                $sheet->fromArray([
                    '#'.$order->id,
                    optional($order->created_at)->format('d/m/Y H:i'),
                    optional($order->User)->name,
                    $order->tennguoinhan ?: optional($order->User)->name,
                    $order->dienthoaigiaohang,
                    $order->diachigiaohang,
                    $order->phuongthucthanhtoan === 'bank'
                        ? 'Chuyển khoản ngân hàng'
                        : 'Thanh toán khi nhận hàng (COD)',
                    $productName,
                    $quantity,
                    $unitPrice,
                    $lineTotal,
                    (float) $order->tiengiam,
                    (float) $order->tongtien,
                    optional($order->TinhTrang)->tinhtrang,
                    $order->ghichu,
                    $order->lydo_huy,
                ], null, 'A'.$row);
                $row++;
            }
        }

        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getStyle('A1:P1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('D9EAD3'));
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $fileName = 'hoa-don-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function getXoa(int $id)
    {
        $donhang = DonHang::findOrFail($id);

        // Xóa hết chi tiết đơn hàng trước để tránh lỗi ràng buộc khóa ngoại (foreign key)
        DonHang_ChiTiet::where('donhang_id', $id)->delete();

        $donhang->delete();

        return redirect()->route('admin.donhang.danhsach')->with('success', 'Xóa đơn hàng thành công.');
    }
}
