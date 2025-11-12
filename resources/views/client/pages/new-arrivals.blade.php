@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>New Arrivals</x-slot:title>
                <x-slot:content>Discover the latest additions to our collection.</x-slot:content>
            </x-breadcrumbs>
        </div>
    </div>
@endsection
