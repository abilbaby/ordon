<x-app-layout>
    <x-slot name="title">Notifications</x-slot>

    <div class="card-pro">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h3 class="text-lg font-semibold">Notification Center</h3>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm">Mark all as read</button>
            </form>
        </div>

        <form method="GET" class="flex flex-wrap gap-2 mb-4">
            <select name="type" class="rounded-xl border-slate-200 text-sm">
                <option value="">All types</option>
                @foreach (['match','approval','invite','rejected','completed','reminder','surgery'] as $type)
                    <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-xl border-slate-200 text-sm">
                <option value="">All</option>
                <option value="unread" @selected(($filters['status'] ?? '') === 'unread')>Unread</option>
                <option value="read" @selected(($filters['status'] ?? '') === 'read')>Read</option>
            </select>
            <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm">Filter</button>
            <div class="rounded-xl bg-blue-50 text-blue-700 px-3 py-2 text-sm">Unread: {{ $unreadCount }}</div>
        </form>

        <div class="space-y-3">
            @forelse ($notifications as $notification)
                <div class="rounded-xl border p-4 {{ $notification->is_read ? 'border-slate-200 bg-white' : 'border-blue-200 bg-blue-50' }}">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="text-xs uppercase text-slate-500 mt-1">{{ $notification->type }}</p>
                        </div>
                        <span class="text-xs text-slate-500">{{ $notification->created_at?->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-slate-700 mt-2">{{ $notification->message }}</p>
                    @if (! $notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="mt-3">
                            @csrf
                            <button class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-700">Mark as read</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
                    No notifications yet.
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    </div>
</x-app-layout>
