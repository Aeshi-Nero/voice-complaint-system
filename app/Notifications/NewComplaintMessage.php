<?php

namespace App\Notifications;

use App\Models\ComplaintMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComplaintMessage extends Notification
{
    use Queueable;

    protected $complaintMessage;

    public function __construct(ComplaintMessage $complaintMessage)
    {
        $this->complaintMessage = $complaintMessage;
    }

    public function via($notifiable): array
    {
        // Real-world: You would add 'vonage' or 'twilio' here for SMS
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $complaint = $this->complaintMessage->complaint;
        $sender = $this->complaintMessage->user;

        return (new MailMessage)
            ->subject('New Message on Complaint #' . $complaint->complaint_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new message regarding your complaint.')
            ->line('Sender: ' . $sender->name)
            ->line('Message: ' . $this->complaintMessage->message)
            ->action('View Complaint', route('user.complaints.show', $complaint))
            ->line('Thank you for using V.O.I.C.E!');
    }

    public function toArray($notifiable): array
    {
        return [
            'complaint_id' => $this->complaintMessage->complaint_id,
            'message' => $this->complaintMessage->message,
        ];
    }
}
