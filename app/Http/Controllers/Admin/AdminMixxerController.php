<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\MixxerMedia;
use App\Models\User;
use Illuminate\Http\Request;

class AdminMixxerController extends Controller
{
    public function analytics(){
        $total = Mixxer::count();
        $upComing = Mixxer::where('status','!=',2)->count();
        $complete = Mixxer::where('status',2)->count();
        return view('panel-v1.mixxer.analytics',compact('total','upComing','complete'));
    }
    public function list($type){
        if($type == 'up-coming'){
            $list = Mixxer::select('id','title','cover','status')->where('status','!=',2)->latest()->paginate(32);
        }
        if($type == 'complete'){
            $list = Mixxer::select('id','title','cover','status')->where('status',2)->latest()->paginate(32);
        }
        return view('panel-v1.mixxer.list',compact('list'));

    }

    public function delete($id){
        $find = Mixxer::find($id);
        if($find){
            $find->delete();
        }
        return response()->json(true);
    }

    public function detail($mixxer_id){
        $mixxer = Mixxer::with(['user'])->where('id',$mixxer_id)->first();
        $categories = explode(',', $mixxer->categories);
        $category = Category::select('id', 'name', 'image')->whereIn('id', $categories)->get();
        $mixxer->categories = $category;
        $photos = MixxerMedia::where('mixxer_id', $mixxer->id)->where('type', 'image')->get();
        $doc = MixxerMedia::where('mixxer_id', $mixxer->id)->where('type', 'doc')->get();
        $mixxer->photos = $photos;
        $mixxer->doc = $doc;
        $mixxer->participant_count = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->count();

        $userIds = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->pluck('user_id');
        $users = User::whereIn('uuid',$userIds)->get();

        return view('panel-v1.mixxer.detail',compact('mixxer','users'));
    }
}
