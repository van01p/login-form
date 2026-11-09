@extends('layouts.app')

@section('title', 'Daftar ' . ucfirst($role))

@section('content')
<div class="card" style="max-width:420px;margin:40px auto;">
    <h2 style="margin-top:0">Daftar sebagai {{ ucfirst($role) }}</h2>

    <form method="POST" action="{{ route('register.store', $role) }}">
        @csrf

        <label>Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Ulangi Password</label>
        <input type="password" name="password_confirmation" required>

        @if ($role === 'admin')
            <label>Kode Admin</label>
            <input type="password" name="admin_code" required>
        @endif

        <br><br>
        <button class="btn" style="width:100%">Daftar</button>
    </form>

    <p style="text-align:center;margin-bottom:0">
        <a href="{{ route('register') }}">Ganti role</a> |
        <a href="{{ route('login') }}">Sudah punya akun?</a>
    </p>
</div>
@endsection