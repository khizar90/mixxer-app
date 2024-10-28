<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Models\Mixxer;
use App\Models\MixxerAfterEnd;
use App\Models\MixxerJoinRequest;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AfterEnd extends Command
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
    protected $signature = 'app:after-end';

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

        $mixxerIDs = MixxerAfterEnd::pluck('mixxer_id');
        $mixxers = Mixxer::where('status', 2)->whereNotIn('id', $mixxerIDs)->get();

        foreach ($mixxers as $mixxer) {
            try {
                $after1Hour = $mixxer->end_timestamp + 3600;
                $owner = User::find($mixxer->user_id);
                $ownerTimeZone = UserDevice::where('user_id', $owner->uuid)->first();
                $current_time = time();
                $current_time = Carbon::createFromTimestamp($current_time)->setTimezone($ownerTimeZone->timezone);
                $current_time = $current_time->timestamp;
                if ($after1Hour <= $current_time) {
                    $create = new MixxerAfterEnd();
                    $create->mixxer_id = $mixxer->id;
                    $create->save();
                    $ownerToken = UserDevice::where('user_id', $owner->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');
                    $data = [
                        'data_id' => $mixxer->id,
                        'type' => 'mixxer_after_end',
                    ];
                    $unreadCounts = UserUnreadCount::handle($owner);
                    $this->firebaseNotification->sendNotification($mixxer->title, 'Pssst, got pics? Share them and connect before the chat closes for new messages.', $ownerToken, $data, $unreadCounts);
                    NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'Pssst, got pics? Share them and connect before the chat closes for new messages.', 'mixxer', 'mixxer_after_end');
                    foreach ($mixxerUserIDs as $userId) {
                        $tokens = UserDevice::where('user_id', $userId)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'Pssst, got pics? Share them and connect before the chat closes for new messages.', 'mixxer', 'mixxer_after_end');
                        $otherUser = User::find($userId);
                        $unreadCounts = UserUnreadCount::handle($otherUser);
                        $this->firebaseNotification->sendNotification($mixxer->title, 'Pssst, got pics? Share them and connect before the chat closes for new messages.', $tokens, $data, $unreadCounts);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error processing Mixxer ID ' . $mixxer->id . ': ' . $e->getMessage());
            }
        }
    }
}
