<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{

    public function sendNotification($title, $body, array $tokens, array $data = [],$badge)
    {
        $firebase = (new Factory)->withServiceAccount(__DIR__.'/../../config/firebase_credentials.json');
        $notification = Notification::create($title, $body);
        $messaging = $firebase->createMessaging();
        $message = CloudMessage::withTarget('topic', 'global')
            ->withNotification($notification)->withData($data)->withDefaultSounds()
            ->withApnsConfig(
                ApnsConfig::new()->withBadge($badge)
            );
        // Send the multicast message
        try {
            $messaging->sendMulticast($message, $tokens);
            Log::info('Notifications sent successfully', [
                'title' => $title,
                'body' => $body,
                'tokens' => $tokens,
                'data' => $data,
                'badge' => $badge,
                'response' => 'Notification send '
            ]);
            return "Notifications sent successfully!";
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::info($e->getMessage());
            return "Failed to send notifications: " . $e->getMessage();
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::info($e->getMessage());
            return "Firebase error: " . $e->getMessage();
        }
    }
}
