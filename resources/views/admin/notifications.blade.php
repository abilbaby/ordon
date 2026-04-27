<x-app-layout>
    <x-slot name="title">Notifications</x-slot>

    <div class="card-pro">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">System Notifications</h3>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to dashboard</a>
        </div>

        <div class="space-y-3">
            @forelse ($notifications as $notification)
                <div class="rounded-xl border border-slate-200 p-4 {{ $notification->is_read ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mt-1">{{ $notification->type }}</p>
                        </div>
                        <span class="text-xs text-slate-500">{{ $notification->created_at?->format('d M Y h:i A') }}</span>
                    </div>
                    <p class="text-sm text-slate-700 mt-2">{{ $notification->message }}</p>
                    <div class="text-xs text-slate-500 mt-2">
                        Status: {{ $notification->is_read ? 'Read' : 'Unread' }}
                        @if ($notification->user)
                            | User: {{ $notification->user->name }}
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-slate-600">No notifications available.</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>
</x-app-layout>
