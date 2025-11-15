@extends('admin.layouts.app')

@section('header-title', 'User Management')
@section('header-description', 'Manage user accounts and permissions')

@section('breadcrumbs')
    @foreach(Breadcrumbs::generate('admin.user-management.index') as $breadcrumb)
        @if($breadcrumb->url && !$loop->last)
            <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
        @endif
    @endforeach
@endsection

@section('content')
    <div class="conatiner-fluid content-inner mt-n5 py-0">
        <div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">User List</h4>
                            </div>
                        </div>
                        <div class="card-body px-0">
                            <div class="table-responsive">
                                <table id="user-list-table" class="table table-striped" role="grid"
                                    data-bs-toggle="data-table">
                                    <thead>
                                        <tr class="ligth">
                                            <th>Profile</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Country</th>
                                            <th>Status</th>
                                            <th>Join Date</th>
                                            <th style="min-width: 100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Single entry for design reference --}}
                                        <tr>
                                            <td class="text-center">
                                                {{-- Updated image path to use Laravel's asset() helper --}}
                                                <img class="bg-primary-subtle rounded img-fluid avatar-40 me-3"
                                                    src="{{ asset('admin_assets/images/shapes/01.png') }}" alt="profile">
                                            </td>
                                            <td>Anna Sthesia</td>
                                            <td>annasthesia@gmail.com</td>
                                            <td>USA</td>
                                            <td><span class="badge bg-primary">Active</span></td>
                                            <td>2019/12/01</td>
                                            <td>
                                                @include('admin.partials.actions')
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
