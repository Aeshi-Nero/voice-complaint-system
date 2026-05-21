<?php

namespace App\Notifications;

use App\Models\ComplaintMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ComplaintMessage $message;

    public function __construct(ComplaintMessage $message)
    {
        $this->message = $message;
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
        $complaint = $this->message->complaint;
        $sender = $this->message->is_admin ? 'An administrator' : $this->message->user->name;
        $url = $this->message->is_admin
            ? route('complaints.show', $complaint)
            : route('admin.complaints.show', $complaint);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('New Message on Complaint #' . $complaint->complaint_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($sender . ' sent a new message regarding complaint #' . $complaint->complaint_number . '.')
            ->line('"' . ($this->message->message ?: 'Sent an attachment') . '"')
            ->action('View Conversation', $url);
    }

    public function toSms($notifiable): string
    {
        $complaint = $this->message->complaint;
        $sender = $this->message->is_admin ? 'Admin' : $this->message->user->name;
        return $sender . ' sent a message on complaint #' . $complaint->complaint_number . ': "' . ($this->message->message ?: 'Attachment') . '"';
    }
}
