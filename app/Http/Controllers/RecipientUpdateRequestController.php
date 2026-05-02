<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use App\Models\RecipientUpdateRequest;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RecipientUpdateRequestController extends Controller
{
    /**
     * Display recipient profile with update request form
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        $recipient = Recipient::where('user_id', $user->id)
            ->with(['user'])
            ->firstOrFail();

        // Get current values for comparison
        $currentValues = $recipient->getApprovalRequiredValues();

        // Get pending update request if any
        $pendingRequest = RecipientUpdateRequest::where('recipient_id', $recipient->id)
            ->where('hospital_id', $recipient->hospital_id)
            ->pending()
            ->with(['requestedByUser'])
            ->first();

        // Get recent update requests
        $recentRequests = RecipientUpdateRequest::where('recipient_id', $recipient->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('recipient.profile', [
            'recipient' => $recipient,
            'currentValues' => $currentValues,
            'pendingRequest' => $pendingRequest,
            'recentRequests' => $recentRequests,
        ]);
    }

    /**
     * Submit update request for recipient
     */
    public function submitUpdateRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'requested_full_name' => ['required', 'string', 'max:255'],
            'requested_dob' => ['required', 'date', 'before_or_equal:today'],
            'requested_gender' => ['required', 'in:male,female,other'],
            'requested_blood_group' => ['required', 'string', 'max:3'],
            'requested_organ_needed' => ['required', 'string', 'max:255'],
            'requested_urgency_level' => ['required', 'in:low,medium,high'],
            'requested_waiting_time' => ['required', 'integer', 'min:0'],
            'requested_other_organs' => ['nullable', 'array'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            $updateRequest = RecipientUpdateRequest::create([
                'recipient_id' => $validated['requested_full_name'] ? $request->user()->recipient->id : null,
                'hospital_id' => $request->user()->hospital->id,
                'requested_by_user_id' => $request->user()->id,
                'requested_full_name' => $validated['requested_full_name'],
                'requested_dob' => $validated['requested_dob'],
                'requested_gender' => $validated['requested_gender'],
                'requested_blood_group' => $validated['requested_blood_group'],
                'requested_organ_needed' => $validated['requested_organ_needed'],
                'requested_urgency_level' => $validated['requested_urgency_level'],
                'requested_waiting_time' => $validated['requested_waiting_time'],
                'requested_other_organs' => $validated['requested_other_organs'],
                'reason' => $validated['reason'],
                'status' => 'Pending',
            ]);

            DB::commit();
            return back()->with('success', 'Profile update request submitted to hospital for confirmation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit request. Please try again.');
        }
    }

    /**
     * Display recipients for hospital management
     */
    public function recipients(Request $request): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $query = Recipient::where('hospital_id', $hospital->id)
            ->with(['user', 'updateRequests' => function ($query) {
                $query->latest()->take(3);
            }]);

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('admin_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('admin_approved', false);
            }
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $recipients = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('hospital.recipients', [
            'recipients' => $recipients,
        ]);
    }

    /**
     * Display recipient details with history
     */
    public function recipientDetails(Request $request, int $id): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $recipient = Recipient::where('id', $id)
            ->where('hospital_id', $hospital->id)
            ->with(['user'])
            ->firstOrFail();

        // Get update history
        $updateHistory = RecipientUpdateRequest::where('recipient_id', $recipient->id)
            ->where('hospital_id', $hospital->id)
            ->where('status', '!=', 'Pending')
            ->with(['reviewedBy'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                $changes = [];
                
                // Compare fields and log changes
                if ($request->requested_full_name && $request->requested_full_name !== $request->recipient->user->name) {
                    $changes[] = [
                        'field_name' => 'Full Name',
                        'old_value' => $request->recipient->user->name,
                        'new_value' => $request->requested_full_name,
                        'changed_by' => $request->reviewedBy?->name ?? 'System',
                        'created_at' => $request->updated_at ?? $request->created_at,
                    ];
                }
                
                if ($request->requested_blood_group && $request->requested_blood_group !== $request->recipient->blood_group) {
                    $changes[] = [
                        'field_name' => 'Blood Group',
                        'old_value' => $request->recipient->blood_group,
                        'new_value' => $request->requested_blood_group,
                        'changed_by' => $request->reviewedBy?->name ?? 'System',
                        'created_at' => $request->updated_at ?? $request->created_at,
                    ];
                }
                
                if ($request->requested_organ_needed && $request->requested_organ_needed !== $request->recipient->organ_needed) {
                    $changes[] = [
                        'field_name' => 'Organ Needed',
                        'old_value' => $request->recipient->organ_needed,
                        'new_value' => $request->requested_organ_needed,
                        'changed_by' => $request->reviewedBy?->name ?? 'System',
                        'created_at' => $request->updated_at ?? $request->created_at,
                    ];
                }
                
                if ($request->requested_urgency_level && $request->requested_urgency_level !== $request->recipient->urgency_level) {
                    $changes[] = [
                        'field_name' => 'Urgency Level',
                        'old_value' => ucfirst($request->recipient->urgency_level),
                        'new_value' => ucfirst($request->requested_urgency_level),
                        'changed_by' => $request->reviewedBy?->name ?? 'System',
                        'created_at' => $request->updated_at ?? $request->created_at,
                    ];
                }
                
                return $changes;
            })
            ->flatten(1);

        return view('hospital.recipient-details', [
            'recipient' => $recipient,
            'updateHistory' => $updateHistory,
        ]);
    }

    /**
     * Show update requests for hospital (all statuses with filters)
     */
    public function hospitalRequests(Request $request): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $query = RecipientUpdateRequest::with(['recipient.user', 'requestedByUser'])
            ->forHospital($hospital->id);

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'approved') {
                $query->approved();
            } elseif ($request->status === 'rejected') {
                $query->rejected();
            }
        }

        $requests = $query->latest()->paginate(10);

        return view('hospital.update-requests', [
            'requests' => $requests,
        ]);
    }

    /**
     * Show update history for hospital
     */
    public function updateHistory(Request $request): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        // Build base query
        $query = RecipientUpdateRequest::with(['recipient.user', 'requestedByUser', 'reviewedBy'])
            ->forHospital($hospital->id)
            ->where('status', '!=', 'Pending');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('recipient.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply field filter
        if ($request->filled('field')) {
            $field = $request->field;
            $query->where(function ($q) use ($field) {
                switch ($field) {
                    case 'full_name':
                        $q->whereNotNull('requested_full_name');
                        break;
                    case 'blood_group':
                        $q->whereNotNull('requested_blood_group');
                        break;
                    case 'organ_needed':
                        $q->whereNotNull('requested_organ_needed');
                        break;
                    case 'urgency_level':
                        $q->whereNotNull('requested_urgency_level');
                        break;
                }
            });
        }

        $history = $query->latest()->paginate(15);

        return view('hospital.update-history', [
            'history' => $history,
        ]);
    }

    /**
     * Show specific request details for hospital review
     */
    public function reviewRequest(Request $request, int $id): View
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $updateRequest = RecipientUpdateRequest::with(['recipient.user', 'requestedByUser'])
            ->where('id', $id)
            ->where('hospital_id', $hospital->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        // Get current values for comparison
        $currentValues = $updateRequest->recipient->getApprovalRequiredValues();

        return view('hospital.review-request', [
            'request' => $updateRequest,
            'currentValues' => $currentValues,
        ]);
    }

    /**
     * Approve a pending update request
     */
    public function approveRequest(Request $request, int $id): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $updateRequest = RecipientUpdateRequest::where('id', $id)
            ->where('hospital_id', $hospital->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $recipient = $updateRequest->recipient;
            
            $recipient->update([
                'blood_group' => $updateRequest->requested_blood_group,
                'organ_needed' => $updateRequest->requested_organ_needed,
                'urgency_level' => $updateRequest->requested_urgency_level,
                'waiting_time' => $updateRequest->requested_waiting_time,
                'date_of_birth' => $updateRequest->requested_dob,
                'gender' => $updateRequest->requested_gender,
                'organs_needed' => $updateRequest->requested_other_organs,
            ]);

            if ($updateRequest->requested_full_name) {
                $recipient->user->update(['name' => $updateRequest->requested_full_name]);
            }

            $updateRequest->update([
                'status' => 'Approved',
                'reviewed_by' => $request->user()->id,
                'reviewer_note' => $request->input('note'),
            ]);

            DB::commit();
            return redirect()->route('hospital.update-requests')
                ->with('success', 'Update request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    /**
     * Approve selected fields from a pending update request
     */
    public function approveSelectedFields(Request $request, int $id): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $updateRequest = RecipientUpdateRequest::where('id', $id)
            ->where('hospital_id', $hospital->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        $validated = $request->validate([
            'fields' => ['required', 'array', 'min:1'],
            'fields.*' => ['string', 'in:full_name,date_of_birth,gender,blood_group,organ_needed,urgency_level,waiting_time,other_organs'],
        ]);

        DB::beginTransaction();
        try {
            $recipient = $updateRequest->recipient;
            $updateData = [];
            $userUpdateData = [];

            foreach ($validated['fields'] as $field) {
                switch ($field) {
                    case 'full_name':
                        if ($updateRequest->requested_full_name) {
                            $userUpdateData['name'] = $updateRequest->requested_full_name;
                        }
                        break;
                    case 'date_of_birth':
                        $updateData['date_of_birth'] = $updateRequest->requested_dob;
                        break;
                    case 'gender':
                        $updateData['gender'] = $updateRequest->requested_gender;
                        break;
                    case 'blood_group':
                        $updateData['blood_group'] = $updateRequest->requested_blood_group;
                        break;
                    case 'organ_needed':
                        $updateData['organ_needed'] = $updateRequest->requested_organ_needed;
                        break;
                    case 'urgency_level':
                        $updateData['urgency_level'] = $updateRequest->requested_urgency_level;
                        break;
                    case 'waiting_time':
                        $updateData['waiting_time'] = $updateRequest->requested_waiting_time;
                        break;
                    case 'other_organs':
                        $updateData['organs_needed'] = $updateRequest->requested_other_organs;
                        break;
                }
            }

            // Update recipient data
            if (!empty($updateData)) {
                $recipient->update($updateData);
            }

            // Update user data
            if (!empty($userUpdateData)) {
                $recipient->user->update($userUpdateData);
            }

            // Mark request as approved
            $updateRequest->update([
                'status' => 'Approved',
                'reviewed_by' => $request->user()->id,
                'reviewer_note' => 'Selected fields approved: ' . implode(', ', $validated['fields']),
            ]);

            DB::commit();
            return redirect()->route('hospital.update-requests')
                ->with('success', 'Selected fields approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve selected fields. Please try again.');
        }
    }

    /**
     * Reject update request
     */
    public function rejectRequest(Request $request, int $id): RedirectResponse
    {
        $hospital = Hospital::where('user_id', $request->user()->id)->firstOrFail();
        
        $updateRequest = RecipientUpdateRequest::where('id', $id)
            ->where('hospital_id', $hospital->id)
            ->where('status', 'Pending')
            ->firstOrFail();

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
        ]);

        $updateRequest->update([
            'status' => 'Rejected',
            'reviewed_by' => $request->user()->id,
            'reviewer_note' => $validated['note'],
        ]);

        DB::commit();
        return redirect()->route('hospital.update-requests')
                ->with('success', 'Update request rejected successfully.');
    }
}
