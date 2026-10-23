@props(['product'])
@php
    $tones = [
        'amber' => 'bg-amber-100 text-amber-900',
        'sky' => 'bg-sky-100 text-sky-900',
        'rose' => 'bg-rose-100 text-rose-900',
        'emerald' => 'bg-emerald-100 text-emerald-900',
        'violet' => 'bg-violet-100 text-violet-900',
        'stone' => 'bg-stone-200 text-stone-800',
    ];
@endphp
<div {{ $attributes->class(['flex items-end p-5', $tones[$product->tone] ?? $tones['stone']]) }}>
    <span class="text-2xl font-semibold leading-tight tracking-tight opacity-80">{{ $product->name }}</span>
</div>
