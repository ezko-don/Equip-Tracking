@props(['class' => 'h-10'])

<img src="{{ asset('images/strathmore-logo.png') }}" alt="Strathmore University" {{ $attributes->merge(['class' => $class]) }}> 