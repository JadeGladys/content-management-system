<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordSetupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $setupUrl,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Set up your CMS password')
            ->view('emails.password-setup');
    }
}