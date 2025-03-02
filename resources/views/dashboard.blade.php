@extends('layouts.layout')
@section('title', 'Dashboard')

@section('content')
    {{-- @if (auth()->check())
        <!-- Konten untuk pengguna yang sudah login -->
        <h2>Welcome back, {{$username }}!</h2>
    @else
        <!-- Konten untuk pengguna yang belum login -->
        <h2>Silakan login terlebih dahulu.</h2>
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
    @endif --}}

@endsection
