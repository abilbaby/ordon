<x-app-layout>
    <x-slot name="title">Doctors</x-slot>

    <div class="card-pro">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-lg font-semibold">Doctors</h3>
                <p class="text-sm text-slate-600">Read-only view of hospital doctors and specializations.</p>
            </div>
            <form method="GET" class="grid grid-cols-1 gap-2 md:grid-cols-3">
                <select name="hospital_id" class="form-control">
                    <option value="">All Hospitals</option>
                    @foreach ($hospitals as $hospital)
                        <option value="{{ $hospital->id }}" @selected((string) $filters['hospital_id'] === (string) $hospital->id)>{{ $hospital->name }}</option>
                    @endforeach
                </select>
                <input name="specialization" value="{{ $filters['specialization'] }}" class="form-control" maxlength="100" placeholder="Specialization">
                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="p-3">Doctor</th>
                        <th class="p-3">Specialization</th>
                        <th class="p-3">Phone</th>
                        <th class="p-3">Hospital</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($doctors as $doctor)
                        <tr>
                            <td class="p-3 font-medium text-slate-900">{{ $doctor->name }}</td>
                            <td class="p-3">{{ $doctor->specialization }}</td>
                            <td class="p-3">{{ $doctor->phone ?? 'N/A' }}</td>
                            <td class="p-3">{{ $doctor->hospital->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-slate-500">No doctors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $doctors->links() }}</div>
    </div>
</x-app-layout>
