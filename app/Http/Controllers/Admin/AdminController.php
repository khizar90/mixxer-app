<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FeatureRequest;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {

        // $verify = User::where('verify', 1)->count();


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
        $list = FeatureRequest::with(['user'])->latest()->paginate(30);
        return view('panel-v1.request.feature', compact('list'));
    }
}
