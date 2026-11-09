@extends('layouts.app')

@section('title', 'Dashboard Seller')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <h2 style="margin:0">Dashboard Toko {{ auth()->user()->name }}</h2>
    <a class="btn" href="{{ route('products.create') }}">+ Tambah Barang</a>
</div>

@include('dashboard._seller_stats')
@endsection