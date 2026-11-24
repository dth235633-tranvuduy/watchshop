@extends('layouts.frontend')

@section('title', 'Giỏ hàng trống - Watch Shop')

@section('content')
<div class="container py-5">
    <div class="vip-card text-center py-5 px-3">
        <i class="fas fa-shopping-bag fa-3x text-gold mb-3"></i>
        <h2 class="mb-2">Giỏ hàng của bạn đang trống</h2>
        <p class="text-muted mb-4">Hãy khám phá bộ sưu tập đồng hồ để bắt đầu mua sắm.</p>
        <a href="{{ route('frontend.home') }}" class="btn btn-gold px-4">
            <i class="fas fa-arrow-left me-1"></i>Tiếp tục mua sắm
        </a>
    </div>
</div>
@endsection
