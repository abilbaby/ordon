<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('mail:test')]
#[Description('Send a test email to verify mail configuration')]
class TestMailCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Sending test email to supportordon@gmail.com...');

        try {
            Mail::raw('This is a test email from ORDON to verify the mail system is working correctly.', function ($message) {
                $message->to('supportordon@gmail.com')
                    ->subject('ORDON Mail System Test');
            });

            $this->info('✅ Test email sent successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
