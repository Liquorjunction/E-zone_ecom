@props([
    'title' => 'Default Title',
    'content' => 'Default description.',
])

<div class="mb-0">
    <nav class="flex text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            {{ Breadcrumbs::render() }}
        </ol>
    </nav>
    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">
            {{ $title }}
        </h1>
    </div>
    <p class="text-gray-600 mt-2">{{ $content }}</p>
</div>
