@extends('layouts.app')

@section('title', 'Keranjang')

@section('content')
<div class="card">
    <h2 style="margin-top:0">Keranjang Belanja</h2>

    @if ($items->isEmpty())
        <p>Keranjang masih kosong.</p>
        <a class="btn" href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('products.index') }}">Mulai belanja</a>
    @else
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Toko</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->user->name }}</td>
                    <td>Rp{{ number_format($item->product->price, 0, ',', '.') }}</td>
                    <td>
                        <form class="inline" method="POST" action="{{ route('cart.update', $item) }}" style="display:flex;gap:6px">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" style="width:80px;padding:6px">
                            <button class="btn btn-light">Ubah</button>
                        </form>
                    </td>
                    <td>Rp{{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}</td>
                    <td>
                        <form class="inline" method="POST" action="{{ route('cart.remove', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right">Total</th>
                    <th>Rp{{ number_format($total, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <form method="POST" action="{{ route('cart.checkout') }}"
              onsubmit="return confirm('Beli semua barang di keranjang?')"
              style="margin-top:20px;text-align:right">
            @csrf
            <button class="btn" style="font-size:1rem;padding:12px 28px">Beli Sekarang</button>
        </form>
    @endif
</div>
@endsection