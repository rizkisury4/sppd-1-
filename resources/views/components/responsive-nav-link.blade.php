@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block px-3 py-2 rounded-md bg-indigo-800 text-white'
    : 'block px-3 py-2 rounded-md text-indigo-100 hover:bg-indigo-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>