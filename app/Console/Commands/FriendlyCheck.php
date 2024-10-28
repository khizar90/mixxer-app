<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Models\Mixxer;
use App\Models\MixxerFriendlyCheck;
use App\Models\MixxerFriendlyCheckNotfication;
use App\Models\MixxerJoinRequest;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FriendlyCheck extends Command
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
    protected $signature = 'app:friendly-check';

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

        $current_time = time();
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');

        $mixxers = Mixxer::where('status', 1)->get();


        foreach ($mixxers as $mixxer) {
            try {
                $owner = User::find($mixxer->user_id);
                $ownerTimeZone = UserDevice::where('user_id', $owner->uuid)->first();
                $current_time = time();
                $current_time = Carbon::createFromTimestamp($current_time)->setTimezone($ownerTimeZone->timezone);
                $current_time = $current_time->timestamp;
                $newTime = $mixxer->start_timestamp  + 600;
                if ($newTime <= $current_time) {
                    $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');

                    $ownerCheck = MixxerFriendlyCheck::where('user_id', $owner->uuid)->where('mixxer_id', $mixxer->id)->first();
                    if (!$ownerCheck) {
                        $ownerNotificationCheck = MixxerFriendlyCheckNotfication::where('user_id', $owner->uuid)->where('mixxer_id', $mixxer->id)->first();
                        if (!$ownerNotificationCheck) {

                            $ownerToken = UserDevice::where('user_id', $owner->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                            $data = [
                                'data_id' => $mixxer->id,
                                'type' => 'mixxer_friendly_check',
                            ];
                            $unreadCounts = UserUnreadCount::handle($owner);
                            $this->firebaseNotification->sendNotification($mixxer->title, 'Just checking in! Is everything going smoothly with your Mixxer? Please confirm.', $ownerToken, $data, $unreadCounts);
                            $createNotfication = new MixxerFriendlyCheckNotfication();
                            $createNotfication->user_id = $owner->uuid;
                            $createNotfication->mixxer_id = $mixxer->id;
                            $createNotfication->save();

                            NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'Just checking in! Is everything going smoothly with your Mixxer? Please confirm', 'mixxer', 'mixxer_friendly_check');
                        }
                    }
                    foreach ($mixxerUserIDs as $userID) {
                        $check = MixxerFriendlyCheck::where('user_id', $userID)->where('mixxer_id', $mixxer->id)->first();
                        if (!$check) {
                            $userNotificationCheck = MixxerFriendlyCheckNotfication::where('user_id', $userID)->where('mixxer_id', $mixxer->id)->first();
                            if (!$userNotificationCheck) {
                                $tokens = UserDevice::where('user_id', $userID)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                                $data = [
                                    'data_id' => $mixxer->id,
                                    'type' => 'mixxer_friendly_check',
                                ];
                                $otherUser = User::find($userID);
                                $createUserNotfication = new MixxerFriendlyCheckNotfication();
                                $createUserNotfication->user_id = $userID;
                                $createUserNotfication->mixxer_id = $mixxer->id;
                                $createUserNotfication->save();
                                NewNotification::handle($userID, $mixxer->user_id, $mixxer->id, 'Just checking in! Is everything going smoothly with your Mixxer? Please confirm.', 'mixxer', 'mixxer_friendly_check');
                                $unreadCounts = UserUnreadCount::handle($otherUser);
                                $this->firebaseNotification->sendNotification($mixxer->title, 'Just checking in! Is everything going smoothly with your Mixxer? Please confirm.', $tokens, $data, $unreadCounts);

                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error processing Mixxer ID ' . $mixxer->id . ': ' . $e->getMessage());
            }
        }
    }
}
