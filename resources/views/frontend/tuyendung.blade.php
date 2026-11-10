@extends('layouts.frontend')

@section('title', 'Tuyển Dụng - Watch Shop')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-briefcase me-2"></i>Tuyển Dụng</h2>
            <a href="{{ route('frontend.home') }}" class="btn btn-outline-info btn-sm"><i class="fas fa-arrow-left me-1"></i>Về trang chủ</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Chúng tôi đang tìm kiếm các ứng viên tài năng</h4>
                        <p>Watch Shop là hệ thống bán đồng hồ chính hãng hàng đầu, chúng tôi luôn sẵn sàng đón nhận những người đam mê và sáng tạo.</p>
                        
                        <h5 class="mt-4">Vị trí đang tuyển</h5>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Nhân viên tư vấn bán hàng</span>
                                <span class="badge bg-info rounded-pill">Hà Nội</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Nhân viên chăm sóc khách hàng</span>
                                <span class="badge bg-info rounded-pill">Hồ Chí Minh</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Nhân viên kho vận chuyển</span>
                                <span class="badge bg-info rounded-pill">Đà Nẵng</span>
                            </li>
                        </ul>

                        <form class="row g-3" method="POST" action="#">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="hoten" class="form-control" placeholder="Nhập họ và tên" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Điện thoại</label>
                                <input type="tel" name="dienthoai" class="form-control" placeholder="Nhập điện thoại" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vị trí ứng tuyển</label>
                                <select name="vitri" class="form-select" required>
                                    <option value="">-- Chọn vị trí --</option>
                                    <option>Nhân viên tư vấn bán hàng</option>
                                    <option>Nhân viên chăm sóc khách hàng</option>
                                    <option>Nhân viên kho vận chuyển</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi thêm (nếu có)</label>
                                <textarea name="ghichu" class="form-control" rows="3" placeholder="Kinh nghiệm, mục tiêu nghề nghiệp..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning px-4"><i class="fas fa-paper-plane me-1"></i>Gửi đơn ứng tuyển</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
