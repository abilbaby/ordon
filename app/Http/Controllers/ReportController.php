<?php

namespace App\Http\Controllers;

use App\Models\IssueReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scope' => ['required', 'in:site,donor,recipient,hospital'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        IssueReport::create([
            'user_id' => $request->user()->id,
            'role' => $request->user()->role,
            'scope' => $validated['scope'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Report submitted successfully.');
    }
}
