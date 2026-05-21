<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComplaintCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Complaint $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function via($notifiable): array
    {
        $channels = [];
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        if ($notifiable->phone_number) {
            $channels[] = \App\Channels\SmsChannel::class;
        }
        return $channels;
    }

    public function toMail($notifiable)
    {
        $url = route('admin.complaints.show', $this->complaint);
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('New Complaint: #' . $this->complaint->complaint_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new complaint has been submitted.')
            ->line('Title: ' . $this->complaint->title)
            ->line('Category: ' . $this->complaint->category)
            ->line('Priority: ' . $this->complaint->priority)
            ->action('View Complaint', $url);
    }

    public function toSms($notifiable): string
    {
        return 'New complaint #' . $this->complaint->complaint_number . ': "' . $this->complaint->title . '" - ' . config('app.url') . '/admin/complaints/' . $this->complaint->id;
    }
}
