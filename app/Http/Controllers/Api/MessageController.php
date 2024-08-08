<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;
use stdClass;

class MessageController extends Controller
{
    protected $firebaseNotification;

    public function __construct(FirebaseNotificationService $firebaseNotification)
    {
        $this->firebaseNotification = $firebaseNotification;
    }

    public function send(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($request->ticket_id) {
            $validator = Validator::make($request->all(), [
                'ticket_id' => "required|exists:tickets,id",
                'type' => 'required',
                'message' => 'required'
            ]);
        } elseif ($request->mixxer_id) {
            $validator = Validator::make($request->all(), [
                'mixxer_id' => "required|exists:mixxers,id",
                'type' => 'required',
                'message' => 'required_without:attachment'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'to' => "required|exists:users,uuid",
                'type' => 'required',
                'message' => 'required_without:attachment',
            ]);
        }

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }



        if ($request->ticket_id) {
            $chat_message = new Message();
            $chat_message->from = $user->uuid;
            $chat_message->type = $request->type;
            $chat_message->to = 0;
            $chat_message->ticket_id = $request->ticket_id;
            $chat_message->message = $request->message;
            $chat_message->time = time();
            $chat_message->save();
            Message::where('ticket_id', $request->ticket_id)->where('is_read', 0)->update(['is_read' => 1]);

            $chat_message = Message::find($chat_message->id);
        } elseif ($request->mixxer_id) {
            $chat_message = new Message();

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/chat', $file);

                // $path = Storage::disk('s3')->putFile('user/' . $request->user->uuid . '/profile', $file);
                // $path = Storage::disk('s3')->url($path);

                $chat_message->attachment = '/uploads/' . $path;
            }

            $chat_message->from = $user->uuid;
            $chat_message->to = 0;
            $chat_message->mixxer_id = $request->mixxer_id;
            $chat_message->type = $request->type;
            $chat_message->message = $request->message ?: '';
            $chat_message->time = time();

            $chat_message->save();
            $chat_message = Message::find($chat_message->id);
            $mixxer = Mixxer::find($request->mixxer_id);
            $ownerToken = [];
            if ($user->uuid != $mixxer->user_id) {
                $ownerToken = UserDevice::where('user_id', $mixxer->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            }

            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $request->mixxer_id)->where('status', 'accept')->pluck('user_id');
            $tokens = UserDevice::whereIn('user_id', $mixxerUserIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $combinedTokens = array_merge($tokens, $ownerToken);

            $data = [
                'data_id' => $request->mixxer_id,
                'type' => 'mixxer_chat',
            ];

            $message = $chat_message->message ? : 'sent a attachment!';

            $this->firebaseNotification->sendNotification($mixxer->title,$user->first_name . ': ' . $message, $combinedTokens, $data,1);

            $pusher = new Pusher('c41deb84f109a656ec85', 'bbc198c5bddd7c335ffc', '1824988', [
                'cluster' => 'us3',
                'useTLS' => true,
            ]);

            $pusher->trigger($request->mixxer_id, 'new-message', $chat_message);
        } else {
            $chat_message = new Message();

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/chat', $file);

                // $path = Storage::disk('s3')->putFile('user/' . $request->user->uuid . '/profile', $file);
                // $path = Storage::disk('s3')->url($path);

                $chat_message->attachment = '/uploads/' . $path;
            }

            $chat_message->from = $user->uuid;
            $chat_message->to = $request->to;
            $chat_message->type = $request->type;
            $chat_message->message = $request->message ?: '';
            $chat_message->time = time();
            $find = Message::where('from_to', $user->uuid . '-' . $request->to)->orWhere('from_to', $request->to . '-' . $user->uuid)->first();
            $channel = '';
            if ($find) {
                $channel = $find->from_to;
                $chat_message->from_to = $find->from_to;
                // Message::where('from_to', $chat_message->from_to)->where('to', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);
            } else {
                $channel = '';
                $chat_message->from_to = $user->uuid . '-' . $request->to;
            }
            $chat_message->save();
            $chat_message = Message::find($chat_message->id);

            $find = Message::find($chat_message->id);
            $tokens = UserDevice::where('user_id', $request->to)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $data = [
                'data_id' => $user->uuid,
                'type' => 'simple_chat',
            ];
            $message = $chat_message->message ? : 'sent a attachment!';

            $this->firebaseNotification->sendNotification($user->first_name . ' ' . $user->last_name,$message, $tokens, $data,1);

            $pusher = new Pusher('c41deb84f109a656ec85', 'bbc198c5bddd7c335ffc', '1824988', [
                'cluster' => 'us3',
                'useTLS' => true,
            ]);

            $pusher->trigger($chat_message->from_to, 'new-message', $chat_message);
        }



        return response()->json([
            'status' => true,
            'action' => "Message send",
            'data' => $chat_message
        ]);
    }

    public function conversation(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);


        // Message::where('from', $to_id)->where('to', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);

        $messages = Message::where('ticket_id', 0)->where('mixxer_id', 0)->where('from_to', $user->uuid . '-' . $to_id)->orWhere('from_to', $to_id . '-' . $user->uuid)->latest()->Paginate(5000);
        $user1 = User::select('uuid', 'first_name', 'last_name', 'profile_picture')->where('uuid', $to_id)->first();
        $friend = FriendRequest::where('user_id', $user->uuid)->where('friend_id', $user1->uuid)->first();
        $friend1 = FriendRequest::where('friend_id', $user->uuid)->where('user_id', $user1->uuid)->first();
        if ($friend) {
            if ($friend->status == 'pending') {
                $user1->is_friend = 'cancel_request';
            } elseif ($friend->status == 'accept') {
                $user1->is_friend = 'friend';
            } else {
                $user1->is_friend = 'add_friend';
            }
        } elseif ($friend1) {
            if ($friend1->status == 'pending') {
                $user1->is_friend = 'accept_request';
            } elseif ($friend1->status == 'accept') {
                $user1->is_friend = 'friend';
            } else {
                $user1->is_friend = 'add_friend';
            }
        } else {
            $user1->is_friend = 'add_friend';
        }
        $friendIds = FriendRequest::where('user_id', $user1->uuid)->where('status', 'accept')->pluck('friend_id');
        $friendIds1 = FriendRequest::where('friend_id', $user1->uuid)->where('status', 'accept')->pluck('user_id');
        $friendIds = $friendIds->merge($friendIds1);
        $total_friend = count($friendIds);
        $user1->total_friend = $total_friend;
        $user1->total_mixxers_hosted = Mixxer::where('user_id', $user1->uuid)->count();
        $user1->total_mixxers_attended =  MixxerJoinRequest::where('user_id', $user1->uuid)->where('status', 'accept')->count();
        $userMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $user1Mixxer = MixxerJoinRequest::where('user_id', $user1->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $commonMixxers = $userMixxer->intersect($user1Mixxer);
        $commonMixxerCount = $commonMixxers->count();
        $user1->total_mixxers_together = $commonMixxerCount;

        return response()->json([
            'status' => true,
            'action' =>  'Conversation',
            'user' => $user1,
            'data' => $messages,
        ]);
    }

    public function inbox(Request $request)
    {

        $user = User::find($request->user()->uuid);
        $get = Message::select('from_to')->where('ticket_id', 0)->where('mixxer_id', 0)->where('from', $user->uuid)->orWhere('to', $user->uuid)->where('ticket_id', 0)->where('mixxer_id', 0)->groupBy('from_to')->pluck('from_to');
        $arr = [];
        foreach ($get as $item) {

            $message = Message::where('from_to', $item)->latest()->first();
            if ($message) {
                if ($message->from == $user->uuid) {
                    $user1 = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where('uuid', $message->to)->first();
                }
                if ($message->to == $user->uuid) {
                    $user1 = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where('uuid', $message->from)->first();
                }
            }
            $unread_count = Message::where('from_to', $item)->where('to', $user->uuid)->where('is_read', 0)->count();
            $obj = new stdClass();
            $obj->message = $message->message;
            $obj->time = $message->time;
            $obj->type = $message->type;
            $obj->is_read = $message->is_read;
            $obj->from = $message->from;
            $obj->from_to = $message->from_to;
            $obj->user = $user1;
            $obj->unread_count = $unread_count;
            $arr[] = $obj;
        }

        $sorted = collect($arr)->sortByDesc('time');

        // ---COMMENTED FOR FUTURE USE IF NEEDED FOR PAGINATION---
        // $sorted = $sorted->forPage($request->page, 20);

        $arr1 = [];
        $count = 0;
        foreach ($sorted as $item) {
            $arr1[] = $item;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Inbox',
            'data' => $arr1
        ]);
    }

    public function messageRead(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);
        Message::where('from', $to_id)->where('to', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);
        
        // Message::where('from_to', $channel)->where('to', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);
        return response(['status' => true, 'action' => 'Messages read']);
    }
}
