@props([
    'title' => 'Default Title',
    'content' => 'Default description.',
])


<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
                {{ $title }}
            </h1>
            <p class="text-gray-600">{{ $content }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                {{ Breadcrumbs::render() }}
            </div>
        </div>
    </div>
</div>
