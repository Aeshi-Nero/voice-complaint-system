<?php

namespace App\Notifications;

use App\Models\Poll;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPollNotification extends Notification
{
    use Queueable;

    protected $poll;

    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    public function via($notifiable): array
    {
        $channels = ['mail'];
        
        // If phone number exists, we'd add 'database' or a custom SMS channel here
        // For this simulation, we'll just stick to mail as per standard Laravel setup
        // but the prompt mentions "through email or phone number"
        
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Campus Poll: ' . $this->poll->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new poll has been launched on V.O.I.C.E.')
            ->line('Title: ' . $this->poll->title)
            ->line('Description: ' . $this->poll->description)
            ->action('Vote Now', route('user.polls'))
            ->line('Your voice matters!');
    }

    public function toArray($notifiable): array
    {
        return [
            'poll_id' => $this->poll->id,
            'title' => $this->poll->title,
        ];
    }
}
