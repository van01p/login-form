@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="card">
    <h2 style="margin-top:0">Edit Produk</h2>
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('products._form')
    </form>
</div>
@endsection