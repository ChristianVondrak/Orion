<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\worksnapUser;
use Carbon\Carbon;

class UserInactivityNotification extends Notification
{
    use Queueable;

    protected worksnapUser $worker;
    protected int           $days;

    /**
     * @param worksnapUser $worker
     * @param int          $days
     */
    public function __construct(worksnapUser $worker, int $days)
    {
        $this->worker = $worker;
        $this->days   = $days;
    }

    public function via($notifiable): array
    {
        return ['mail','database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🚨 Inactividad detectada: {$this->worker->first_name} {$this->worker->last_name}")
            ->line("No registra actividad desde hace {$this->days} días.")
            ->action('Ver perfil', "/user/{$this->worker->id}");
    }

    public function toArray($notifiable): array
    {
        return [
            'type'      => 'user_inactivity',
            'title'     => "{$this->worker->first_name} {$this->worker->last_name}",
            'message'   => "Sin actividad registrada por {$this->days} días.",
            'url'       => "/user/{$this->worker->id}",
            'timestamp' => now()->toISOString(),
        ];
    }
}
