<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Models\Mixxer;
use App\Models\MixxerInbox;
use App\Models\MixxerJoinRequest;
use App\Models\Notification;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DisableMixxerInbox extends Command
{
    protected $firebaseNotification;

    public function __construct(FirebaseNotificationService $firebaseNotification)
    {
        parent::__construct();
        $this->firebaseNotification = $firebaseNotification;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:disable-mixxer-inbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $diableMixxer  = MixxerInbox::pluck('mixxer_id');
        $mixxers = Mixxer::whereNotIn('id', $diableMixxer)->where('status', 2)->get();
        $current_time = time();
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');

        foreach ($mixxers as $mixxer) {
            try {
                $after24HourTime = $mixxer->start_timestamp + 86400;
                $owner = User::find($mixxer->user_id);
                $ownerTimeZone = UserDevice::where('user_id',$owner->uuid)->first();
                $current_time = time();
                $current_time = Carbon::createFromTimestamp($current_time)->setTimezone($ownerTimeZone->timezone);
                $current_time = $current_time->timestamp;
                if ($after24HourTime <= $current_time) {
                    $create = new MixxerInbox();
                    $create->mixxer_id = $mixxer->id;
                    $create->disable = 1;
                    $create->save();
                    $ownerToken = UserDevice::where('user_id', $owner->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');

                    $data = [
                        'data_id' => $mixxer->id,
                        'type' => 'mixxer_chat_disable',
                    ];
                    Notification::where('data_id', $mixxer->id)->where('type', 'mixxer_after_end')->delete();
                    $unreadCounts = UserUnreadCount::handle($owner);
                    $this->firebaseNotification->sendNotification($mixxer->title, 'We’d love to hear how your Mixxer went! Share your feedback with us.', $ownerToken, $data, $unreadCounts);
                    NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'We’d love to hear how your Mixxer went! Share your feedback with us.', 'mixxer', 'mixxer_chat_disable');
                    foreach ($mixxerUserIDs as $userId) {
                        $tokens = UserDevice::where('user_id', $userId)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'We’d love to hear how your Mixxer went! Share your feedback with us.', 'mixxer', 'mixxer_chat_disable');
                        $otherUser = User::find($userId);
                        $unreadCounts = UserUnreadCount::handle($otherUser);
                        $this->firebaseNotification->sendNotification($mixxer->title, 'We’d love to hear how your Mixxer went! Share your feedback with us.', $tokens, $data, $unreadCounts);
                    }

                }
            } catch (\Exception $e) {
                Log::error('Error processing Mixxer ID ' . $mixxer->id . ': ' . $e->getMessage());
            }
        }
    }
}
