<x-app-layout>
    <x-slot name="title">Donor Certificate</x-slot>

    <div class="card-pro">
        <h3 class="text-xl font-semibold text-[#0b3650] mb-3">Official Organ Donation Completion Certificate</h3>
        <p class="text-slate-600 mb-6">
            This certificate is issued only after successful transplant completion.
        </p>
        <div class="rounded-2xl border-4 border-[#0b3650] bg-[#f8fcff] p-8 text-center">
            <img src="{{ asset('images/ordon-logo.png') }}" alt="ORDON logo" class="h-16 mx-auto mb-4 rounded-lg" />
            <p class="text-sm text-slate-500 uppercase tracking-widest">Certificate of Appreciation</p>
            <h4 class="text-3xl font-semibold mt-3 text-[#0b3650]">{{ auth()->user()->name }}</h4>
            <p class="mt-2 text-slate-700">for completing a successful organ donation</p>
            <p class="text-sm text-slate-700 mt-4">Organ: <strong>{{ ucfirst($donor->organ_type) }}</strong> | Blood Group: <strong>{{ $donor->blood_group }}</strong></p>
            <p class="text-sm text-slate-700">Hospital: <strong>{{ $completedTransplant->hospital->name ?? 'N/A' }}</strong></p>
            <p class="text-sm text-slate-700">Recipient: <strong>{{ $completedTransplant->recipient_name_override ?: ($completedTransplant->match->recipient->user->name ?? 'N/A') }}</strong></p>
            <p class="text-sm text-slate-700 mt-1">Certificate ID: <strong>{{ $certificateId }}</strong></p>
            <img class="h-24 w-24 mx-auto mt-4 border rounded-lg bg-white p-1"
                 src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ rawurlencode($certificateId) }}"
                 alt="Certificate QR">
            <p class="text-xs text-slate-500 mt-4">Completion Date: {{ $completedTransplant->updated_at?->format('d M Y') }}</p>
        </div>

        <div class="mt-6">
            <a href="{{ route('donor.certificate.download') }}"
               class="inline-block rounded-xl bg-[#0b6ea2] text-white px-5 py-2.5 hover:bg-[#0a5f8b] transition-all duration-200">
                Download Certificate (PDF)
            </a>
        </div>
    </div>
</x-app-layout>
