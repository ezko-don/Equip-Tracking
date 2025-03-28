@props(['equipment', 'size' => 'md'])

@php
$sizes = [
    'sm' => 'h-12 w-12',
    'md' => 'h-20 w-20',
    'lg' => 'h-32 w-32',
    'xl' => 'h-48 w-48'
];
$containerClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex-shrink-0 {{ $containerClass }}">
    @if($equipment->image)
        <img class="{{ $containerClass }} rounded-lg object-cover" 
             src="{{ Storage::url($equipment->image) }}" 
             alt="{{ $equipment->name }}">
    @else
        <div class="{{ $containerClass }} rounded-lg bg-gray-200 flex items-center justify-center">
            <svg class="h-1/2 w-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    @endif
</div> 