@extends('layouts.frontend')

@section('title', 'Đặt Hàng Thành Công - Watch Shop')

@section('content')
<div class="text-center py-5">
    <i class="fas fa-check-circle fa-4x mb-3" style="color:var(--ws-gold);"></i>
    <h2 class="mb-2">Đặt hàng thành công</h2>
    <p class="text-muted mb-4">Cảm ơn bạn đã mua hàng. Đơn hàng đang được xử lý và sẽ sớm được giao đến bạn.</p>
    <a href="{{ route('user.donhang') }}" class="btn btn-gold me-2"><i class="fas fa-shopping-bag me-1"></i>Xem đơn hàng</a>
    <a href="{{ route('frontend.sanpham') }}" class="btn btn-outline-dark">Tiếp tục mua sắm</a>
</div>
@endsection