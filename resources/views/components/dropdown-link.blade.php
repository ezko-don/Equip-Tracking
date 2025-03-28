@props(['href' => '#', 'as' => 'a'])

@php
    $baseClasses = 'block w-full text-left text-sm leading-5 transition duration-150 ease-in-out';
    
    if ($as === 'button') {
        $element = 'button';
        $attrs = $attributes->merge([
            'type' => 'submit',
            'class' => $baseClasses
        ]);
    } else {
        $element = 'a';
        $attrs = $attributes->merge([
            'href' => $href,
            'class' => $baseClasses
        ]);
    }
@endphp

<{{ $element }} {{ $attrs }}>
    {{ $slot }}
</{{ $element }}> 