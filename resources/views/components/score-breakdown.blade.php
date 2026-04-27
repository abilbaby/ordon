<div class="mt-2 p-3 bg-gradient-to-r from-slate-50 to-blue-50 rounded-xl border border-slate-200 text-xs space-y-1">
    <div class="font-semibold text-slate-800 text-sm mb-1">Score Breakdown</div>
    
    @if(isset($breakdown) && is_array($breakdown))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-slate-600">
            <div><span class="font-medium">Urgency:</span> <span class="text-emerald-600 font-semibold">{{ $breakdown['urgency'] ?? 0 }}</span></div>
            <div><span class="font-medium">Waiting:</span> <span class="text-blue-600 font-semibold">{{ $breakdown['waiting'] ?? 0 }}</span></div>
            <div><span class="font-medium">Compat:</span> <span class="text-indigo-600 font-semibold">{{ $breakdown['compatibility'] ?? 0 }}</span></div>
            <div><span class="font-medium">Region:</span> <span class="text-purple-600 font-semibold">{{ $breakdown['distance'] ?? 0 }}</span></div>
            
            @if(isset($breakdown['time_constraint']))
                <div class="col-span-2 md:col-span-1"><span class="font-medium">Time:</span> {{ $breakdown['time_constraint'] }}</div>
            @endif
            @if(isset($breakdown['critical_boost']) && $breakdown['critical_boost'] > 0)
                <div class="col-span-2 md:col-span-1 bg-amber-100 text-amber-800 px-2 py-1 rounded-lg font-semibold">Critical +{{ $breakdown['critical_boost'] }}</div>
            @endif
            @if(isset($breakdown['emergency_boost']) && $breakdown['emergency_boost'] > 0)
                <div class="col-span-2 md:col-span-1 bg-red-100 text-red-800 px-2 py-1 rounded-lg font-semibold">Emergency +{{ $breakdown['emergency_boost'] }}</div>
            @endif
        </div>
        <div class="text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-200">
            Total: <span class="font-bold text-slate-900">{{ $breakdown['total'] ?? 'N/A' }}</span> 
            | Priority: <span class="font-bold text-blue-600">{{ $priority ?? 'N/A' }}</span>
        </div>
    @else
        <div class="text-slate-500 italic">Breakdown not available</div>
    @endif
</div>

