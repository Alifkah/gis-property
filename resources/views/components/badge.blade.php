@props([
    'variant' => 'default',
])

@php
    $classes = match ($variant) {
        'new' => 'bg-indigo-600 text-white',
        'safe' => 'bg-emerald-500 text-white',
        default => 'bg-slate-900 text-white',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold shadow-sm {$classes}") }}>
    {{ $slot }}
</span>

