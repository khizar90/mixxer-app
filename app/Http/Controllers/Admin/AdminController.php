<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppFeedback;
use App\Models\AppSetting;
use App\Models\Faq;
use App\Models\FeatureRequest;
use App\Models\FeatureRequestNew;
use App\Models\Mixxer;
use App\Models\MixxerFeedback;
use App\Models\MixxerFriendlyCheckFeedback;
use App\Models\MixxerJoinRequest;
use App\Models\Notification;
use App\Models\ReportUser;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserSubscription;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    protected $firebaseNotification;

    public function __construct(FirebaseNotificationService $firebaseNotification)
    {
        $this->firebaseNotification = $firebaseNotification;
    }
    public function index()
    {
        $total = User::count();
        $todayActive = 0;

        $todayNew = User::whereDate('created_at', date('Y-m-d'))->count();
        $mainUsers = User::pluck('uuid');
        $loggedIn = UserDevice::whereIn('user_id', $mainUsers)->where('token', '!=', '')->distinct('user_id')->count();

        $iosTraffic = UserDevice::whereIn('user_id', $mainUsers)->where('device_name', 'ios')->count();
        $androidTraffic = UserDevice::whereIn('user_id', $mainUsers)->where('device_name', 'android')->count();

        return view('panel-v1.index', compact('todayActive', 'total', 'todayNew', 'mainUsers', 'loggedIn', 'iosTraffic', 'androidTraffic'));
    }

    public function users(Request $request)
    {


        $users = User::latest()->paginate(20);

        if ($request->ajax()) {

            $query = $request->input('query');

            $users = User::query();
            if ($query) {
                $users = $users->where('first_name', 'like', '%' . $query . '%')->orWhere('last_name', 'like', '%' . $query . '%')->orWhere('email', 'like', '%' . $query . '%');
            }
            $users = $users->latest()->Paginate(20);
            foreach ($users as $user) {
                $user->total_mixxers_hosted = Mixxer::where('user_id', $user->uuid)->count();
                $user->total_mixxers_attended = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->count();
                $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
                if ($is_subscribe) {
                    $user->is_subscribe = $is_subscribe->type;
                } else {
                    $user->is_subscribe = 'No subscription';
                }
            }

            return view('panel-v1.user.user-ajax', compact('users'));
        }

        foreach ($users as $user) {
            $user->total_mixxers_hosted = Mixxer::where('user_id', $user->uuid)->count();
            $user->total_mixxers_attended = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->count();
            $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
            if ($is_subscribe) {
                $user->is_subscribe = $is_subscribe->type;
            } else {
                $user->is_subscribe = 'No subscription';
            }
        }
        return view('panel-v1.user.index', compact('users'));
    }

    public function profile($user_id)
    {
        $user = User::find($user_id);
        return view('panel-v1.user.show', compact('user'));
    }


    public function exportCSV()
    {

        $users = User::select('first_name', 'last_name', 'email')->get();
        $columns = ['first_name', 'last_name', 'email'];

        $handle = fopen(storage_path('users.csv'), 'w');

        fputcsv($handle, $columns);

        foreach ($users->chunk(2000) as $chunk) {
            foreach ($chunk as $user) {
                fputcsv($handle, $user->toArray());
            }
        }

        fclose($handle);

        return response()->download(storage_path('users.csv'))->deleteFileAfterSend(true);
    }



    public function deleteUser($id)
    {
        $find = User::find($id);
        if ($find) {
            $find->delete();
        }
        return redirect()->back();
    }


    public function faqs()
    {
        $faqs = Faq::all();

        return view('panel-v1.faq', compact('faqs'));
    }

    public function deleteFaq($id)
    {
        $faq  = Faq::find($id);
        $faq->delete();
        return redirect()->back();
    }

    public function addFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back();
    }

    public function editFaq(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $faq = Faq::find($id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();
        return redirect()->back();
    }
    public function featureRequest()
    {
        $list = FeatureRequestNew::with(['user'])->latest()->paginate(30);
        return view('panel-v1.request.feature', compact('list'));
    }

    public function featureRequestDelete($id)
    {
        $find = FeatureRequestNew::find($id);
        if ($find) {
            $find->delete();
        }
        return redirect()->back();
    }

    public function featureRequestDetail($id)
    {
        $find = FeatureRequestNew::with(['user'])->where('id', $id)->first();
        return view('panel-v1.request.feature-detail', compact('find'));
    }

    public function appFeedback()
    {
        $list = AppFeedback::with(['user'])->latest()->paginate(30);
        return view('panel-v1.request.feedback', compact('list'));
    }

    public function appFeedbackDetail($id)
    {
        $find = AppFeedback::with(['user'])->where('id', $id)->first();
        return view('panel-v1.request.feedback-detail', compact('find'));
    }
    public function appFeedbackDelete($id)
    {
        $find = AppFeedback::find($id);
        if ($find) {
            $find->delete();
        }
        return redirect()->back();
    }

    public function reportedUser()
    {
        $list = ReportUser::with(['user'])->with(['report'])->latest()->paginate(50);
        return view('panel-v1.report.user', compact('list'));
    }

    public function reportUserDelete($id)
    {
        $find = ReportUser::find($id);
        if ($find) {
            $find->delete();
        }
        return redirect()->back();
    }

    public function reportedUserDetail($id)
    {
        $find = ReportUser::with(['user'])->with(['report'])->where('id', $id)->first();
        $media = explode(',', $find->media);
        if (!empty($find->media)) {
            $media = explode(',', $find->media);
        } else {
            $media = [];
        }
        $doc = explode(',', $find->doc);
        if (!empty($find->doc)) {
            $doc = explode(',', $find->doc);
        } else {
            $doc = [];
        }

        $find->media = $media;
        $find->doc = $doc;
        return view('panel-v1.report.user-detail', compact('find'));
    }

    public function mixxerFeedback()
    {
        $list = MixxerFeedback::with(['user'])->with(['mixxer'])->latest()->paginate(30);
        return view('panel-v1.request.mixxer-feedback', compact('list'));
    }

    public function mixxerFeedbackDEtail($id)
    {
        $find = MixxerFeedback::with(['user'])->with(['mixxer'])->where('id', $id)->first();
        return view('panel-v1.request.mixxer-feedback-detail', compact('find'));
    }

    public function mixxerCheckInFeedback(){
        $list = MixxerFriendlyCheckFeedback::with(['user'])->with(['mixxer'])->latest()->paginate(30);
        return view('panel-v1.request.mixxer-check-in-feedback', compact('list'));
    }


    public function version($status)
    {
        if ($status == 'android') {
            return view('panel-v1.app-version.android', compact('status'));
        }
        if ($status == 'iOS') {
            return view('panel-v1.app-version.iOS', compact('status'));
        }
    }

    public function versionSave($type)
    {
        if ($type == 'android') {
            foreach ($_POST as $key => $value) {
                if ($key == "_token")
                    continue;

                $data = array();
                $data['value'] = $value;
                $data['updated_at'] = Carbon::now();

                if (AppSetting::where('name', $key)->exists()) {
                    AppSetting::where('name', $key)->update($data);
                } else {
                    $data['name'] = $key;
                    $data['created_at'] = Carbon::now();
                    AppSetting::insert($data);
                }
            }
            return redirect()->back()->with('message', 'Android version updated!');
        }
        if ($type == 'iOS') {
            foreach ($_POST as $key => $value) {
                if ($key == "_token")
                    continue;

                $data = array();
                $data['value'] = $value;
                $data['updated_at'] = Carbon::now();

                if (AppSetting::where('name', $key)->exists()) {
                    AppSetting::where('name', $key)->update($data);
                } else {
                    $data['name'] = $key;
                    $data['created_at'] = Carbon::now();
                    AppSetting::insert($data);
                }
            }
            return redirect()->back()->with('message', 'IOS version updated!');
        }
    }

    public function sendNotification(){
        $tokens = [
            'c5MeTHim1UF_nZocOpGI3e:APA91bGQ2QDd3kVaQ_IVsjFS7JpmYQkTCrZoHw6lAleCy0lSW2M03ixCXjW25ISOUGDivljoDQIEjU8I1A0iqljWma1GiYEwE1MzDGx97zXevKgG1yE6O7TEMX7OqoAMiBX04oFFfdkY',
            'eJNLxSr_P0bXs4DaBBV-Ur:APA91bF4T-AKlizmx8hv94G8HD30md7_uoFIIv0SGUBZBvUHwSleeZAOBVtTpBx0bPiv--4oVdxqOy0a_5I-458EkSdHmqzN3XueGVanut_uWbaNp2uLwtF8JYci5lskYAHdFluDGlPv',
            'f18OeHNjWU2_urJAN-H3nI:APA91bEB4vPZpKxEezjgkUcDev7YFUydLYYzbsTY_qOCnSqJ7VPwlIjsXk5HlFxiuL8iFvGHfdRUM_q567z3V-CwGewLyreEMhwMc4EG5XkGydbdrhJ06f5TjyAz0Q2GiYVPq7hLgTbd',
            'cextKHONIkDvs0I-7CaKww:APA91bEvbwfTNenBc3Y2q0YP5cHOHnqj-vq738baHI5QHWBEAJwJCTmq24LhrwH-kRbGLoShY-V_5HTgbsZWG4Wn9Yql2pJc58ArGON5zu8Iq0qmUhQ-chyk6uhPBLusZkC5HORqkaCV'
        ];
        $data = [
            'data_id' => 0,
            'type' => 'test',
        ];

        $this->firebaseNotification->sendNotification('Mixxer Support',  'This is test Notification', $tokens, $data, 1);
        return 'Notification Send';
    }
}
