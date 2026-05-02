<x-app-layout>
    <x-slot name="title">Edit Doctor</x-slot>

    <div class="card-pro">
        <h3 class="mb-4 text-lg font-semibold">Edit Doctor</h3>
        <form method="POST" action="{{ route('hospital.doctors.update', $doctor) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-sm text-slate-600">Doctor Name</label>
                <input name="name" value="{{ old('name', $doctor->name) }}" class="mt-1 form-control @error('name') form-control-invalid @enderror" minlength="2" maxlength="100" pattern="[A-Za-z ]+" required>
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm text-slate-600">Specialization</label>
                <input name="specialization" value="{{ old('specialization', $doctor->specialization) }}" class="mt-1 form-control @error('specialization') form-control-invalid @enderror" minlength="2" maxlength="100" required>
                @error('specialization') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm text-slate-600">Phone</label>
                <input type="tel" name="phone" value="{{ old('phone', $doctor->phone) }}" class="mt-1 form-control @error('phone') form-control-invalid @enderror" maxlength="15" pattern="[0-9]{10,15}" inputmode="numeric" placeholder="10-15 digits">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-end gap-3">
                <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Save Doctor</button>
                <a href="{{ route('hospital.dashboard') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
