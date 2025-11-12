@extends('client.layouts.app')

@section('content')
    <div class="bg-linear-to-br from-gray-50 to-white min-h-screen py-8">
        <div class="container mx-auto px-4">
            <x-breadcrumbs>
                <x-slot:title>Deals</x-slot:title>
                <x-slot:content>Discover the best deals on our platform.</x-slot:content>
            </x-breadcrumbs>
        </div>
    </div>
@endsection
