<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\worksnapUser;

class UserHourDeviationNotification extends Notification
{
    use Queueable;

    protected worksnapUser $worker;
    protected float         $planned;
    protected float         $actual;
    protected float         $deviation;

    /**
     * Create a new notification instance.
     *
     * @param worksnapUser $worker    The independent professional
     * @param float        $planned   Planned hours for the week
     * @param float        $actual    Actual hours worked
     * @param float        $deviation Percentage deviation
     */
    public function __construct(worksnapUser $worker, float $planned, float $actual, float $deviation)
    {
        $this->worker    = $worker;
        $this->planned   = $planned;
        $this->actual    = $actual;
        $this->deviation = $deviation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<int,string>
     */
    public function via($notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Build the mail representation.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("🚨 Deviation Alert: {$this->worker->first_name} {$this->worker->last_name}")
            ->line("Planned hours: {$this->planned}h")
            ->line("Actual hours: {$this->actual}h")
            ->line("Deviation: {$this->deviation}%")
            ->action('View Worker', "/users/{$this->worker->id}");
    }

    /**
     * Get the array representation (database channel).
     *
     * @param  mixed  $notifiable
     * @return array<string,mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'type'      => 'user_deviation',
            'title'     => $this->worker->first_name . ' ' . $this->worker->last_name,
            'message'   => "Deviation of {$this->deviation}% in weekly hours.",
            'url'       => "/users/{$this->worker->id}",
            'timestamp' => now()->toISOString(),
        ];
    }
}
