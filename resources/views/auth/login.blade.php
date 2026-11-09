@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card" style="max-width:420px;margin:60px auto;">
    <h2 style="margin-top:0">Masuk</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label>Password</label>
        <input type="password" name="password" required>

        <label style="font-weight:400">
            <input type="checkbox" name="remember" style="width:auto"> Ingat saya
        </label>

        <br>
        <button class="btn" style="width:100%">Login</button>
    </form>

    <p style="text-align:center;margin-bottom:0">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </p>
</div>
@endsection