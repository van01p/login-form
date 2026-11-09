@extends('layouts.app')

@section('title', 'Belanja')

@section('content')
<h2 style="margin-top:0">Belanja</h2>

@forelse ($bySeller as $group)
    <h3 style="margin-top:28px;margin-bottom:0">Toko {{ $group->first()->user->name }}</h3>
    @include('shop._grid', ['products' => $group])
@empty
    <div class="card">Belum ada produk yang dijual.</div>
@endforelse
@endsection