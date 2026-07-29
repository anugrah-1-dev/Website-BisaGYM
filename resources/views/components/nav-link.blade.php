@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md bg-neon text-darker shadow-[0_0_15px_rgba(224,255,0,0.3)] transition-all'
            : 'flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md text-gray-400 hover:bg-gray-800 hover:text-white transition-colors duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
