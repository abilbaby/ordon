<?php

namespace App\Http\Controllers;

use App\DTO\RegisterRecipientWithInviteData;
use App\Http\Requests\StoreRecipientInviteRegistrationRequest;
use App\Services\RecipientInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RecipientInviteRegistrationController extends Controller
{
    public function show(Request $request, RecipientInvitationService $service): View|Response
    {
        $validated = $request->validate([
            'rvid' => ['required', 'string'],
        ]);

        try {
            $invite = $service->validateRvid($validated['rvid']);
        } catch (ValidationException $exception) {
            return response()
                ->view('auth.recipient-invite-expired', ['message' => $exception->getMessage()], 403);
        }

        return view('auth.recipient-invite-register', ['invite' => $invite]);
    }

    public function store(StoreRecipientInviteRegistrationRequest $request, RecipientInvitationService $service): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $user = $service->registerWithInvite(
                new RegisterRecipientWithInviteData(
                    $validated['rvid'],
                    $validated['identity_type'],
                    $validated['identity_number'],
                    $validated['password'],
                )
            );
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        Auth::login($user);

        return redirect()->route('recipient.dashboard')->with(
            'success',
            'Registration complete. Awaiting hospital/admin approval for full recipient access.'
        );
    }
}
