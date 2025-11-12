<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use App\Notifications\PasswordChangedNotification;

class SendPasswordChangedNotification
{
    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        if (method_exists($event->user, 'notify')) {
            $event->user->notify(new PasswordChangedNotification());
        }
    }
}