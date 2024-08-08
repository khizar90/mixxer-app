<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
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
        Log::info('start');
        $current_time = time();
        $mixxers = Mixxer::where('status', 0)->where('start_timestamp', '<=', $current_time)->get();
        foreach ($mixxers as $mixxer) {
            $mixxer->status = 1;
            $end_time = Carbon::createFromTimestamp($mixxer->end_timestamp);
            $new_end_time = $end_time->addDay();
            $endDate = $new_end_time->toDateString();
            $obj = new \stdClass();
            $obj->endDate = $endDate;
            $obj->isLocked = false;
            $obj->roomMode = 'group';
            $obj->templateType = 'viewerMode';
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
            $viewerUrl = $data->viewerRoomUrl;

            $mixxer->host_url = $data->hostRoomUrl;
            $mixxer->viewer_url = $viewerUrl;
            $mixxer->save();

            $owner = User::find($mixxer->user_id);
            $ownerToken = UserDevice::where('user_id', $owner->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');
            $tokens = UserDevice::whereIn('user_id', $mixxerUserIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $combinedTokens = array_merge($tokens, $ownerToken);

            $data = [
                'data_id' => $mixxer->id,
                'type' => 'mixxer_start',
            ];

            NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'Just a friendly reminder that mixxer is about to begin', 'mixxer', 'mixxer_start');
            foreach ($mixxerUserIDs as $userId) {
                NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'Just a friendly reminder that mixxer is about to begin', 'mixxer', 'mixxer_start');
            }
            $this->firebaseNotification->sendNotification($mixxer->title, 'Just a friendly reminder that mixxer is about to begin', $combinedTokens, $data, 1);
        }
        Log::info('end');
    }
}
