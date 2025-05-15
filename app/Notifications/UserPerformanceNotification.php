<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\worksnapUser;
use App\Models\Project;

class UserPerformanceNotification extends Notification
{
    use Queueable;

    protected worksnapUser $worker;
    protected Project     $project;
    protected float       $percent;

    public function __construct(worksnapUser $worker, Project $project, float $percent)
    {
        $this->worker  = $worker;
        $this->project = $project;
        $this->percent = $percent;
    }

    public function via($notifiable): array
    {
        return ['mail','database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🚨 Rendimiento inusual: {$this->worker->first_name} {$this->worker->last_name}")
            ->line("Proyecto: {$this->project->name}")
            ->line("Actividad: {$this->percent}% (fuera de [75%,97%])")
            ->action('Ver perfil', "/user/{$this->worker->id}");
    }

    public function toArray($notifiable): array
    {
        return [
            'type'      => 'user_performance',
            'title'     => "{$this->worker->first_name} {$this->worker->last_name}",
            'message'   => "Rendimiento del {$this->percent}% en proyecto {$this->project->name}.",
            'url'       => "/user/{$this->worker->id}",
            'timestamp' => now()->toISOString(),
        ];
    }
}
