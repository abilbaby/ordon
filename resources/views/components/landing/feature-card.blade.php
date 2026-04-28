@props([
    'title',
    'description',
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-200 hover:shadow-md']) }}>
    <div class="mb-5 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700">
        {{ $icon }}
    </div>
    <h3 class="text-lg font-semibold text-slate-950">{{ $title }}</h3>
    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $description }}</p>
</div>
