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
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>Join Date</th>
                                            <th style="min-width: 100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($admins ?? [] as $admin)
                                            <tr>
                                                <td class="text-center">
                                                    <img class="bg-primary-subtle rounded img-fluid avatar-40 me-3"
                                                        src="{{ $admin->profile_image ? asset('admin_assets/images/avatars/' . $admin->profile_image) : asset('admin_assets/images/avatars/01.png') }}" alt="profile">
                                                </td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>{{ $admin->username }}</td>
                                                <td>
                                                    <span class="badge {{ $admin->status == 'active' ? 'bg-primary' : 'bg-danger' }}">
                                                        {{ ucfirst($admin->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $admin->created_at->format('Y/m/d') }}</td>
                                                <td>
                                                    @include('admin.partials.actions', ['model' => $admin, 'routePrefix' => 'admin.user-management'])
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No admins found.</td>
                                            </tr>
                                        @endforelse
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
