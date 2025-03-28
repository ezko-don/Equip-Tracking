@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-strathmore-red focus:ring-strathmore-red rounded-md shadow-sm']) !!}> 