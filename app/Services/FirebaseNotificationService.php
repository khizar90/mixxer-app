<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\ApnsConfig;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase')->createMessaging();
    }

    public function sendNotification($title, $body, array $tokens, array $data = [],$badge)
    {
        // Create the notification
        $notification = Notification::create($title, $body);

        // Create the message
        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withApnsConfig(
                ApnsConfig::new()
                    ->withSound('bingbong.aiff')
                    ->withBadge($badge)
            )
            ->withData($data);

        // Send the multicast message
        try {
            // $sendReport = $this->messaging->sendMulticast($message, $tokens);
            $this->messaging->sendMulticast($message, $tokens);

            // // Check if there were any failures
            // if ($sendReport->hasFailures()) {
            //     // Handle failures here
            //     foreach ($sendReport->failures()->getItems() as $failure) {
            //         // Log or handle each failure
            //         echo "Failed to send to {$failure->target()->value()}: {$failure->error()->getMessage()}\n";
            //     }
            // }

            return "Notifications sent successfully!";
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            return "Failed to send notifications: " . $e->getMessage();
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            return "Firebase error: " . $e->getMessage();
        }
    }

    // public function sendNotification($title, $body, $token)
    // {
    //     $message = CloudMessage::withTarget('token', $token)
    //         ->withNotification(Notification::create($title, $body));

    //     try {
    //         $this->messaging->send($message);
    //         return "Notification sent successfully!";
    //     } catch (\Kreait\Firebase\Exception\MessagingException $e) {
    //         return "Failed to send notification: " . $e->getMessage();
    //     } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
    //         return "Firebase error: " . $e->getMessage();
    //     }
    // }
}
