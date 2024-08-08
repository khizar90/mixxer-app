<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function create(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'type' => "required",
            'start' => 'required',
            'end' => 'required'
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = new UserSubscription();
        $create->user_id = $user->uuid;
        $create->type = $request->type;
        $create->start = $request->start;
        $create->end = $request->end;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Subscription Purchased',
        ]);
    }
}
