@props([
    'title',
    'description',
    'initials',
])

<article {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-6 shadow-sm']) }}>
    <div class="flex items-center gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
            {{ $initials }}
        </div>
        <div>
            <h3 class="font-semibold text-slate-950">{{ $title }}</h3>
            <p class="text-xs uppercase tracking-wide text-slate-500">Verified workflow story</p>
        </div>
    </div>
    <p class="mt-5 text-sm leading-6 text-slate-600">{{ $description }}</p>
</article>
