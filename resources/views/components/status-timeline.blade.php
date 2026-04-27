@php
    $steps = ['REGISTERED', 'VERIFIED', 'MATCHED', 'APPROVED', 'COMPLETED'];
    $currentIndex = array_search(strtoupper($currentStatus ?? 'REGISTERED'), $steps, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<div class="card-pro mb-6">
    <h3 class="text-lg font-semibold mb-3">{{ $title ?? 'Workflow Timeline' }}</h3>
    <div class="flex flex-wrap gap-2">
        @foreach ($steps as $index => $step)
            <span class="px-3 py-1 rounded-full text-xs {{ $index <= $currentIndex ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $step }}
            </span>
        @endforeach
    </div>
</div>
