@extends('admin.layouts.app')

@section('title', 'Showroom Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ $showroom->getTranslation('name', 'en') }}</h4>
                    </div>
                    <a href="{{ route('admin.showrooms.index') }}" class="btn btn-primary btn-sm">Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ asset('storage/'.$showroom->cover_image) }}" class="img-fluid rounded mb-3" alt="Cover">
                            <h5>Contact Info</h5>
                            <p><strong>Email:</strong> {{ $showroom->email }}</p>
                            <p><strong>Phone:</strong> {{ $showroom->phone }}</p>
                            <p><strong>Address:</strong><br>
                                {{ $showroom->getTranslation('address', 'en') }},<br>
                                {{ $showroom->getTranslation('city', 'en') }}, {{ $showroom->getTranslation('state', 'en') }}<br>
                                {{ $showroom->getTranslation('country', 'en') }} - {{ $showroom->pincode }}
                            </p>
                        </div>
                        <div class="col-md-8">
                            @if($showroom->getTranslation('description', 'en'))
                                <h5>Description</h5>
                                <p>{!! $showroom->getTranslation('description', 'en') !!}</p>
                            @endif

                            <h5 class="mt-4">Opening Hours</h5>
                            @if($showroom->opening_hours && is_array($showroom->opening_hours))
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($showroom->opening_hours as $day => $times)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="text-capitalize">{{ $day }}</span>
                                    <span>{{ is_array($times) ? ($times['open'] ?? 'N/A') . ' - ' . ($times['close'] ?? 'N/A') : $times }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif

                            @if($showroom->map_iframe)
                            <div class="mt-3">
                                <h5>Location Map</h5>
                                <div class="ratio ratio-16x9">
                                    {!! $showroom->map_iframe !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Gallery</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($showroom->images as $img)
                                    <img src="{{ asset('storage/'.$img->image) }}" class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
