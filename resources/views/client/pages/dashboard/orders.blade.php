@extends('client.pages.dashboard-layout')

@section('dashboard-title', statictranslations('My Orders'))

@section('dashboard-content')
  <div class="rounded-xl lg:rounded-2xl">
    <div id="orders-list-container">
      @include('client.pages.dashboard.partials.orders-list', ['orders' => $orders, 'currentStatus' => $currentStatus])
    </div>
  </div>
@endsection
