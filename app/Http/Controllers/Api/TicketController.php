<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function create(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        } else {
            $ticket = new Ticket();
            $ticket->user_id = $user->uuid;
            $ticket->message = $request->message;
            $ticket->time = time();
            $ticket->save();

            $message = new Message();
            $message->from = $user->uuid;
            $message->type = 'text';
            $message->to = "";
            $message->ticket_id = $ticket->id;
            $message->message = $request->message;
            $message->time = time();
            $message->save();


            $defaultMessage = new Message();
            $defaultMessage->ticket_id = $ticket->id;
            $defaultMessage->to = $user->uuid;
            $defaultMessage->type = 'text';
            $defaultMessage->from = "";
            $defaultMessage->message = 'Hi,👋Thanks for your message. We ll get back to you within 24 hours.';
            $defaultMessage->time = time();
            $defaultMessage->save();



            $newticket = Ticket::find($ticket->id);
            // $karachiTime = Carbon::parse($Ticket->created_at)->timezone('Asia/Karachi');
            // $mail_details = [
            //     'subject' => 'Express',
            //     'body' => $request->message,
            //     'user' => $user->name,
            //     'category' => $cat->name,
            //     'time' => $karachiTime->format('Y-m-d H:i:s')
            // ];

            // Mail::to('khzrkhan0000@gmail.com')->send(new \App\Mail\TicketCreated());

            // Mail::to('zrzunair10@gmail.com')->send(new TicketCreated($mail_details));

            return response()->json([
                'status' => true,
                'action' => "Ticket Added",
                'data' =>  $newticket
            ]);
        }
    }

    public function list(Request $request, $status)
    {
        $user = User::find($request->user()->uuid);

        $tickets = Ticket::where('user_id', $user->uuid)->where('status', $status)->latest()->paginate(12);

        foreach ($tickets as $item) {
            $unread = Message::where('ticket_id', $item->id)->where('to', $user->uuid)->where('is_read', 0)->count();
            $item->unread_count = $unread;
        }
        return response()->json([
            'status' => true,
            'action' => "User Ticket",
            'data' => $tickets,
        ]);
    }

    public function close($ticket_id)
    {
        $Ticket = Ticket::find($ticket_id);
        if ($Ticket) {
            $Ticket->status = 1;
            $Ticket->save();
            return response()->json([
                'status' => true,
                'action' => "Ticket Close",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'action' => "No Tickets found",
            ]);
        }
    }
    public function conversation($id)
    {
        $ticket = Ticket::find($id);
        if ($ticket) {
            $messages = Message::where('ticket_id', $id)->get();
            $user = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where('uuid', $ticket->user_id)->first();
            foreach ($messages as $message) {
                $message->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' => "Conversation",
                'data' => $messages,
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "No Ticket found",
        ]);
    }
}
