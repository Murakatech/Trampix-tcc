<?php

namespace App\Listeners;

use App\Notifications\PasswordChangedNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Notification;

class SendPasswordChangedNotification
{
    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        if ($event->user) {
            Notification::send($event->user, new PasswordChangedNotification);
        }
    }
}
