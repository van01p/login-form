@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<h2 style="margin-top:0">Dashboard Admin</h2>

<h3 style="margin-bottom:0">Seller</h3>
<div class="grid">
    @forelse ($sellers as $s)
        <a class="seller-card" href="{{ route('dashboard.seller', $s) }}">
            <strong>{{ $s->name }}</strong>
            <span class="muted">{{ $s->email }}</span>
            <div>Terjual: {{ number_format($s->total_units ?? 0, 0, ',', '.') }} barang</div>
            <div>Pendapatan: Rp{{ number_format($s->total_revenue ?? 0, 0, ',', '.') }}</div>
            <span class="btn btn-light" style="text-align:center">Lihat detail penjualan</span>
        </a>
    @empty
        <p>Belum ada seller.</p>
    @endforelse
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-top:36px">
    <h3 style="margin:0">Barang yang dijual</h3>
    <a class="btn" href="{{ route('products.create') }}">+ Tambah Barang</a>
</div>

@include('shop._grid', ['products' => $products])
@endsection