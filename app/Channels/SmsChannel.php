<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phone = $notifiable->routeNotificationFor('sms');

        if (!$phone) {
            return;
        }

        Log::info('SMS notification sent', [
            'to' => $phone,
            'message' => $message,
        ]);
    }
}
