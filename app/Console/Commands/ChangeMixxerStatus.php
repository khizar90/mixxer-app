<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\Notification;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChangeMixxerStatus extends Command
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
    protected $signature = 'app:change-mixxer-status';

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
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');
        $mixxers = Mixxer::where('status', '!=', 2)->get();
        foreach ($mixxers as $mixxer) {
            try {
                $owner = User::find($mixxer->user_id);
                $ownerTimeZone = UserDevice::where('user_id', $owner->uuid)->first();
                $current_time = time();
                $current_time = Carbon::createFromTimestamp($current_time)->setTimezone($ownerTimeZone->timezone);
                $current_time = $current_time->timestamp;
                if ($mixxer->end_timestamp <= $current_time) {
                    $mixxer->status = 2;
                    $mixxer->save();
                    $ownerToken = UserDevice::where('user_id', $owner->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');
                    $data = [
                        'data_id' => $mixxer->id,
                        'type' => 'mixxer_complete',
                    ];

                    Notification::where('data_id', $mixxer->id)->where('type', 'mixxer_start')->delete();

                    $unreadCounts = UserUnreadCount::handle($owner);
                    $this->firebaseNotification->sendNotification($mixxer->title, 'Your Mixxer has ended – we hope it was everything you expected!', $ownerToken, $data, $unreadCounts);
                    // NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'Your Mixxer has ended – we hope it was everything you expected!', 'mixxer', 'mixxer_complete');
                    foreach ($mixxerUserIDs as $userId) {
                        $tokens = UserDevice::where('user_id', $userId)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        // NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'Your Mixxer has ended – we hope it was everything you expected!', 'mixxer', 'mixxer_complete');
                        $otherUser = User::find($userId);
                        $unreadCounts = UserUnreadCount::handle($otherUser);
                        $this->firebaseNotification->sendNotification($mixxer->title, 'Your Mixxer has ended – we hope it was everything you expected!', $tokens, $data, $unreadCounts);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error processing Mixxer ID ' . $mixxer->id . ': ' . $e->getMessage());
            }
        }
    }
}
