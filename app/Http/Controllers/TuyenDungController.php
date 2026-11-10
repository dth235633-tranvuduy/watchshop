<?php

namespace App\Http\Controllers;

use App\Models\DonUngTuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TuyenDungController extends Controller
{
    /**
     * List all job applications for the admin panel.
     */
    public function getDanhSach()
    {
        $donUngTuyen = DonUngTuyen::latest()->get();

        return view('admin.tuyendung.danhsach', compact('donUngTuyen'));
    }

    /**
     * Update the review status of an application.
     */
    public function postCapNhat(Request $request, int $id)
    {
        $request->validate([
            'trangthai' => 'required|in:cho_duyet,da_duyet,tu_choi',
        ]);

        $donUngTuyen = DonUngTuyen::findOrFail($id);
        $donUngTuyen->trangthai = $request->trangthai;
        $donUngTuyen->save();

        return redirect()->route('admin.tuyendung.danhsach')
            ->with('success', 'Đã cập nhật trạng thái đơn ứng tuyển.');
    }

    /**
     * Stream a stored CV from the private disk to an authorised admin.
     */
    public function getCv(int $id): StreamedResponse
    {
        $donUngTuyen = DonUngTuyen::findOrFail($id);

        abort_unless(Storage::disk('private')->exists($donUngTuyen->cv_path), 404);

        return Storage::disk('private')->response($donUngTuyen->cv_path);
    }
}