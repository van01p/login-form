<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="user-id" content="{{ auth()->id() ?? 0 }}">
    <title>@yield('title', 'login-form')</title>
    <style>
        :root {
            --emerald: #059669;
            --emerald-dark: #047857;
            --mint: #d1fae5;
            --mint-soft: #ecfdf5;
            --text: #064e3b;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; background: var(--mint-soft); color: var(--text); }
        nav { background: var(--emerald); color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
        nav .brand { font-weight: 700; font-size: 1.1rem; margin-right: 20px; }
        nav a.link { color: #fff; text-decoration: none; margin-right: 16px; font-size: .95rem; }
        nav a.link:hover { text-decoration: underline; }
        nav span { margin-right: 12px; font-size: .9rem; }
        .container { max-width: 1000px; margin: 28px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(5,150,105,.12); }
        .btn { display: inline-block; background: var(--emerald); color: #fff; border: 0; padding: 8px 16px; border-radius: 8px; cursor: pointer; text-decoration: none; font-size: .9rem; }
        .btn:hover { background: var(--emerald-dark); }
        .btn-light { background: var(--mint); color: var(--emerald-dark); }
        .btn-light:hover { background: #a7f3d0; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn:disabled { opacity: .5; cursor: not-allowed; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: var(--mint); text-align: left; }
        th, td { padding: 10px 12px; border-bottom: 1px solid var(--mint); font-size: .92rem; }
        img.thumb { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; }
        label { display: block; margin: 14px 0 6px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #a7f3d0; border-radius: 8px; font: inherit; }
        input:focus, select:focus, textarea:focus { outline: 2px solid var(--emerald); }
        .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; }
        .alert-success { background: var(--mint); color: var(--emerald-dark); }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .badge { background: var(--mint); color: var(--emerald-dark); padding: 2px 10px; border-radius: 999px; font-size: .8rem; }
        form.inline { display: inline; }
        .muted { color: var(--emerald-dark); font-size: .85rem; }

        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .stat { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(5,150,105,.12); }
        .stat small { color: var(--emerald-dark); }
        .stat strong { display: block; font-size: 1.5rem; margin-top: 4px; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 16px; margin-top: 12px; }
        .product-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(5,150,105,.12); display: flex; flex-direction: column; }
        .product-card img.cover { width: 100%; height: 150px; object-fit: cover; }
        .placeholder { width: 100%; height: 150px; background: var(--mint); color: var(--emerald-dark); display: flex; align-items: center; justify-content: center; font-size: 2.4rem; font-weight: 700; }
        .product-body { padding: 14px; display: flex; flex-direction: column; gap: 6px; flex: 1; }
        .price { font-weight: 700; color: var(--emerald-dark); font-size: 1.05rem; }
        .qty-row { display: flex; gap: 8px; margin-top: auto; padding-top: 8px; }
        .qty-row input { width: 70px; padding: 8px; }

        .seller-card { display: flex; flex-direction: column; gap: 6px; background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 8px rgba(5,150,105,.12); text-decoration: none; color: inherit; border: 2px solid transparent; }
        .seller-card:hover { border-color: var(--emerald); }
    </style>
</head>
<body>
@auth
<nav>
    <div>
        <span class="brand">login-form</span>

        @if (in_array(auth()->user()->role, ['admin', 'seller']))
            <a class="link" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="link" href="{{ route('products.index') }}">Kelola Produk</a>
        @else
            <a class="link" href="{{ route('products.index') }}">Belanja</a>
        @endif

        @if (in_array(auth()->user()->role, ['admin', 'customer']))
            <a class="link" href="{{ route('cart.index') }}">Keranjang ({{ auth()->user()->cartItems()->sum('quantity') }})</a>
        @endif
    </div>
    <div>
        <span>{{ auth()->user()->name }} <small class="badge">{{ auth()->user()->role }}</small></span>
        <form class="inline" method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light">Logout</button>
        </form>
    </div>
</nav>
@endauth

<div class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</div>

{{-- Sinkron akun antar tab: kalau akun di tab lain berubah, tab ini ikut reload --}}
<script>
(function () {
    const myId = String(document.querySelector('meta[name="user-id"]').content);
    const channel = 'BroadcastChannel' in window ? new BroadcastChannel('login-form-session') : null;
    let checking = false;

    async function check() {
        if (checking) return;
        checking = true;
        try {
            const res = await fetch('{{ route('whoami', [], false) }}', {
                cache: 'no-store',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (String(data.id) !== myId) {
                window.location.reload();
            }
        } catch (e) {}
        checking = false;
    }

    if (channel) {
        channel.onmessage = check;
        channel.postMessage('loaded');
    }
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) check();
    });
    window.addEventListener('focus', check);
    setInterval(check, 10000);
})();
</script>

@stack('scripts')
</body>
</html>