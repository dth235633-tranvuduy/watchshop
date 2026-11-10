@extends('layouts.frontend')

@section('title', 'Liên Hệ - Watch Shop')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-envelope me-2"></i>Liên Hệ</h2>
            <a href="{{ route('frontend.home') }}" class="btn btn-outline-info btn-sm"><i class="fas fa-arrow-left me-1"></i>Về trang chủ</a>
        </div>

        <div class="row g-4">
            <!-- Thông tin liên hệ -->
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle me-2 text-info"></i>Thông tin liên hệ</h5>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-3"><i class="fas fa-map-marker-alt me-2 text-info"></i> <strong>Địa chỉ:</strong> 123 Đường Đồng Hồ, Quận 1, TP. Hồ Chí Minh</li>
                            <li class="mb-3"><i class="fas fa-phone me-2 text-info"></i> <strong>Hotline:</strong> 0909.123.456</li>
                            <li class="mb-3"><i class="fas fa-envelope me-2 text-info"></i> <strong>Email:</strong> support@watchshop.com</li>
                            <li class="mb-3"><i class="fas fa-clock me-2 text-info"></i> <strong>Giờ làm việc:</strong> 8:00 - 21:00 (Thứ 2 - Chủ Nhật)</li>
                        </ul>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-info btn-sm"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-info btn-sm"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-outline-info btn-sm"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form liên hệ -->
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Gửi tin nhắn cho chúng tôi</h5>
                        <form method="POST" action="#">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Họ và tên</label>
                                    <input type="text" name="hoten" class="form-control" placeholder="Nhập họ và tên" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Tiêu đề</label>
                                    <input type="text" name="tieude" class="form-control" placeholder="Nhập tiêu đề tin nhắn" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nội dung tin nhắn</label>
                                    <textarea name="noidung" class="form-control" rows="5" placeholder="Nhập nội dung..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning px-4"><i class="fas fa-paper-plane me-1"></i>Gửi tin nhắn</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
