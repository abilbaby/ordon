@props([
    'index',
    'question',
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm']) }}>
    <button
        type="button"
        @click="open = open === {{ $index }} ? 0 : {{ $index }}"
        class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
        :aria-expanded="open === {{ $index }}"
    >
        <span class="font-semibold text-slate-950">{{ $question }}</span>
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-lg font-semibold text-slate-600" x-text="open === {{ $index }} ? '-' : '+'"></span>
    </button>
    <div x-show="open === {{ $index }}" x-transition>
        <div class="px-6 pb-5 text-sm leading-6 text-slate-600">
            {{ $slot }}
        </div>
    </div>
</div>
