@extends('client.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Translation Test: Cards</h1>
        <div class="bg-primary/10 text-primary px-4 py-2 rounded-lg font-bold uppercase">
            Current Language: {{ app()->getLocale() }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($cards as $card)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6">
                <div class="mb-4 border-b pb-2">
                    <span class="text-xs font-bold text-gray-400 uppercase">Database (English)</span>
                    <h3 class="text-lg font-semibold text-gray-600">{{ $card->title }}</h3>
                </div>

                <div>
                    <span class="text-xs font-bold text-primary uppercase">Translated ({{ app()->getLocale() }})</span>
                    {{-- Using the Accessors created in Step 8 --}}
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $card->title_translated }}</h3>
                    <p class="text-gray-600 mb-4">{{ $card->description_translated }}</p>

                    <a href="{{ $card->link }}" class="text-primary font-medium hover:underline">
                        {{ $card->link_text_translated }} &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
