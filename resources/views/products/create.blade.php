@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="card">
    <h2 style="margin-top:0">Tambah Produk</h2>
    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('products._form')
    </form>
</div>
@endsection