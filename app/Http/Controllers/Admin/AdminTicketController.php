<?php

namespace App\Http\Controllers\Admin;

use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\NotificationAllow;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use stdClass;

class AdminTicketController extends Controller
{
    protected $firebaseNotification;

    public function __construct(FirebaseNotificationService $firebaseNotification)
    {
        $this->firebaseNotification = $firebaseNotification;
    }
    public function ticket($status)
    {

        if ($status == 'active') {
            $reports = Ticket::where('status', 0)->paginate(30);

            foreach ($reports as $report) {
                $user = User::where('uuid', $report->user_id)->first();
                $report->user = $user;
            }
        } else {
            $reports = Ticket::where('status', 1)->paginate(30);

            foreach ($reports as $report) {
                $user = User::where('uuid', $report->user_id)->first();
                $report->user = $user;
            }
        }
        return view('panel-v1.ticket.index', compact('reports', 'status'));
    }

    public function messages($status, $ticket_id)
    {


        $conversation = Message::where('ticket_id', $ticket_id)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($conversation as $message) {
            $messageTime = Carbon::createFromTimestamp($message->time, 'Asia/Karachi');
            $formattedTime = '';

            if ($messageTime->isToday()) {
                $formattedTime = 'Today, ' . $messageTime->format('g:i A');
            } elseif ($messageTime->isYesterday()) {
                $formattedTime = 'Yesterday, ' . $messageTime->format('g:i A');
            } else {
                $formattedTime = $messageTime->format('d M Y, g:i A');
            }
            $message->time = $formattedTime;
        }

        $ticket = Ticket::find($ticket_id);
        $findUser = User::where('uuid', $ticket->user_id)->first();
        return view('panel-v1.ticket.show', compact('conversation', 'findUser', 'ticket'));
    }

    public function closeTicket($report_id)
    {
        $obj = new stdClass();
        $report = Ticket::find($report_id);
        if ($report) {
            $report->status = 1;
            $report->save();
            return redirect()->route('dashboard-ticket-ticket', 'active');
        }
    }


    public function sendMessage(Request $request)
    {
        $userIDs = NotificationAllow::where('is_allow',0)->pluck('user_id');

        $ticket = Ticket::find($request->ticket_id);
        $message = new Message();
        $message->ticket_id = $request->ticket_id;
        $message->to = $request->user_id;
        $message->from = '';
        $message->message = $request->message ?: '';
        $message->type = 'text';
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/chat', $file);
            $path = Storage::disk('s3')->url($path);
            $message->attachment = $path;
            $message->type = 'image';
        }
        $message->time = time();
        $message->save();
        // NewNotification::handle($user->uuid, $friend_id, 0, 'has accepted your friend request', 'normal', 'accept_request');
        $tokens = UserDevice::where('user_id', $request->user_id)->whereNotIn('user_id',$userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

        $data = [
            'data_id' => $request->ticket_id,
            'type' => 'ticket_chat',
        ];
        $user = User::find($request->user_id);
        $unreadCounts = UserUnreadCount::handle($user);
        if ($message->message) {
            $this->firebaseNotification->sendNotification('Mixxer Support',  'Admin: ' . $message->message, $tokens, $data, $unreadCounts);
        } else {
            $this->firebaseNotification->sendNotification('Mixxer Support',  'You have received a new image. Tap to view.', $tokens, $data, $unreadCounts);
        }



        return response()->json($message);
    }
}
