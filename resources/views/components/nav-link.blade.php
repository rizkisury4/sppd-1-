@props(['active'])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-800 text-white transition'
    : 'flex items-center gap-3 px-3 py-2 rounded-lg text-indigo-100 hover:bg-indigo-600/70 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>