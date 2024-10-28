<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartMeeting extends Command
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
    protected $signature = 'app:start-meeting';

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
        $mixxers = Mixxer::where('status', 0)->get();
        
        foreach ($mixxers as $mixxer) {
            try {
                $owner = User::find($mixxer->user_id);
                $ownerTimeZone = UserDevice::where('user_id', $owner->uuid)->first();
                $current_time = time();
                $current_time = Carbon::createFromTimestamp($current_time)->setTimezone($ownerTimeZone->timezone);
                $current_time = $current_time->timestamp;
                if ($mixxer->start_timestamp <= $current_time) {
                    $mixxer->status = 1;
                    if ($mixxer->type != 'In-Person') {
                        $end_time = Carbon::createFromTimestamp($mixxer->end_timestamp);
                        $new_end_time = $end_time->addDays(2);
                        $endDate = $new_end_time->toDateString();
                        $obj = new \stdClass();
                        $obj->endDate = $endDate;
                        $obj->isLocked = false;
                        $obj->roomMode = 'group';
                        $obj->fields = array('hostRoomUrl');
                        $convert = json_encode($obj);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $convert);
                        $headers = [
                            'Authorization: Bearer ' . config('app.whereby_key'),
                            'Content-Type: application/json'
                        ];
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $response = curl_exec($ch);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        $data = json_decode($response);
                        $viewerUrl = $data->roomUrl;

                        $mixxer->host_url = $data->hostRoomUrl;
                        $mixxer->viewer_url = $viewerUrl;
                    }
                    $mixxer->save();

                    $ownerToken = UserDevice::where('user_id', $owner->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');
                    $data = [
                        'data_id' => $mixxer->id,
                        'type' => 'mixxer_start',
                    ];
                    $unreadCounts = UserUnreadCount::handle($owner);
                    $this->firebaseNotification->sendNotification($mixxer->title, 'It’s go time – your Mixxer just started!', $ownerToken, $data, $unreadCounts);
                    NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'It’s go time – your Mixxer just started!', 'mixxer', 'mixxer_start');
                    foreach ($mixxerUserIDs as $userId) {
                        $tokens = UserDevice::where('user_id', $userId)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'It’s go time – your Mixxer just started!', 'mixxer', 'mixxer_start');
                        $otherUser = User::find($userId);
                        $unreadCounts = UserUnreadCount::handle($otherUser);
                        $this->firebaseNotification->sendNotification($mixxer->title, 'It’s go time – your Mixxer just started!', $tokens, $data, $unreadCounts);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error processing Mixxer ID ' . $mixxer->id . ': ' . $e->getMessage());
            }
        }
    }
}
