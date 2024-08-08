<?php

namespace App\Console\Commands;

use App\Actions\NewNotification;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
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
        Log::info('Mixxer Change Status Start');
        $current_timestamp = time();


        $mixxers = Mixxer::where('end_timestamp', '<', $current_timestamp)->where('status', '!=', 2)->get();
        foreach ($mixxers as $mixxer) {
            $owner = User::find($mixxer->user_id);
            $ownerToken = UserDevice::where('user_id', $owner->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->pluck('user_id');
            $tokens = UserDevice::whereIn('user_id', $mixxerUserIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $combinedTokens = array_merge($tokens, $ownerToken);

            $data = [
                'data_id' => $mixxer->id,
                'type' => 'mixxer_complete',
            ];
            NewNotification::handle($owner->uuid, $mixxer->user_id, $mixxer->id, 'Just a friendly reminder that mixxer completed', 'mixxer', 'mixxer_complete');
            foreach ($mixxerUserIDs as $userId) {
                NewNotification::handle($userId, $mixxer->user_id, $mixxer->id, 'Just a friendly reminder that mixxer completed', 'mixxer', 'mixxer_complete');
            }

            $this->firebaseNotification->sendNotification($mixxer->title, 'Just a friendly reminder that mixxer completed', $combinedTokens, $data, 1);
        }
        Mixxer::where('end_timestamp', '<', $current_timestamp)->where('status', '!=', 2)->update(['status' => 2]);

        Log::info('Mixxer Change Status End');
    }
}
