@extends('layouts.app')

@section('title', 'Dashboard - Watch Shop Admin')

@push('styles')
<style>
    .kpi-card {
        border: 1px solid var(--ws-line);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(13,13,13,.05);
        transition: transform .22s ease, box-shadow .22s ease;
        position: relative;
        overflow: hidden;
        padding: 20px;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 16px 38px rgba(13,13,13,.10); }
    .kpi-card.featured {
        background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
        color: #fff;
        border-color: #0d0d0d;
    }
    .kpi-card.featured::before {
        content: '';
        position: absolute; top: -40px; right: -40px;
        width: 140px; height: 140px; border-radius: 50%;
        background: radial-gradient(circle, rgba(201,162,39,.4) 0%, transparent 70%);
    }
    .kpi-card.featured .kpi-label { color: rgba(255,255,255,.65); }
    .kpi-card.featured .kpi-value { color: #fff; }
    .kpi-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
    }
    .kpi-value {
        font-family: 'Playfair Display', serif;
        font-weight: 800;
        font-size: 2rem;
        line-height: 1.05;
        letter-spacing: -.02em;
    }
    .kpi-label {
        font-size: .74rem;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #8a8a8a;
        font-weight: 700;
        margin-top: 6px;
    }
    .kpi-badge {
        font-size: .72rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 5px 12px;
        letter-spacing: .03em;
    }

    .vip-card {
        border: 1px solid var(--ws-line);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(13,13,13,.05);
    }
    .vip-card .card-header {
        background: #fff;
        border-bottom: 1px solid var(--ws-line);
        border-radius: 20px 20px 0 0 !important;
        font-weight: 700;
        padding: 18px 22px;
    }
    .chart-wrap { position: relative; min-height: 300px; }
    .chart-empty {
        position: absolute; inset: 0;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: #b8b2a4;
        pointer-events: none;
    }
    .chart-empty i { font-size: 2.6rem; margin-bottom: 10px; opacity: .6; }
    .chart-empty span { font-size: .9rem; font-weight: 500; }

    .timeline { position: relative; padding-left: 30px; }
    .timeline::before {
        content: ''; position: absolute; left: 10px; top: 6px; bottom: 6px;
        width: 2px; background: linear-gradient(to bottom, var(--ws-gold), var(--ws-line));
    }
    .timeline-item { position: relative; padding-bottom: 20px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item::before {
        content: ''; position: absolute; left: -26px; top: 4px;
        width: 12px; height: 12px; border-radius: 50%;
        background: #fff; border: 2px solid var(--ws-gold);
        box-shadow: 0 0 0 3px rgba(201,162,39,.12);
    }
    .timeline-text { font-size: .88rem; font-weight: 500; color: #333; }
    .timeline-time { font-size: .72rem; color: #a5a096; margin-top: 2px; }

    .empty-state { text-align: center; padding: 50px 16px; color: #b8b2a4; }
    .empty-state i { font-size: 2.6rem; opacity: .55; margin-bottom: 12px; display: block; }
    .empty-state span { font-size: .88rem; }

    .low-stock-item { border-radius: 12px; transition: background .15s; }
    .low-stock-item:hover { background: var(--ws-cream); }

    .section-eyebrow {
        font-size: .74rem; letter-spacing: .18em; text-transform: uppercase;
        color: #a5a096; font-weight: 700; margin-bottom: 4px;
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="section-eyebrow mb-1">Tổng quan</p>
        <h1 class="mb-0" style="font-weight:800; font-size:2.1rem;">Dashboard quản trị</h1>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">Xin chào, <strong style="color:var(--ws-gold);">{{ Auth::user()->name }}</strong></span>
    </div>
</div>

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card featured h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:rgba(201,162,39,.18); color:var(--ws-gold-light);">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="kpi-badge" style="background:rgba(201,162,39,.18); color:var(--ws-gold-light);">Doanh thu</span>
            </div>
            <div class="kpi-value">{{ number_format($stats['doanhthu'], 0, ',', '.') }}<span class="fs-6 ms-1" style="font-weight:600; opacity:.7;">đ</span></div>
            <div class="kpi-label">Tổng doanh thu</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:rgba(13,13,13,.06); color:var(--ws-black);"><i class="fas fa-receipt"></i></div>
                <span class="kpi-badge bg-gold-soft">Đơn mới</span>
            </div>
            <div class="kpi-value">{{ number_format($stats['donhang_moi']) }}</div>
            <div class="kpi-label">Đơn hàng mới</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:rgba(46,125,50,.10); color:#2e7d32;"><i class="fas fa-user-group"></i></div>
                <span class="kpi-badge" style="background:rgba(46,125,50,.12); color:#2e7d32;">Khách</span>
            </div>
            <div class="kpi-value">{{ number_format($stats['nguoidung']) }}</div>
            <div class="kpi-label">Khách hàng</div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:rgba(198,40,40,.10); color:#c62828;"><i class="fas fa-briefcase"></i></div>
                <span class="kpi-badge" style="background:rgba(198,40,40,.12); color:#c62828;">Ứng tuyển</span>
            </div>
            <div class="kpi-value">{{ number_format($stats['ungvien']) }}</div>
            <div class="kpi-label">Đơn ứng tuyển</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="vip-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line me-2" style="color:var(--ws-gold);"></i>Doanh thu theo tháng</span>
                <span class="badge rounded-pill" style="background:var(--ws-cream); color:#7a7a7a; font-weight:600;">{{ now()->year }}</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="chart-wrap">
                    <canvas id="revenueChart"></canvas>
                    <div class="chart-empty" id="chartEmpty" style="display:none;">
                        <i class="fas fa-chart-area"></i>
                        <span>Chưa có dữ liệu doanh thu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="vip-card h-100">
            <div class="card-header"><i class="fas fa-clock-rotate-left me-2" style="color:var(--ws-gold);"></i>Lịch sử hoạt động</div>
            <div class="card-body p-3 p-md-4">
                @if(count($hoatDong))
                    <div class="timeline">
                        @foreach($hoatDong as $log)
                            <div class="timeline-item">
                                <div class="timeline-text">{{ $log['text'] }}</div>
                                <div class="timeline-time">{{ $log['time'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <span>Chưa có hoạt động nào được ghi nhận.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="vip-card h-100">
            <div class="card-header"><i class="fas fa-receipt me-2" style="color:var(--ws-gold);"></i>Đơn hàng mới nhất</div>
            <div class="table-responsive">
                <table class="table table-hover table-vip align-middle mb-0">
                    <thead>
                        <tr><th class="ps-4">#</th><th>Khách</th><th class="text-center">Tình trạng</th><th class="text-end pe-4">Ngày</th></tr>
                    </thead>
                    <tbody>
                        @forelse($donhangMoi as $dh)
                            <tr>
                                <td class="ps-4 fw-semibold">#{{ $dh->id }}</td>
                                <td>{{ $dh->tennguoinhan }}</td>
                                <td class="text-center"><span class="badge rounded-pill bg-secondary">{{ optional($dh->TinhTrang)->tinhtrang }}</span></td>
                                <td class="text-end pe-4 small text-muted">{{ optional($dh->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Chưa có đơn hàng</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="vip-card h-100">
            <div class="card-header"><i class="fas fa-triangle-exclamation me-2" style="color:var(--ws-gold);"></i>Sản phẩm sắp hết hàng</div>
            <ul class="list-group list-group-flush p-2">
                @forelse($sapHetHang as $sp)
                    <li class="list-group-item low-stock-item d-flex justify-content-between align-items-center border-0">
                        <span class="text-truncate me-2 fw-medium">{{ $sp->tensanpham }}</span>
                        <span class="badge rounded-pill bg-{{ $sp->soluong > 0 ? 'warning' : 'danger' }}">{{ $sp->soluong }} còn lại</span>
                    </li>
                @empty
                    <li class="list-group-item border-0 text-center text-muted py-4">Không có sản phẩm sắp hết</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var canvas = document.getElementById('revenueChart');
        if (!canvas || typeof Chart === 'undefined') { return; }

        var data = @json($chart);
        var hasData = data.values.some(function (v) { return Number(v) > 0; });

        if (!hasData) {
            canvas.style.display = 'none';
            var empty = document.getElementById('chartEmpty');
            if (empty) { empty.style.display = 'flex'; }
            return;
        }

        var ctx = canvas.getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(201,162,39,.32)');
        gradient.addColorStop(1, 'rgba(201,162,39,0)');

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Doanh thu',
                    data: data.values,
                    borderColor: '#c9a227',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: .38,
                    pointBackgroundColor: '#0d0d0d',
                    pointBorderColor: '#c9a227',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0d0d0d',
                        titleFont: { family: 'Inter', weight: '600' },
                        bodyFont: { family: 'Inter' },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + Number(ctx.raw).toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0ece2' },
                        ticks: {
                            color: '#9a9488',
                            font: { family: 'Inter', size: 11 },
                            callback: function (v) { return Number(v).toLocaleString('vi-VN'); }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9a9488', font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });
    })();
</script>
@endpush