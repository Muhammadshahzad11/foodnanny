<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public array $info;

    public function __construct($info)
    {
        $this->info = $info;
    }


    public function build(): ForgotPassword
    {
        return $this->subject("Reset Password")->markdown('emails.password', ['info' => $this->info]);
    }
}
