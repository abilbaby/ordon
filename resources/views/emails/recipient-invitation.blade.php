<p>Hello {{ $invite->recipient_name }},</p>
<p>You have been invited by <strong>{{ $hospitalName }}</strong> to complete your organ recipient registration on ORDON.</p>
<p>
    Registration link:<br>
    <a href="{{ $invite->registration_link }}">{{ $invite->registration_link }}</a>
</p>
<p><strong>Important:</strong> this secure link expires in 48 hours and can be used only once.</p>
<p>Thank you,<br>ORDON Coordination Team</p>
