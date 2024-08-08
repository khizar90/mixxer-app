<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Models\Mixxer;
use App\Models\MixxerFriendlyCheck;
use App\Models\MixxerJoinRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
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
        Log::info('Friendly check start');

        $current_time = time();



        $mixxers = Mixxer::where('status', 1)
            ->where('start_timestamp', '<=', $current_time)
            ->get();

        foreach ($mixxers as $mixxer) {
            if ($mixxer->start_timestamp <= $mixxer->start_timestamp + 600) {
                $owner = User::find($mixxer->user_id);
                $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');

                $ownerCheck = MixxerFriendlyCheck::where('user_id', $owner->uuid)->where('mixxer_id', $mixxer->id)->first();
                if (!$ownerCheck) {
                    log::info('send to owner');
                    $ownerToken = UserDevice::where('user_id', $owner->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $data = [
                        'data_id' => $mixxer->id,
                        'type' => 'mixxer_friendly_check',
                    ];
                    $this->firebaseNotification->sendNotification($mixxer->title, 'Is everything going fine?', $ownerToken, $data, 1);
                }
                foreach ($mixxerUserIDs as $userID) {
                    $check = MixxerFriendlyCheck::where('user_id', $userID)->where('mixxer_id', $mixxer->id)->first();
                    if (!$check) {
                        $tokens = UserDevice::where('user_id', $userID)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                        $data = [
                            'data_id' => $mixxer->id,
                            'type' => 'mixxer_friendly_check',
                        ];
                        $this->firebaseNotification->sendNotification($mixxer->title, 'Is everything going fine?', $tokens, $data, 1);
                    }
                }
            }
        }

        Log::info('Friendly check end');
    }
}
