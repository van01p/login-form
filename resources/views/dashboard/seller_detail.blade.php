@extends('layouts.app')

@section('title', 'Detail ' . $seller->name)

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <h2 style="margin:0">Detail Penjualan {{ $seller->name }}</h2>
    <a class="btn btn-light" href="{{ route('dashboard') }}">&larr; Kembali ke Dashboard</a>
</div>

@include('dashboard._seller_stats')
@endsection
