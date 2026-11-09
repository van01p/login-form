<div class="grid">
    @forelse ($products as $p)
        <div class="product-card">
            @if ($p->image)
                <img class="cover" src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}">
            @else
                <div class="placeholder">{{ strtoupper(substr($p->name, 0, 1)) }}</div>
            @endif

            <div class="product-body">
                <strong>{{ $p->name }}</strong>
                <div><span class="badge">{{ $p->category->name }}</span></div>
                <div class="muted">Toko: {{ $p->user->name }}</div>
                <div class="price">Rp{{ number_format($p->price, 0, ',', '.') }}</div>
                <div class="muted">Stok {{ $p->stock }} · Terjual {{ $p->sold }}</div>

                @if ($p->user_id === auth()->id())
                    <div class="muted" style="margin-top:auto">Produk kamu</div>
                @elseif ($p->stock < 1)
                    <button class="btn" style="margin-top:auto" disabled>Stok habis</button>
                @else
                    <form class="qty-row" method="POST" action="{{ route('cart.add', $p) }}">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" max="{{ $p->stock }}">
                        <button class="btn" style="flex:1">+ Keranjang</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <p>Belum ada produk.</p>
    @endforelse
</div>