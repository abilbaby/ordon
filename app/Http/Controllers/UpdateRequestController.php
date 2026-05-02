<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipient;
use App\Models\RecipientUpdateRequest;
use Illuminate\Support\Facades\DB;

class UpdateRequestController extends Controller
{
    public function pendingRequests()
    {
        $requests = RecipientUpdateRequest::where('status', 'Pending')
            ->latest()
            ->get();
        
        return view('hospital.update-requests', compact('requests'));
    }

    public function history()
    {
        $history = RecipientUpdateRequest::whereIn('status', ['Approved', 'Rejected'])
            ->latest()
            ->get();
        
        return view('hospital.update-history', compact('history'));
    }

    public function show($id)
    {
        $request = RecipientUpdateRequest::findOrFail($id);
        $recipient = Recipient::findOrFail($request->recipient_id);
        
        return view('hospital.request-details', compact('request', 'recipient'));
    }

    public function historyDetails($id)
    {
        $request = RecipientUpdateRequest::findOrFail($id);
        $recipient = Recipient::find($request->recipient_id);
        $approvedFields = json_decode($request->approved_fields, true) ?? [];
        
        return view('hospital.history-details', compact('request', 'recipient', 'approvedFields'));
    }

    public function approveSelected(Request $req, $id)
    {
        $request = RecipientUpdateRequest::findOrFail($id);
        $recipient = Recipient::findOrFail($request->recipient_id);

        if ($request->status !== 'Pending') {
            return back()->with('error', 'Already processed');
        }

        $fields = $req->approvedFields ?? [];
        $approvedList = [];

        if (empty($fields)) {
            return back()->with('error', 'Please select at least one field to approve');
        }

        DB::beginTransaction();
        try {
            if (in_array('full_name', $fields)) {
                $recipient->user->update(['name' => $request->requested_full_name]);
                $approvedList[] = 'full_name';
            }

            if (in_array('dob', $fields)) {
                $recipient->date_of_birth = $request->requested_dob;
                $approvedList[] = 'dob';
            }

            if (in_array('gender', $fields)) {
                $recipient->gender = $request->requested_gender;
                $approvedList[] = 'gender';
            }

            if (in_array('blood_group', $fields)) {
                $recipient->blood_group = $request->requested_blood_group;
                $approvedList[] = 'blood_group';
            }

            if (in_array('organ', $fields)) {
                $recipient->organ_needed = $request->requested_organ_needed;
                $approvedList[] = 'organ';
            }

            if (in_array('urgency', $fields)) {
                $recipient->urgency_level = $request->requested_urgency_level;
                $approvedList[] = 'urgency';
            }

            if (in_array('waiting_time', $fields)) {
                $recipient->waiting_time = $request->requested_waiting_time;
                $approvedList[] = 'waiting_time';
            }

            if (in_array('other_organs', $fields)) {
                $recipient->organs_needed = $request->requested_other_organs;
                $approvedList[] = 'other_organs';
            }

            $recipient->save();

            $request->status = 'Approved';
            $request->approved_fields = json_encode($approvedList);
            $request->reviewed_by = $req->user()->id;
            $request->reviewed_at = now();
            $request->save();

            DB::commit();
            return redirect('/hospital/update-requests')->with('success', 'Selected fields approved');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    public function reject($id)
    {
        $request = RecipientUpdateRequest::findOrFail($id);

        if ($request->status !== 'Pending') {
            return back()->with('error', 'Already processed');
        }

        $request->status = 'Rejected';
        $request->reviewed_by = auth()->user()->id;
        $request->reviewed_at = now();
        $request->save();

        return redirect('/hospital/update-requests')->with('success', 'Request rejected');
    }
}
