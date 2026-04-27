<?php

namespace App\Mail;

use App\Models\RecipientVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecipientInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecipientVerification $invite,
        public string $hospitalName,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Complete Your Organ Recipient Registration')
            ->view('emails.recipient-invitation');
    }
}
