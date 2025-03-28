<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature = 'mail:test {email?}';
    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address');

        $this->info("Testing mail configuration...");
        $this->info("Sending to: " . $email);

        try {
            Mail::raw('Test email from Equipment Management System', function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email');
            });

            $this->info("Mail sent successfully!");
        } catch (\Exception $e) {
            $this->error("Mail sending failed!");
            $this->error($e->getMessage());
        }
    }
} 