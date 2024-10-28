<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Mixxer;
use App\Models\MixxerCategory;
use App\Models\MixxerJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;

class MixxerController extends Controller
{

    public function show($type, $loginId, $id)
    {
        $title = 'Mixxer Co';
        $description = 'Mixxer Co';
        $image = 'https://api.mixxerco.com/logo.jpg';
        if($type === 'mixxer') {
            $mixxer = Mixxer::findorFail($id);
            if($loginId === $mixxer->user_id)
                $title = 'Check out my Mixxer: ' . $mixxer->title;
            else {
                $title = 'Check out this Mixxer: ' . $mixxer->title;
                $joined = MixxerJoinRequest::where('user_id', $loginId)->where('mixxer_id', $mixxer->id)->first();
                if($joined) {
                    if($joined->status === 'accept')
                        $title = 'I’m in! Here’s the Mixxer: ' . $mixxer->title;
                }
            }
            $description = $mixxer->description;
            if($mixxer->cover === ''){
                $category = MixxerCategory::where('mixxer_id', $mixxer->id)->first();
                $findCategory = Category::find($category->category_id);
                $image = $findCategory->image;
            }else
                $image = $mixxer->cover;
        }
        elseif($type === 'profile'){
            $user = User::where('uuid', $id)->first();
            if($user){
                if($loginId === $user->uuid)
                    $title = 'Mixxer Profile: '.$user->first_name;
                else
                    $title = 'Mixxer Profile: '.$user->first_name.' '. $user->last_name;
                $description = $user->bio;
                if($user->profile_picture != '')
                    $image = $user->profile_picture;
            }
        }
        return view('mixxers.show', compact('title', 'description', 'image'));
    }
}
