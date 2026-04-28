@props([
    'label',
    'value',
    'detail' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-6 shadow-sm']) }}>
    <p class="text-3xl font-bold tracking-tight text-slate-950">{{ $value }}</p>
    <p class="mt-2 text-sm font-semibold text-slate-700">{{ $label }}</p>
    @if ($detail)
        <p class="mt-1 text-sm text-slate-500">{{ $detail }}</p>
    @endif
</div>
