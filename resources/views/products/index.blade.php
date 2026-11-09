@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <h2 style="margin:0">Daftar Produk</h2>
        @if (in_array(auth()->user()->role, ['admin', 'seller']))
            <a class="btn" href="{{ route('products.create') }}">+ Tambah Produk</a>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Terjual</th>
                <th>Penjual</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($products as $p)
            <tr>
                <td>
                    @if ($p->image)
                        <img class="thumb" src="{{ asset('storage/'.$p->image) }}">
                    @endif
                </td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name }}</td>
                <td>Rp{{ number_format($p->price, 0, ',', '.') }}</td>
                <td>{{ $p->stock }}</td>
                <td>{{ $p->sold }}</td>
                <td>{{ $p->user->name }}</td>
                <td>
                    @if ($p->isManagedBy(auth()->user()))
                        <a class="btn btn-light" href="{{ route('products.edit', $p) }}">Edit</a>
                        <form class="inline" method="POST" action="{{ route('products.destroy', $p) }}"
                              onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center">Belum ada produk</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection