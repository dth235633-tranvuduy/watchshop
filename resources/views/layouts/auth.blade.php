<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Watch Shop')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ws-gold: #c9a227;
            --ws-gold-light: #e3c766;
            --ws-black: #0d0d0d;
            --ws-charcoal: #1a1a1a;
            --ws-line: #e6e2d8;
            --ws-cream: #faf8f3;
        }
        body { font-family: 'Inter', system-ui, sans-serif; background: var(--ws-cream); }
        .font-display { font-family: 'Playfair Display', serif; }

        .auth-form-pane { width: 100%; max-width: 340px; margin: 0 auto; }
        .auth-brand { font-family: 'Playfair Display', serif; font-weight: 800; font-size: 1.6rem; color: var(--ws-black); text-decoration: none; letter-spacing: .5px; }
        .auth-brand i, .auth-brand span { color: var(--ws-gold); }

        .auth-control.form-control, .auth-control.form-select {
            border-radius: 50rem;
            border: 1px solid var(--ws-line);
            background: #fff;
            padding-left: 1.15rem;
            font-size: .95rem;
        }
        .auth-control.form-control-lg { padding-top: .55rem; padding-bottom: .55rem; }
        .auth-control.form-control:focus { border-color: var(--ws-gold); box-shadow: 0 0 0 .2rem rgba(201,162,39,.15); }

        /* password toggle */
        .password-field { position: relative; }
        .password-field .auth-control { padding-right: 3rem; }
        .password-toggle {
            position: absolute; top: 50%; right: 1rem; transform: translateY(-50%);
            background: none; border: none; color: #9a9a9a; padding: 0; line-height: 1; cursor: pointer;
        }
        .password-toggle:hover { color: var(--ws-gold); }

        .btn-auth {
            background: var(--ws-black); border: 1px solid var(--ws-black); color: #fff;
            font-weight: 600; letter-spacing: .3px;
        }
        .btn-auth:hover, .btn-auth:focus { background: var(--ws-gold); border-color: var(--ws-gold); color: #fff; }

        .btn-social {
            border: 1px solid var(--ws-line); background: #fff; color: var(--ws-black);
            border-radius: 50rem; font-weight: 500; transition: .2s;
        }
        .btn-social:hover { border-color: var(--ws-gold); background: var(--ws-cream); color: var(--ws-black); }
        .btn-social .fa-google { color: #db4437; }
        .btn-social .fa-facebook-f { color: #1877f2; }
        .btn-social .fa-apple { color: #111; }

        .auth-divider { display: flex; align-items: center; gap: 12px; color: #9a9a9a; font-size: .8rem; }
        .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: var(--ws-line); }

        /* Cover pane */
        .auth-cover {
            background: linear-gradient(135deg, #0d0d0d 0%, #2b2413 55%, #4a3c15 100%);
            color: #fff; position: relative; overflow: hidden;
        }
        .auth-cover::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at 75% 25%, rgba(201,162,39,.28), transparent 55%);
            pointer-events: none;
        }
        .auth-cover .cover-content { position: relative; z-index: 2; }
        .auth-cover .cover-eyebrow { color: var(--ws-gold-light); text-transform: uppercase; letter-spacing: 3px; font-size: .75rem; font-weight: 600; }
        .auth-cover h1 { font-family: 'Playfair Display', serif; font-weight: 800; font-size: 2.6rem; line-height: 1.2; }
        .auth-cover .cover-clock { font-size: 5rem; color: rgba(201,162,39,.35); }

        /* Dark mode */
        [data-bs-theme="dark"] body { background: #111; }
        [data-bs-theme="dark"] .auth-brand { color: #f2f2f2; }
        [data-bs-theme="dark"] .auth-control.form-control { background: #1c1c1c; border-color: #333; color: #eee; }
        [data-bs-theme="dark"] .auth-control.form-control::placeholder { color: #777; }
        [data-bs-theme="dark"] .btn-social { background: #1c1c1c; border-color: #333; color: #eee; }
        [data-bs-theme="dark"] .text-muted { color: #9a9a9a !important; }
        [data-bs-theme="dark"] .auth-cover { background: linear-gradient(135deg, #000 0%, #1a1409 60%, #33290d 100%); }

        .theme-toggle { background: none; border: 1px solid var(--ws-line); border-radius: 50rem; width: 38px; height: 38px; color: var(--ws-black); }
        [data-bs-theme="dark"] .theme-toggle { border-color: #333; color: #eee; }

        /* Centered compact card on desktop, full-screen on mobile */
        .auth-shell { width: 100%; }
        .auth-card { background: #fff; overflow: hidden; }
        .auth-left { width: 100%; flex: 0 0 auto; }

        @media (min-width: 992px) {
            .auth-shell {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 32px;
            }
            .auth-card {
                display: flex;
                width: 100%;
                max-width: 880px;
                border-radius: 16px;
                box-shadow: 0 24px 60px rgba(0,0,0,.12);
            }
            .auth-left { width: 400px; flex: 0 0 400px; }
            .auth-right { flex: 1 1 auto; min-height: 560px; }
            .auth-cover { border-radius: 0 16px 16px 0; }
        }

        [data-bs-theme="dark"] .auth-card { background: #161616; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-shell">
      <div class="auth-card">

        {{-- LEFT: form pane --}}
        <div class="auth-left d-flex flex-column flex-grow-0 flex-shrink-0 bg-body">
            <div class="d-flex flex-column h-100 w-100">
                <div class="d-flex justify-content-between align-items-center p-4">
                    <a class="auth-brand" href="{{ route('frontend.home') }}"><i class="fas fa-clock me-1"></i>Watch<span>Shop</span></a>
                    <button type="button" class="theme-toggle" id="themeToggle" aria-label="Chuyển chế độ sáng/tối">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>

                <div class="flex-grow-1 d-flex align-items-center px-4 pb-4">
                    <div class="auth-form-pane">
                        @yield('form')
                    </div>
                </div>

                <div class="px-4 pb-4 text-center">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} Watch Shop. All rights reserved.</p>
                </div>
            </div>
        </div>

        {{-- RIGHT: cover pane --}}
        <div class="auth-cover auth-right d-none d-lg-flex flex-grow-1 align-items-center p-5">
            <div class="cover-content">
                <i class="fas fa-clock cover-clock mb-4 d-block"></i>
                <div class="cover-eyebrow mb-3">@yield('cover-eyebrow', 'Đẳng cấp & Tinh tế')</div>
                <h1 class="mb-3">@yield('cover-title', 'Mỗi khoảnh khắc, một kiệt tác')</h1>
                <p class="lead mb-0" style="color:#d8d0bd;max-width:420px;">@yield('cover-text', 'Khám phá bộ sưu tập đồng hồ chính hãng từ những thương hiệu danh giá nhất thế giới.')</p>
            </div>
        </div>

      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            var root = document.documentElement;
            var toggle = document.getElementById('themeToggle');
            var icon = toggle.querySelector('i');
            var saved = localStorage.getItem('ws-theme') || 'light';
            apply(saved);
            toggle.addEventListener('click', function () {
                var next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                apply(next);
                localStorage.setItem('ws-theme', next);
            });
            function apply(theme) {
                root.setAttribute('data-bs-theme', theme);
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
        })();

        document.querySelectorAll('.password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(btn.dataset.target);
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.querySelector('i').className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
            });
        });
    </script>
    @stack('scripts')
</body>
</html>