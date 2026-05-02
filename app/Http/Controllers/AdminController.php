<?php

namespace App\Http\Controllers;

use App\Models\AllocationMatch;
use App\Enums\OrganType;
use App\Models\Donor;
use App\Models\DonationHistory;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\IssueReport;
use App\Models\OrganInventory;
use App\Models\Recipient;
use App\Models\SystemSetting;
use App\Models\Transplant;
use App\Models\UserNotification;
use App\Services\AllocationEngine;
use App\Services\AuditLogger;
use App\Services\DonationHistoryService;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $status = $request->string('status')->toString();

        $recentMatchesQuery = AllocationMatch::with(['donor.user', 'recipient.user'])->latest();
        if ($status !== '') {
            $recentMatchesQuery->where('status', $status);
        }

        $recentMatches = $recentMatchesQuery->limit(10)->get();

        $recentTransplants = Transplant::with(['match.donor.user', 'match.recipient.user'])->latest()->limit(6)->get();
        $activities = collect()
            ->merge($recentMatches->map(function (AllocationMatch $match): array {
                return [
                    'time' => $match->created_at,
                    'label' => "Match {$match->status}: ".($match->donor->user->name ?? 'Donor')." -> ".($match->recipient->user->name ?? 'Recipient'),
                    'type' => $match->status === 'REJECTED' ? 'danger' : ($match->status === 'APPROVED' ? 'success' : 'warning'),
                ];
            }))
            ->merge($recentTransplants->map(function (Transplant $transplant): array {
                return [
                    'time' => $transplant->created_at,
                    'label' => "Transplant {$transplant->status} at ".($transplant->scheduled_at?->format('M d, H:i') ?? 'unscheduled'),
                    'type' => $transplant->status === 'COMPLETED' ? 'success' : 'info',
                ];
            }))
            ->sortByDesc('time')
            ->take(8)
            ->values();

        return view('admin.dashboard', [
            'totalDonors' => Donor::count(),
            'totalRecipients' => Recipient::count(),
            'activeMatches' => AllocationMatch::where('status', 'MATCHED')->count(),
            'pendingApprovals' => AllocationMatch::where('status', 'APPROVED')->count(),
            'flaggedDonors' => Donor::where('fraud_flag', true)->count(),
            'flaggedHospitals' => Hospital::where('fraud_flag', true)->count(),
            'blacklistCount' => Donor::where('blacklisted', true)->count() + Hospital::where('blacklisted', true)->count(),
            'pendingVerification' => [
                'donors' => Donor::where('identity_verified', false)->count(),
                'recipients' => Recipient::where('identity_verified', false)->count(),
                'hospitals' => Hospital::where('identity_verified', false)->count(),
            ],
            'workflowStats' => [
                'registered' => Recipient::where('status', 'REGISTERED')->count(),
                'verified' => Recipient::where('status', 'VERIFIED')->count(),
                'matched' => Recipient::where('status', 'MATCHED')->count(),
                'approved' => AllocationMatch::where('status', 'APPROVED')->count(),
                'completed' => Transplant::where('status', 'COMPLETED')->count(),
            ],
            'selectedStatus' => $status,
            'recentMatches' => $recentMatches,
            'recentTransplants' => $recentTransplants,
            'activities' => $activities,
            'recentAudits' => \App\Models\AuditLog::with('user')->latest()->limit(8)->get(),
            'notificationPreview' => UserNotification::where('user_id', $request->user()->id)->latest()->limit(5)->get(),
        ]);
    }

    public function donors(): View
    {
        $search = request()->string('search')->toString();
        $status = request()->string('status')->toString();
        $organType = request()->string('organ_type')->toString();
        $bloodGroup = request()->string('blood_group')->toString();
        $fraud = request()->string('fraud')->toString();
        $blacklisted = request()->string('blacklisted')->toString();
        $dateFrom = request()->string('date_from')->toString();
        $dateTo = request()->string('date_to')->toString();

        $query = Donor::with('user')->latest();
        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhere('blood_group', 'like', "%{$search}%")
                    ->orWhere('organ_type', 'like', "%{$search}%");
            });
        }
        if ($status !== '') {
            $query->where('medical_status', $status);
        }
        if ($organType !== '') {
            $query->where('organ_type', $organType);
        }
        if ($bloodGroup !== '') {
            $query->where('blood_group', $bloodGroup);
        }
        if ($fraud !== '') {
            $query->where('fraud_flag', $fraud === 'yes');
        }
        if ($blacklisted !== '') {
            $query->where('blacklisted', $blacklisted === 'yes');
        }
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return view('admin.donors', [
            'donors' => $query->paginate(10)->withQueryString(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'organ_type' => $organType,
                'blood_group' => $bloodGroup,
                'fraud' => $fraud,
                'blacklisted' => $blacklisted,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function recipients(): View
    {
        $search = request()->string('search')->toString();
        $status = request()->string('status')->toString();
        $urgency = request()->string('urgency')->toString();
        $organType = request()->string('organ_type')->toString();
        $bloodGroup = request()->string('blood_group')->toString();
        $hospitalId = request()->string('hospital_id')->toString();
        $dateFrom = request()->string('date_from')->toString();
        $dateTo = request()->string('date_to')->toString();

        $query = Recipient::with('user')->latest();
        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhere('blood_group', 'like', "%{$search}%")
                    ->orWhere('organ_needed', 'like', "%{$search}%");
            });
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($urgency !== '') {
            $query->where('urgency_level', $urgency);
        }
        if ($organType !== '') {
            $query->where('organ_needed', $organType);
        }
        if ($bloodGroup !== '') {
            $query->where('blood_group', $bloodGroup);
        }
        if ($hospitalId !== '') {
            $query->where('hospital_id', (int) $hospitalId);
        }
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return view('admin.recipients', [
            'recipients' => $query->paginate(10)->withQueryString(),
            'filters' => [
                'search' => $search,
                'status' => $status,
                'urgency' => $urgency,
                'organ_type' => $organType,
                'blood_group' => $bloodGroup,
                'hospital_id' => $hospitalId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'hospitals' => Hospital::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function flaggedRecipients(): View
    {
        return view('admin.flagged-recipients', [
            'recipients' => Recipient::with(['user', 'hospital', 'recipientVerification'])
                ->where('flagged_for_review', true)
                ->latest()
                ->paginate(12),
        ]);
    }

    public function matches(): View
    {
        $status = request()->string('status')->toString();
        $priority = request()->string('priority')->toString();
        $organType = request()->string('organ_type')->toString();
        $bloodGroup = request()->string('blood_group')->toString();
        $urgency = request()->string('urgency')->toString();
        $hospitalId = request()->string('hospital_id')->toString();
        $dateFrom = request()->string('date_from')->toString();
        $dateTo = request()->string('date_to')->toString();

        $query = AllocationMatch::with(['donor.user', 'recipient.user', 'transplant'])->latest();
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($organType !== '') {
            $query->whereHas('recipient', fn ($recipientQuery) => $recipientQuery->where('organ_needed', $organType));
        }
        if ($urgency !== '') {
            $query->whereHas('recipient', fn ($recipientQuery) => $recipientQuery->where('urgency_level', $urgency));
        }
        if ($bloodGroup !== '') {
            $query->where(function ($matchQuery) use ($bloodGroup): void {
                $matchQuery
                    ->whereHas('donor', fn ($donorQuery) => $donorQuery->where('blood_group', $bloodGroup))
                    ->orWhereHas('recipient', fn ($recipientQuery) => $recipientQuery->where('blood_group', $bloodGroup));
            });
        }
        if ($hospitalId !== '') {
            $query->whereHas('transplant', fn ($transplantQuery) => $transplantQuery->where('hospital_id', (int) $hospitalId));
        }
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $matches = $query->paginate(12)->withQueryString();
        if ($priority !== '') {
            $matches->setCollection(
                $matches->getCollection()->filter(fn (AllocationMatch $match) => $match->priority_level === $priority)->values()
            );
        }

        return view('admin.matches', [
            'matches' => $matches,
            'filters' => [
                'status' => $status,
                'priority' => $priority,
                'organ_type' => $organType,
                'blood_group' => $bloodGroup,
                'urgency' => $urgency,
                'hospital_id' => $hospitalId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'hospitals' => Hospital::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function notifications(): View
    {
        $notifications = UserNotification::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.notifications', ['notifications' => $notifications]);
    }

    public function hospitals(): View
    {
        $search = request()->string('search')->toString();
        $approved = request()->string('approved')->toString();
        $fraud = request()->string('fraud')->toString();
        $blacklisted = request()->string('blacklisted')->toString();

        $query = Hospital::with('user')->latest();
        if ($search !== '') {
            $query->where(fn ($searchQuery) => $searchQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%"));
        }
        if ($approved !== '') {
            $query->where('approved', $approved === 'yes');
        }
        if ($fraud !== '') {
            $query->where('fraud_flag', $fraud === 'yes');
        }
        if ($blacklisted !== '') {
            $query->where('blacklisted', $blacklisted === 'yes');
        }

        return view('admin.hospitals', [
            'hospitals' => $query->paginate(10)->withQueryString(),
            'filters' => ['search' => $search, 'approved' => $approved, 'fraud' => $fraud, 'blacklisted' => $blacklisted],
        ]);
    }

    public function doctors(): View
    {
        $hospitalId = request()->string('hospital_id')->toString();
        $specialization = request()->string('specialization')->toString();

        $query = Doctor::with('hospital')->latest();
        if ($hospitalId !== '') {
            $query->where('hospital_id', (int) $hospitalId);
        }
        if ($specialization !== '') {
            $query->where('specialization', 'like', "%{$specialization}%");
        }

        return view('admin.doctors', [
            'doctors' => $query->paginate(12)->withQueryString(),
            'hospitals' => Hospital::orderBy('name')->get(['id', 'name']),
            'filters' => ['hospital_id' => $hospitalId, 'specialization' => $specialization],
        ]);
    }

    public function organs(): View
    {
        $hospitalId = request()->string('hospital_id')->toString();
        $organType = request()->string('organ_type')->toString();

        $query = OrganInventory::with('hospital')->latest();
        if ($hospitalId !== '') {
            $query->where('hospital_id', (int) $hospitalId);
        }
        if ($organType !== '') {
            $query->where('organ_type', $organType);
        }

        return view('admin.organs', [
            'inventory' => $query->paginate(12)->withQueryString(),
            'hospitals' => Hospital::orderBy('name')->get(['id', 'name']),
            'organTypes' => OrganType::values(),
            'filters' => ['hospital_id' => $hospitalId, 'organ_type' => $organType],
        ]);
    }

    public function reports(): View
    {
        $queueByOrgan = Recipient::query()
            ->selectRaw('organ_needed, COUNT(*) as total')
            ->groupBy('organ_needed')
            ->orderByDesc('total')
            ->get();
        $queueByBlood = Recipient::query()
            ->selectRaw('blood_group, COUNT(*) as total')
            ->groupBy('blood_group')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports', [
            'totalTransplants' => Transplant::count(),
            'completedTransplants' => Transplant::where('status', 'COMPLETED')->count(),
            'approvalRate' => AllocationMatch::count() > 0
                ? round((AllocationMatch::where('status', 'APPROVED')->count() / AllocationMatch::count()) * 100, 2)
                : 0,
            'queueByOrgan' => $queueByOrgan,
            'queueByBlood' => $queueByBlood,
            'donationsByRegion' => Donor::query()
                ->selectRaw('COALESCE(region, "Unknown") as region, COUNT(*) as total')
                ->groupBy('region')
                ->orderByDesc('total')
                ->get(),
            'successRate' => Transplant::count() > 0
                ? round((Transplant::where('status', 'COMPLETED')->count() / Transplant::count()) * 100, 2)
                : 0,
            'auditLogs' => \App\Models\AuditLog::with('user')->latest()->limit(20)->get(),
            'issueReports' => IssueReport::with('user')->latest()->limit(20)->get(),
            'openIssueReports' => IssueReport::where('status', 'open')->count(),
            'resolvedIssueReports' => IssueReport::where('status', 'resolved')->count(),
            'donationLogs' => DonationHistory::with(['donor.user', 'recipient.user', 'hospital'])
                ->latest('donation_date')
                ->limit(20)
                ->get(),
        ]);
    }

    public function donationLogs(DonationHistoryService $donationHistoryService): View
    {
        return view('admin.donation-logs', [
            'donationLogs' => $donationHistoryService->getAllDonations(),
        ]);
    }

    public function blacklistRegistry(): View
    {
        return view('admin.blacklist-registry', [
            'donors' => Donor::with('user')->where('blacklisted', true)->latest()->get(),
            'hospitals' => Hospital::with('user')->where('blacklisted', true)->latest()->get(),
        ]);
    }

    public function runAutoMatching(AllocationEngine $allocationEngine): RedirectResponse
    {
        $eligibleDonors = Donor::where('is_available', true)
            ->where('medical_status', 'VERIFIED')
            ->where('identity_verified', true)
            ->get();

        $created = 0;
        foreach ($eligibleDonors as $donor) {
            $match = $allocationEngine->findBestMatch($donor);
            if ($match) {
                $created++;
            }
        }

        return back()->with('success', "Auto-matching completed. {$created} new match(es) created.");
    }

    public function exportReportsCsv(): StreamedResponse
    {
        $fileName = 'ordon-reports-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Section', 'Metric', 'Value']);
            fputcsv($handle, ['Summary', 'Total Transplants', Transplant::count()]);
            fputcsv($handle, ['Summary', 'Completed Transplants', Transplant::where('status', 'COMPLETED')->count()]);
            $approvalRate = AllocationMatch::count() > 0
                ? round((AllocationMatch::where('status', 'APPROVED')->count() / AllocationMatch::count()) * 100, 2)
                : 0;
            fputcsv($handle, ['Summary', 'Approval Rate (%)', $approvalRate]);
            fputcsv($handle, ['Summary', 'Open Issue Reports', IssueReport::where('status', 'open')->count()]);
            fputcsv($handle, ['Summary', 'Resolved Issue Reports', IssueReport::where('status', 'resolved')->count()]);

            fputcsv($handle, []);
            fputcsv($handle, ['Queue by Organ']);
            fputcsv($handle, ['Organ Type', 'Count']);
            Recipient::query()
                ->selectRaw('organ_needed, COUNT(*) as total')
                ->groupBy('organ_needed')
                ->orderByDesc('total')
                ->get()
                ->each(fn ($row) => fputcsv($handle, [$row->organ_needed, $row->total]));

            fputcsv($handle, []);
            fputcsv($handle, ['Recent Issue Reports']);
            fputcsv($handle, ['User', 'Role', 'Scope', 'Subject', 'Message', 'Status', 'Created At']);
            IssueReport::with('user')->latest()->limit(20)->get()->each(function (IssueReport $report) use ($handle): void {
                fputcsv($handle, [
                    $report->user->name ?? 'User',
                    $report->role,
                    $report->scope,
                    $report->subject,
                    $report->message,
                    $report->status,
                    $report->created_at?->toDateTimeString(),
                ]);
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    public function exportDonorsCsv(Request $request): StreamedResponse
    {
        $query = Donor::with('user');
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhere('blood_group', 'like', "%{$search}%")
                    ->orWhere('organ_type', 'like', "%{$search}%");
            });
        }
        foreach (['status' => 'medical_status', 'organ_type' => 'organ_type', 'blood_group' => 'blood_group'] as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->string($param)->toString());
            }
        }

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Blood Group', 'Organ', 'Status', 'Created At']);
            $query->latest()->get()->each(function (Donor $donor) use ($handle): void {
                fputcsv($handle, [
                    $donor->user->name ?? 'N/A',
                    $donor->blood_group,
                    $donor->organ_type,
                    $donor->medical_status,
                    $donor->created_at?->toDateTimeString(),
                ]);
            });
            fclose($handle);
        }, 'donors-export-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportRecipientsCsv(Request $request): StreamedResponse
    {
        $query = Recipient::with(['user', 'hospital']);
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($searchQuery) use ($search): void {
                $searchQuery->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhere('blood_group', 'like', "%{$search}%")
                    ->orWhere('organ_needed', 'like', "%{$search}%");
            });
        }
        foreach (['status' => 'status', 'urgency' => 'urgency_level', 'organ_type' => 'organ_needed', 'blood_group' => 'blood_group'] as $param => $column) {
            if ($request->filled($param)) {
                $query->where($column, $request->string($param)->toString());
            }
        }
        if ($request->filled('hospital_id')) {
            $query->where('hospital_id', (int) $request->string('hospital_id')->toString());
        }

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Hospital', 'Blood Group', 'Organ Needed', 'Urgency', 'Status']);
            $query->latest()->get()->each(function (Recipient $recipient) use ($handle): void {
                fputcsv($handle, [
                    $recipient->user->name ?? 'N/A',
                    $recipient->hospital->name ?? 'N/A',
                    $recipient->blood_group,
                    $recipient->organ_needed,
                    $recipient->urgency_level,
                    $recipient->status,
                ]);
            });
            fclose($handle);
        }, 'recipients-export-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportMatchesCsv(Request $request): StreamedResponse
    {
        $query = AllocationMatch::with(['donor.user', 'recipient.user', 'transplant.hospital']);
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Donor', 'Recipient', 'Hospital', 'Match Score', 'Status', 'Reason']);
            $query->latest()->get()->each(function (AllocationMatch $match) use ($handle): void {
                fputcsv($handle, [
                    $match->donor->user->name ?? 'N/A',
                    $match->recipient->user->name ?? 'N/A',
                    $match->transplant->hospital->name ?? 'N/A',
                    $match->match_score ?? $match->score,
                    $match->status,
                    $match->reason,
                ]);
            });
            fclose($handle);
        }, 'matches-export-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportHospitalsCsv(Request $request): StreamedResponse
    {
        $query = Hospital::query();
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(fn ($searchQuery) => $searchQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%"));
        }
        if ($request->filled('approved')) {
            $query->where('approved', $request->string('approved')->toString() === 'yes');
        }

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Hospital', 'Location', 'Approved', 'Identity Verified']);
            $query->latest()->get()->each(function (Hospital $hospital) use ($handle): void {
                fputcsv($handle, [
                    $hospital->name,
                    $hospital->location,
                    $hospital->approved ? 'Yes' : 'No',
                    $hospital->identity_verified ? 'Yes' : 'No',
                ]);
            });
            fclose($handle);
        }, 'hospitals-export-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportReportsPdf(): \Symfony\Component\HttpFoundation\Response
    {
        $data = [
            'totalTransplants' => Transplant::count(),
            'completedTransplants' => Transplant::where('status', 'COMPLETED')->count(),
            'approvalRate' => AllocationMatch::count() > 0
                ? round((AllocationMatch::where('status', 'APPROVED')->count() / AllocationMatch::count()) * 100, 2)
                : 0,
            'openIssueReports' => IssueReport::where('status', 'open')->count(),
            'resolvedIssueReports' => IssueReport::where('status', 'resolved')->count(),
            'queueByOrgan' => Recipient::query()
                ->selectRaw('organ_needed, COUNT(*) as total')
                ->groupBy('organ_needed')
                ->orderByDesc('total')
                ->get(),
            'issueReports' => IssueReport::with('user')->latest()->limit(20)->get(),
        ];

        return Pdf::loadView('admin.reports-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download('ordon-reports-'.now()->format('Ymd-His').'.pdf');
    }

    public function approveDonor(Request $request, Donor $donor, AuditLogger $auditLogger): RedirectResponse
    {
        $donor->update(['approved' => true, 'medical_status' => 'VERIFIED']);
        $auditLogger->log($request->user(), 'admin', 'approve_donor', "Donor {$donor->id} approved");

        return back()->with('success', 'Donor approved and verified.');
    }

    public function rejectDonor(Request $request, Donor $donor, AuditLogger $auditLogger): RedirectResponse
    {
        $donor->update(['approved' => false, 'medical_status' => 'REJECTED']);
        $auditLogger->log($request->user(), 'admin', 'reject_donor', "Donor {$donor->id} rejected");

        return back()->with('success', 'Donor rejected.');
    }

    public function flagDonorFraud(Request $request, Donor $donor, AuditLogger $auditLogger): RedirectResponse
    {
        $donor->update(['fraud_flag' => true]);
        $auditLogger->log($request->user(), 'admin', 'flag_donor_fraud', "Donor {$donor->id} flagged");

        return back()->with('success', 'Donor flagged for fraud review.');
    }

    public function blacklistDonor(Request $request, Donor $donor, AuditLogger $auditLogger): RedirectResponse
    {
        $donor->update(['blacklisted' => true, 'is_available' => false]);
        $auditLogger->log($request->user(), 'admin', 'blacklist_donor', "Donor {$donor->id} blacklisted");

        return back()->with('success', 'Donor blacklisted.');
    }

    public function approveHospital(Request $request, Hospital $hospital, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital->update(['approved' => true]);
        $auditLogger->log($request->user(), 'admin', 'approve_hospital', "Hospital {$hospital->id} approved");

        return back()->with('success', 'Hospital approved.');
    }

    public function rejectHospital(Request $request, Hospital $hospital, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital->update(['approved' => false]);
        $auditLogger->log($request->user(), 'admin', 'reject_hospital', "Hospital {$hospital->id} rejected");

        return back()->with('success', 'Hospital rejected.');
    }

    public function flagHospitalFraud(Request $request, Hospital $hospital, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital->update(['fraud_flag' => true]);
        $auditLogger->log($request->user(), 'admin', 'flag_hospital_fraud', "Hospital {$hospital->id} flagged");

        return back()->with('success', 'Hospital flagged for fraud review.');
    }

    public function blacklistHospital(Request $request, Hospital $hospital, AuditLogger $auditLogger): RedirectResponse
    {
        $hospital->update(['blacklisted' => true, 'approved' => false]);
        $auditLogger->log($request->user(), 'admin', 'blacklist_hospital', "Hospital {$hospital->id} blacklisted");

        return back()->with('success', 'Hospital blacklisted.');
    }

    public function overrideMatch(
        Request $request,
        AllocationMatch $match,
        AuditLogger $auditLogger,
        NotificationService $notificationService
    ): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:MATCHED,APPROVED,REJECTED,COMPLETED'],
            'override_reason' => ['required', 'string', 'max:1000'],
        ]);

        $match->update([
            'status' => $validated['status'],
            'admin_override' => true,
            'override_reason' => $validated['override_reason'],
        ]);

        $auditLogger->log($request->user(), 'admin', 'override_match', "Match {$match->id}: {$validated['status']}");
        if ($match->donor?->user) {
            $notificationService->notify(
                $match->donor->user,
                'approval',
                'Match status updated by admin',
                "Match #{$match->id} status changed to {$validated['status']}.",
                AllocationMatch::class,
                $match->id
            );
        }

        return back()->with('success', 'Match status overridden by admin.');
    }

    public function setEmergencyPriority(Request $request, Recipient $recipient, AuditLogger $auditLogger): RedirectResponse
    {
        $recipient->update([
            'is_emergency' => true,
            'urgency_level' => 'high',
            'priority_escalation_requested' => false,
        ]);
        $auditLogger->log($request->user(), 'admin', 'emergency_priority', "Recipient {$recipient->id} escalated");

        return back()->with('success', 'Emergency priority activated for recipient.');
    }

    public function verifyDonorIdentity(Donor $donor): RedirectResponse
    {
        $donor->update([
            'identity_verified' => true,
            'medical_status' => $donor->medical_status === 'REGISTERED' ? 'VERIFIED' : $donor->medical_status,
        ]);

        return back()->with('success', 'Donor identity verified.');
    }

    public function verifyRecipientIdentity(Recipient $recipient): RedirectResponse
    {
        $recipient->update([
            'identity_verified' => true,
            'status' => $recipient->status === 'REGISTERED' ? 'VERIFIED' : $recipient->status,
        ]);

        return back()->with('success', 'Recipient identity verified.');
    }

    public function overrideRecipientApproval(Request $request, Recipient $recipient): RedirectResponse
    {
        $validated = $request->validate([
            'approved' => ['required', 'boolean'],
        ]);

        $approved = (bool) $validated['approved'];
        $recipient->update([
            'admin_approved' => $approved,
            'flagged_for_review' => false,
            'status' => $approved ? 'VERIFIED' : 'REJECTED',
        ]);

        return back()->with('success', $approved ? 'Recipient approved by admin override.' : 'Recipient marked as rejected by admin.');
    }

    public function verifyHospitalIdentity(Hospital $hospital): RedirectResponse
    {
        $hospital->update(['identity_verified' => true]);

        return back()->with('success', 'Hospital identity verified.');
    }

    public function setCertificateRecipientName(Request $request, Transplant $transplant): RedirectResponse
    {
        $request->merge([
            'recipient_name_override' => $request->filled('recipient_name_override')
                ? trim((string) $request->input('recipient_name_override'))
                : null,
        ]);

        $validated = $request->validate([
            'recipient_name_override' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
        ]);
        $transplant->update(['recipient_name_override' => $validated['recipient_name_override']]);

        return back()->with('success', 'Certificate recipient name updated.');
    }

    public function resolveIssueReport(IssueReport $issueReport): RedirectResponse
    {
        $issueReport->update(['status' => 'resolved']);

        return back()->with('success', 'Issue report marked as resolved.');
    }

    public function reopenIssueReport(IssueReport $issueReport): RedirectResponse
    {
        $issueReport->update(['status' => 'open']);

        return back()->with('success', 'Issue report reopened.');
    }

    public function settings(): View
    {
        $settings = SystemSetting::firstOrCreate(
            ['id' => 1],
            [
                'urgency_weight' => 40,
                'waiting_weight' => 30,
                'compatibility_weight' => 20,
                'emergency_threshold' => 180,
                'max_daily_surgeries' => 6,
            ]
        );

        return view('admin.settings', ['settings' => $settings]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'urgency_weight' => ['required', 'integer', 'min:1', 'max:100'],
            'waiting_weight' => ['required', 'integer', 'min:1', 'max:100'],
            'compatibility_weight' => ['required', 'integer', 'min:1', 'max:100'],
            'emergency_threshold' => ['required', 'integer', 'min:1', 'max:3650'],
            'max_daily_surgeries' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $settings = SystemSetting::firstOrCreate(['id' => 1]);
        $settings->update($validated);

        return back()->with('success', 'System settings updated.');
    }
}
