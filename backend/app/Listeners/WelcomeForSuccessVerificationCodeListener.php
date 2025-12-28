<?php

namespace App\Listeners;

use App\Events\WelcomeForSuccessVerificationCodeEvent;
use App\Mail\WelcomeForSuccessMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class WelcomeForSuccessVerificationCodeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WelcomeForSuccessVerificationCodeEvent $event): void
    {
        $email = $event->user['email'];
        \Log::info('WelcomeForSuccessVerificationCodeEvent started',[
            'email' => $email,
        ]);

        Mail::to($email)
            ->send(new WelcomeForSuccessMail($email));
    }
}
