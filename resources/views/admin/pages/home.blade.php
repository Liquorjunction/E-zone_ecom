@extends('admin.layouts.app')

@section('header-title', 'Dashboard')
@section('header-description', 'Welcome to your admin dashboard')

@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.dashboard') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="conatiner-fluid content-inner mt-n5 py-0">

    </div>
@endsection
