@props(['active', 'icon', 'href'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-2.5 rounded-lg bg-white/20 text-white font-medium shadow-sm'
            : 'flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition text-emerald-50 hover:text-white font-medium';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'href' => $href]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
