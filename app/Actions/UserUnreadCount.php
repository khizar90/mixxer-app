<?php

    namespace App\Actions;

    use App\Models\Message;
    use App\Models\Mixxer;
    use App\Models\MixxerJoinRequest;
    use App\Models\Notification;
    use App\Models\User;

    class UserUnreadCount
    {

        public static function handle(User $user)
        {
            $message_count = Message::where('ticket_id', 0)->where('to', $user->uuid)->where('is_read', 0)->distinct('from')->count();
            $notification_count = Notification::where('user_id', $user->uuid)->where('is_read', 0)->count();
            $ticket_unread_counter = Message::where('to', $user->uuid)->where('ticket_id', '!=', 0)->where('is_read', 0)->distinct('ticket_id')->count();
            $mixxerIds = MixxerJoinRequest::where('user_id', $user->uuid)
                ->where('status', 'accept')
                ->pluck('mixxer_id');
            $ownedMixxers = Mixxer::where('user_id', $user->uuid)->pluck('id');
            $combinedMixxers = $ownedMixxers->merge($mixxerIds);

            // Get the Mixxer IDs that have unread messages
            $unreadMixxerCount = Message::where('from', '!=', $user->uuid)
                ->whereIn('mixxer_id', $combinedMixxers)
                ->whereDoesntHave('messageReads', function ($query) use ($user) {
                    $query->where('user_id', $user->uuid);
                })
                ->distinct('mixxer_id')
                ->count('mixxer_id');
            return $unreadMixxerCount + $message_count + $notification_count + $ticket_unread_counter;
        }
    }
