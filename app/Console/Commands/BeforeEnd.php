<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Models\Mixxer;
use App\Models\MixxerBeforeEnd;
use App\Models\MixxerJoinRequest;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BeforeEnd extends Command
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
    protected $signature = 'app:before-end';

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

        $mixxerIDs = MixxerBeforeEnd::pluck('mixxer_id');
        $mixxers = Mixxer::where('status', 1)->whereNotIn('id', $mixxerIDs)->get();
        $current_time = time();

        foreach ($mixxers as $mixxer) {
            try {
                $before10Mintue = $mixxer->end_timestamp - 600;
                $owner = User::find($mixxer->user_id);
                $ownerTimeZone = UserDevice::where('user_id',$owner->uuid)->first();
                $current_time = time();
                $current_time = Carbon::createFromTimestamp($current_time)->setTimezone($ownerTimeZone->timezone);
                $current_time = $current_time->timestamp;
                if ($before10Mintue <= $current_time) {
                    $create = new MixxerBeforeEnd();
                    $create->mixxer_id = $mixxer->id;
                    $create->save();
                    $ownerToken = UserDevice::where('user_id', $owner->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');
                    $data = [
                        'data_id' => $mixxer->id,
                        'type' => 'mixxer_before_end',
                    ];
                    $unreadCounts = UserUnreadCount::handle($owner);
                    $this->firebaseNotification->sendNotification($mixxer->title, 'Your Mixxer is ending soon – be sure to snap a pic and collect your stuff!', $ownerToken, $data, $unreadCounts);
                    // NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'Your Mixxer is ending soon – be sure to snap a pic and collect your stuff!', 'mixxer', 'mixxer_before_end');
                    foreach ($mixxerUserIDs as $userId) {
                        $tokens = UserDevice::where('user_id', $userId)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        // NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'Your Mixxer is ending soon – be sure to snap a pic and collect your stuff!', 'mixxer', 'mixxer_before_end');
                        $otherUser = User::find($userId);
                        $unreadCounts = UserUnreadCount::handle($otherUser);
                        $this->firebaseNotification->sendNotification($mixxer->title, 'Your Mixxer is ending soon – be sure to snap a pic and collect your stuff!', $tokens, $data, $unreadCounts);
                    }

                }
            } catch (\Exception $e) {
                Log::error('Error processing Mixxer ID ' . $mixxer->id . ': ' . $e->getMessage());
            }
        }
    }
}
