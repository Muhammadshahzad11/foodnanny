<?php

namespace App\Listeners;



use App\Events\PasswordForgotten;
use App\Mail\ForgotPassword;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function handle(PasswordForgotten $event): void
    {
        try {
            Mail::to($event->info['email'])->send(new ForgotPassword(['name' => $event->info['name'], 'pin' => $event->info['pin']]));
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
