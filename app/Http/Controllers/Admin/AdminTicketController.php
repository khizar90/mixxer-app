<?php

namespace App\Http\Controllers\Admin;

use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
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
        $ticket = Ticket::find($request->ticket_id);
        $message = new Message();
        $message->ticket_id = $request->ticket_id;
        $message->to = $request->user_id;
        $message->from = '';
        $message->message = $request->message;
        $message->type = 'text';
        $message->time = time();
        $message->save();
        // NewNotification::handle($user->uuid, $friend_id, 0, 'has accepted your friend request', 'normal', 'accept_request');
        $tokens = UserDevice::where('user_id', $request->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

        $data = [
            'data_id' => $request->ticket_id,
            'type' => 'ticket_chat',
        ];

        $message = $message->message ? : 'sent a attachment!';

        $this->firebaseNotification->sendNotification('Mixxer Support',  $message , $tokens, $data, 1);

        return response()->json($message);
    }
}
