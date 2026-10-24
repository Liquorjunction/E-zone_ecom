@if (!empty($selected_category_id))
    @php
        $currentFilteredCat = \App\Models\Categories::find($selected_category_id);
    @endphp
    @if ($currentFilteredCat)
        <div class="mb-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-semibold">
            <span>{{ statictranslations('Filtered by:') }} {{ $currentFilteredCat->getTranslation('category-name', app()->getLocale(), false) }}</span>
            <a href="{{ route('brand.products', $brand->slug) }}#products-catalog" class="brand-filter-clear-btn hover:text-primary-dark font-bold ml-1 text-sm leading-none" title="{{ statictranslations('Clear filter') }}">&times;</a>
        </div>
    @endif
@endif

<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
        {{ $brand->name }} — {{ statictranslations('All Products') }}
    </h2>
    @if ($products && $products->total())
        <span class="text-sm font-medium text-gray-500">
            {{ $products->total() }} {{ statictranslations('products available') }}
        </span>
    @endif
</div>

@if ($products && $products->count())
    <div class="grid grid-cols-2 gap-3 sm:hidden">
        @foreach ($products as $product)
            @include('client.partials.product-card-mobile', ['product' => $product])
        @endforeach
    </div>
    <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach ($products as $product)
            @include('client.partials.product-card', ['product' => $product])
        @endforeach
    </div>
    <div class="mt-8 brand-pagination-wrapper">
        {{ $products->links() }}
    </div>
@else
    <div class="text-center py-16 text-gray-500 text-lg">
        {{ statictranslations('No products found for this brand.') }}
    </div>
@endif
