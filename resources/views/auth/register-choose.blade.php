@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="card" style="max-width:480px;margin:60px auto;">
    <h2 style="margin-top:0">Daftar sebagai apa?</h2>

    <p><a class="btn" style="width:100%;text-align:center" href="{{ route('register.form', 'customer') }}">Customer (pembeli)</a></p>
    <p><a class="btn" style="width:100%;text-align:center" href="{{ route('register.form', 'seller') }}">Seller (penjual)</a></p>
    <p><a class="btn btn-light" style="width:100%;text-align:center" href="{{ route('register.form', 'admin') }}">Admin (butuh kode admin)</a></p>

    <p style="text-align:center;margin-bottom:0">
        Sudah punya akun? <a href="{{ route('login') }}">Login</a>
    </p>
</div>
@endsection