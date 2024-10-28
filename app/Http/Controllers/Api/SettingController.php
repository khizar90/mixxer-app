<?php

namespace App\Http\Controllers\Api;

use App\Actions\AppVersions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Mail\ContactUs;
use App\Models\Category;
use App\Models\Faq;
use App\Models\NotificationAllow;
use App\Models\User;
use App\Models\UserInterest;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use stdClass;

class SettingController extends Controller
{
    public function categories(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        $categories = Category::select('id', 'name', 'image')->where('type', $type)->get();
        foreach ($categories as $item) {
            $check = UserInterest::where('user_id', $user->uuid)->where('category_id', $item->id)->first();
            if ($check) {
                $item->is_added = true;
            } else {
                $item->is_added = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' => "Category List",
            'data' => $categories
        ]);
    }
    public function listCategories(Request $request, $type)
    {
        $categories = Category::select('id', 'name', 'image')->where('type', $type)->orderBy('name', 'asc')->get();
        foreach ($categories as $item) {
            $item->is_added = false;
        }
        return response()->json([
            'status' => true,
            'action' => "Category List",
            'data' => $categories
        ]);
    }

    public function faqs()
    {
        $list = Faq::all();
        return response()->json([
            'status' => true,
            'action' =>  'Faqs',
            'data' => $list
        ]);
    }

    public function splash($user_id = null)
    {
        $obj = new stdClass();
        $obj1 = new stdClass();

        // // $interest = Category::select('id', 'name','image')->where('type', 'interest')->get();
        // $obj->events_category = $events;
        $appVersions = AppVersions::handle();

        if ($user_id != null) {
            $user = User::find($user_id);
            if ($user) {
                $user->token = "";
                $obj->user = $user;
                $interest = UserInterest::where('user_id', $user->uuid)->first();
                if ($interest) {
                    $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                    $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
                    foreach ($categories as $item) {
                        $item->is_added = true;
                    }
                    $obj->user->interest = $categories;
                } else {
                    $obj->user->interest = [];
                }
                $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
                if ($is_subscribe) {
                    $obj->user->is_subscribe = true;
                } else {
                    $obj->user->is_subscribe = false;
                }
                $allowNotify = NotificationAllow::where('user_id', $user->uuid)->first();
                if ($allowNotify) {
                    if ($allowNotify->is_allow == 1) {
                        $obj->user->is_notify_allow = true;
                    } else {
                        $obj->user->is_notify_allow = false;
                    }
                } else {
                    $obj->user->is_notify_allow = false;
                }

                $is_delete = false;
            } else {
                $is_delete = true;
                $obj->user = $obj1;
            }
        } else {
            $obj->user = $obj1;
            $is_delete = false;
        }

        return response()->json([
            'status' => true,
            'action' => "Splash",
            'is_delete' => $is_delete,
            'app_versions' => $appVersions,
            'data' => $obj,
        ]);
    }

    public function contact(ContactRequest $request)
    {
        Mail::to('contactus@mixxerco.com')->send(new ContactUs($request->name, $request->email, $request->message));
        return response()->json([
            'status' => true,
        ]);
    }
}
