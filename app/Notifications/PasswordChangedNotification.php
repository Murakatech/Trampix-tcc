<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordChangedNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Senha alterada com sucesso - ' . config('app.name'))
            ->line('A senha da sua conta foi alterada com sucesso.')
            ->line('Se você não fez esta alteração, por favor, redefina sua senha imediatamente e entre em contato com o suporte.')
            ->action('Redefinir senha', url(route('password.request')))
            ->line('Conta: ' . ($notifiable->email ?? 'não informado'));
    }
}