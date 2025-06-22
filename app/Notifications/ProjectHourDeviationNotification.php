<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectHourDeviationNotification extends Notification
{
    use Queueable;

    /** @var Project */
    protected Project $project;

    /** @var float */
    protected float $planned;

    /** @var float */
    protected float $actual;

    /** @var float */
    protected float $deviation;

    /**
     * Create a new notification instance.
     *
     * @param Project $project
     * @param  float  $planned     Planned hours for the week
     * @param  float  $actual      Actual hours worked in that week
     * @param  float  $deviation   Percentage deviation between actual and planned
     */
    public function __construct(Project $project, float $planned, float $actual, float $deviation)
    {
        $this->project   = $project;
        $this->planned   = $planned;
        $this->actual    = $actual;
        $this->deviation = $deviation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🚨 Deviation in hours: {$this->project->name}")
            ->line("Planned: {$this->planned}h")
            ->line("Actual (last week): {$this->actual}h")
            ->line("Deviation: {$this->deviation}%")
            ->action('View project', route('projects.show',$this->project->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'project_deviation',
            'title'      => $this->project->name,
            'project_id'  => $this->project->id,
            'project'     => $this->project->name,
            'planned'     => $this->planned,
            'actual'      => $this->actual,
            'deviation'   => $this->deviation,
            'url'        => "/project/{$this->project->id}",
            'message'     => "Deviation for {$this->deviation}% in hours of Project.",
        ];
    }
}
