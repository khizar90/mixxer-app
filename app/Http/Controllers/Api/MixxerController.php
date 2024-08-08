<?php

namespace App\Http\Controllers\Api;

use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Mixxer;
use App\Models\MixxerCategory;
use App\Models\MixxerFriendlyCheck;
use App\Models\MixxerJoinRequest;
use App\Models\MixxerMedia;
use App\Models\Notification;
use App\Models\SaveMixxer;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserInterest;
use App\Models\UserSubscription;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Mix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use stdClass;

class MixxerController extends Controller
{
    protected $firebaseNotification;

    public function __construct(FirebaseNotificationService $firebaseNotification)
    {
        $this->firebaseNotification = $firebaseNotification;
    }

    public function create(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categories' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'start_timestamp' => 'required',
            'end_time' => 'required',
            'end_timestamp' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $categoriesIds = explode(',', $request->categories);


        $create = new Mixxer();
        $create->user_id = $user->uuid;
        $create->title = $request->title;
        $create->categories = $request->categories;
        $create->start_date = $request->start_date;
        $create->start_time = $request->start_time;
        $create->start_timestamp = $request->start_timestamp;
        $create->type = $request->type;
        $create->description = $request->description;
        $create->is_all_day = $request->is_all_day ?: 0;
        $create->end_time = $request->end_time;
        $create->end_timestamp = $request->end_timestamp;
        $create->limit_audience = $request->limit_audience ?: '';
        $create->gender = $request->gender ?: '';
        $create->age_limit = $request->age_limit ?: '';
        $create->location = $request->location ?: '';
        $create->lat = $request->lat ?: '';
        $create->lng = $request->lng ?: '';
        $create->registration_link = $request->registration_link ?: '';
        $create->website_link = $request->website_link ?: '';
        $create->address = $request->address ?: '';
        $create->description = $request->description ?: '';
        $create->doc = '';
        $create->photos = '';

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $path = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/cover', $file);
            $create->cover = '/uploads/' . $path;

            $imagePath = public_path('/uploads/' . $path);
            list($width, $height) = getimagesize($imagePath);

            $size = $width / $height;
            $size = number_format($size, 2);
            $create->cover_size = $size;
        }
        $create->save();

        foreach ($categoriesIds as $category) {
            $post_category = new MixxerCategory();
            $post_category->mixxer_id = $create->id;
            $post_category->category_id = $category;
            $post_category->save();
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/photos', $file);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = '/uploads/' . $path;
                $photo->type = 'image';
                $photo->save();
            }
        }

        if ($request->hasFile('doc')) {
            $files = $request->file('doc');
            foreach ($files as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/doc', $file);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = '/uploads/' . $path;
                $photo->type = 'doc';
                $photo->save();
            }
        }

        $new = Mixxer::find($create->id);

        // $chat_message = new Message();
        // $chat_message->from = $user->uuid;
        // $chat_message->to = 0;
        // $chat_message->mixxer_id = $new->id;
        // $chat_message->type = 'create';
        // $chat_message->message = 'has created this mixxer';
        // $chat_message->time = time();
        // $chat_message->save();

        // $joinRequest = new MixxerJoinRequest();
        // $joinRequest->user_id = $user->uuid;
        // $joinRequest->mixxer_id = $new->id;
        // $joinRequest->status = 'accept';
        // $joinRequest->save();

        return response()->json([
            'status' => true,
            'action' =>  'Mixxer Added',
            'data' => $new
        ]);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categories' => 'required',
            'start_date' => 'required',
            'start_time' => 'required',
            'start_timestamp' => 'required',
            'end_time' => 'required',
            'end_timestamp' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $categoriesIds = explode(',', $request->categories);

        $user = User::find($request->user()->uuid);
        $create = Mixxer::find($request->mixxer_id);
        $create->title = $request->title;
        $create->categories = $request->categories;
        $create->start_date = $request->start_date;
        $create->start_time = $request->start_time;
        $create->start_timestamp = $request->start_timestamp;
        $create->type = $request->type;
        $create->description = $request->description;
        $create->is_all_day = $request->is_all_day ?: 0;
        $create->end_time = $request->end_time;
        $create->end_timestamp = $request->end_timestamp;
        $create->limit_audience = $request->limit_audience ?: '';
        $create->gender = $request->gender ?: '';
        $create->age_limit = $request->age_limit ?: '';
        $create->location = $request->location ?: '';
        $create->lat = $request->lat ?: '';
        $create->lng = $request->lng ?: '';
        $create->registration_link = $request->registration_link ?: '';
        $create->website_link = $request->website_link ?: '';
        $create->address = $request->address ?: '';
        $create->description = $request->description ?: '';


        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $path = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/cover', $file);
            $create->cover = '/uploads/' . $path;
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/photos', $file);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = '/uploads/' . $path;
                $photo->type = 'image';
                $photo->save();
            }
        }

        if ($request->hasFile('doc')) {
            $files = $request->file('doc');
            foreach ($files as $file) {
                $path = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/doc', $file);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = '/uploads/' . $path;
                $photo->type = 'doc';
                $photo->save();
            }
        }


        MixxerCategory::where('mixxer_id', $request->mixxer_id)->delete();


        foreach ($categoriesIds as $category) {
            $post_category = new MixxerCategory();
            $post_category->mixxer_id = $create->id;
            $post_category->category_id = $category;
            $post_category->save();
        }

        $create->save();

        $find = Mixxer::find($create->id);
        $categories = explode(',', $find->categories);
        $category = Category::select('id', 'name', 'image')->whereIn('id', $categories)->get();
        $find->categories = $category;
        $photos = MixxerMedia::where('mixxer_id', $find->id)->where('type', 'image')->get();
        $doc = MixxerMedia::where('mixxer_id', $find->id)->where('type', 'doc')->get();
        $find->photos = $photos;
        $find->doc = $doc;
        return response()->json([
            'status' => true,
            'action' =>  'Mixxer Edit',
            'data' => $find
        ]);
    }

    public function removeCover($mixxer_id)
    {
        $mixxer = Mixxer::find($mixxer_id);
        if ($mixxer) {
            $mixxer->cover = '';
            $mixxer->save();
            return response()->json([
                'status' => true,
                'action' =>  'Cover Remove',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'Mixxer not found',
        ]);
    }

    public function save(Request $request, $mixxer_id)
    {
        $user  = User::find($request->user()->uuid);
        $check = SaveMixxer::where('mixxer_id', $mixxer_id)->where('user_id', $user->uuid)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Mixxer Unsaved',
            ]);
        }

        $create  = new SaveMixxer();
        $create->mixxer_id = $mixxer_id;
        $create->user_id = $user->uuid;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Mixxer Saved',
        ]);
    }

    public function home(Request $request)
    {

        $user = User::find($request->user()->uuid);
        $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
        $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

        $userIds = UserSubscription::where('type', '!=', 'free')->pluck('user_id');

        $feature_mixxer  = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
            'age_limit',
            'gender',
            'categories',
            'start_date',
            'is_all_day',
            'start_time',
            'end_time',
            'location',
            'lat',
            'lng',
            'address'
        )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereIn('user_id', $userIds)->orderby('id', 'desc')
            ->where('status', '!=', 2);



        if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
            $feature_mixxer->where('start_date', $request->start_date);
        }

        if ($request->has('start_date') && $request->has('to_date') && $request->start_date != "" && $request->to_date != "") {
            $feature_mixxer->whereBetween('start_date', [$request->start_date, $request->to_date]);
        }
        if ($request->has('gender') && $request->gender != "") {
            $feature_mixxer->where('gender', $request->gender);
        }
        if ($request->has('min_age') && $request->has('max_age') && $request->min_age != "" && $request->max_age != "") {
            $minAge = $request->input('min_age');
            $maxAge = $request->input('max_age');

            $feature_mixxer->whereBetween('age_limit', [$minAge, $maxAge]);
        }
        if ($request->has('categories') && $request->categories != "") {
            $categories = explode(',', $request->categories);
            $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
            $feature_mixxer->whereIn('id', $mixxer_ids);
        }

        if ($request->has('lat') && $request->has('lng') && $request->has('radius') && $request->lat != "" && $request->lng != "" && $request->radius != "") {
            $userLat = $request->lat;
            $userLng = $request->lng;
            $feature_mixxer->select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
                ->having('distance', '<=', $request->radius)
                ->orderBy('distance');
        }

        $feature_mixxer = $feature_mixxer->limit(12)->get();
        foreach ($feature_mixxer as $item) {
            $categorieIds = explode(',', $item->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item->categories = $categories;
        }


        if ($request->has('lat') && $request->has('lng')) {
            $userLat = $request->lat;
            $userLng = $request->lng;
            $nearby_mixxer = Mixxer::select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
                // ->having('distance', '<=', $request->radius)
                ->orderBy('distance')
                ->where('user_id', '!=', $user->uuid)
                ->where('status', '!=', 2);

            if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
                $nearby_mixxer->where('start_date', $request->start_date);
            }
            if ($request->has('start_date') && $request->has('to_date')  && $request->start_date != ""  && $request->to_date != "") {
                $nearby_mixxer->whereBetween('start_date', [$request->start_date, $request->to_date]);
            }
            if ($request->has('gender') && $request->gender != "") {
                $nearby_mixxer->where('gender', $request->gender);
            }
            if ($request->has('min_age') && $request->has('max_age') && $request->min_age != "" && $request->max_age != "") {
                $minAge = $request->input('min_age');
                $maxAge = $request->input('max_age');

                $nearby_mixxer->whereBetween('age_limit', [$minAge, $maxAge]);
            }
            if ($request->has('categories') && $request->categories != "") {
                $categories = explode(',', $request->categories);
                $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
                $nearby_mixxer->whereIn('id', $mixxer_ids);
            }

            if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
                $userLat = $request->lat;
                $userLng = $request->lng;
                $nearby_mixxer->select(
                    'id',
                    'user_id',
                    'cover',
                    'title',
                    'age_limit',
                    'gender',
                    'categories',
                    'start_date',
                    'is_all_day',
                    'start_time',
                    'end_time',
                    'location',
                    'lat',
                    'lng',
                    'address',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                    ->having('distance', '<=', $request->radius)
                    ->orderBy('distance');
            }
            $nearby_mixxer = $nearby_mixxer->paginate(12);

            foreach ($nearby_mixxer as $item1) {
                $categorieIds = explode(',', $item1->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item1->categories = $categories;
            }
        } else {

            $nearby_mixxer = Mixxer::select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->orderBy('id', 'desc')
                ->where('status', '!=', 2);

            if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
                $nearby_mixxer->where('start_date', $request->start_date);
            }
            if ($request->has('start_date') && $request->has('to_date') && $request->start_date != "" && $request->to_date != "") {
                $nearby_mixxer->whereBetween('start_date', [$request->start_date, $request->to_date]);
            }
            if ($request->has('gender') && $request->gender != "") {
                $nearby_mixxer->where('gender', $request->gender);
            }
            if ($request->has('min_age') && $request->has('max_age') && $request->min_age != "" && $request->max_age != "") {
                $minAge = $request->input('min_age');
                $maxAge = $request->input('max_age');

                $nearby_mixxer->whereBetween('age_limit', [$minAge, $maxAge]);
            }
            if ($request->has('categories')  && $request->categories != "") {
                $categories = explode(',', $request->categories);
                $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
                $nearby_mixxer->whereIn('id', $mixxer_ids);
            }

            if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
                $userLat = $request->lat;
                $userLng = $request->lng;
                $nearby_mixxer->select(
                    'id',
                    'user_id',
                    'cover',
                    'title',
                    'age_limit',
                    'gender',
                    'categories',
                    'start_date',
                    'is_all_day',
                    'start_time',
                    'end_time',
                    'location',
                    'lat',
                    'lng',
                    'address',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                    ->having('distance', '<=', $request->radius)
                    ->orderBy('distance');
            }
            $nearby_mixxer = $nearby_mixxer->paginate(12);
            foreach ($nearby_mixxer as $item1) {
                $categorieIds = explode(',', $item1->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item1->categories = $categories;
            }
        }

        $firendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('friend_id');
        $firendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->pluck('user_id');
        $firendIds = $firendIds->merge($firendIds1);

        $friend_mixxer = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
            'age_limit',
            'gender',
            'categories',
            'start_date',
            'is_all_day',
            'start_time',
            'end_time',
            'location',
            'lat',
            'lng',
            'address'
        )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
            ->where('status', '!=', 2);

        if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
            $friend_mixxer->where('start_date', $request->start_date);
        }
        if ($request->has('start_date') && $request->has('to_date') && $request->start_date != "" && $request->to_date != "") {
            $friend_mixxer->whereBetween('start_date', [$request->start_date, $request->to_date]);
        }
        if ($request->has('gender') && $request->gender != "") {
            $friend_mixxer->where('gender', $request->gender);
        }
        if ($request->has('min_age') && $request->has('max_age') && $request->min_age != "" && $request->max_age != "") {
            $minAge = $request->input('min_age');
            $maxAge = $request->input('max_age');

            $friend_mixxer->whereBetween('age_limit', [$minAge, $maxAge]);
        }
        if ($request->has('categories') && $request->categories != "") {
            $categories = explode(',', $request->categories);
            $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
            $friend_mixxer->whereIn('id', $mixxer_ids);
        }

        if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
            $userLat = $request->lat;
            $userLng = $request->lng;
            $friend_mixxer->select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
                ->having('distance', '<=', $request->radius)
                ->orderBy('distance');
        }
        $friend_mixxer = $friend_mixxer->limit(12)->get();
        foreach ($friend_mixxer as $item2) {
            $categorieIds = explode(',', $item2->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item2->categories = $categories;
        }


        return response()->json([
            'status' => true,
            'action' => 'Home',
            'data' => array(
                'feature' => $feature_mixxer,
                'friend' => $friend_mixxer,
                'nearby' => $nearby_mixxer
            )
        ]);
    }

    public function list(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);

        if ($type == 'feature') {
            $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
            $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

            $userIds = UserSubscription::where('type', '!=', 'free')->pluck('user_id');
            $feature_mixxer  = Mixxer::select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereIn('user_id', $userIds)->orderby('id', 'desc')
                ->where('status', '!=', 2);

            if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
                $feature_mixxer->where('start_date', $request->start_date);
            }
            if ($request->has('start_date') && $request->has('to_date') && $request->start_date != "" && $request->to_date != "") {
                $feature_mixxer->whereBetween('start_date', [$request->start_date, $request->to_date]);
            }
            if ($request->has('gender') && $request->gender != "") {
                $feature_mixxer->where('gender', $request->gender);
            }
            if ($request->has('min_age') && $request->has('max_age')  && $request->min_age != ""  && $request->max_age != "") {
                $minAge = $request->input('min_age');
                $maxAge = $request->input('max_age');

                $feature_mixxer->whereBetween('age_limit', [$minAge, $maxAge]);
            }
            if ($request->has('categories')  && $request->categories != "") {
                $categories = explode(',', $request->categories);
                $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
                $feature_mixxer->whereIn('id', $mixxer_ids);
            }

            if (
                $request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != ""
            ) {
                $userLat = $request->lat;
                $userLng = $request->lng;
                $feature_mixxer->select(
                    'id',
                    'user_id',
                    'cover',
                    'title',
                    'age_limit',
                    'gender',
                    'categories',
                    'start_date',
                    'is_all_day',
                    'start_time',
                    'end_time',
                    'location',
                    'lat',
                    'lng',
                    'address',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                    ->having('distance', '<=', $request->radius)
                    ->orderBy('distance');
            }

            $feature_mixxer = $feature_mixxer->paginate(12);
            foreach ($feature_mixxer as $item) {
                $categorieIds = explode(',', $item->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item->categories = $categories;
            }


            return response()->json([
                'status' => true,
                'action' => 'Feature Mixxer',
                'data' => $feature_mixxer
            ]);
        }
        if ($type == 'friend') {
            $firendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('friend_id');
            $firendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->pluck('user_id');
            $firendIds = $firendIds->merge($firendIds1);

            $friend_mixxer = Mixxer::select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
                ->where('status', '!=', 2);
            if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
                $friend_mixxer->where('start_date', $request->start_date);
            }
            if ($request->has('start_date') && $request->has('to_date')  && $request->start_date != "" && $request->to_date != "") {
                $friend_mixxer->whereBetween('start_date', [$request->start_date, $request->to_date]);
            }
            if ($request->has('gender') && $request->gender != "") {
                $friend_mixxer->where('gender', $request->gender);
            }
            if ($request->has('min_age') && $request->has('max_age') && $request->min_age != "" && $request->max_age != "") {
                $minAge = $request->input('min_age');
                $maxAge = $request->input('max_age');

                $friend_mixxer->whereBetween('age_limit', [$minAge, $maxAge]);
            }
            if ($request->has('categories')  && $request->categories != "") {
                $categories = explode(',', $request->categories);
                $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
                $friend_mixxer->whereIn('id', $mixxer_ids);
            }

            if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
                $userLat = $request->lat;
                $userLng = $request->lng;
                $friend_mixxer->select(
                    'id',
                    'user_id',
                    'cover',
                    'title',
                    'age_limit',
                    'gender',
                    'categories',
                    'start_date',
                    'is_all_day',
                    'start_time',
                    'end_time',
                    'location',
                    'lat',
                    'lng',
                    'address',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                    ->having('distance', '<=', $request->radius)
                    ->orderBy('distance');
            }
            $friend_mixxer = $friend_mixxer->paginate(12);
            foreach ($friend_mixxer as $item2) {
                $categorieIds = explode(',', $item2->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item2->categories = $categories;
            }

            return response()->json([
                'status' => true,
                'action' => 'Friend Mixxer',
                'data' => $friend_mixxer

            ]);
        }
    }

    public function applyFilter(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $mixxers = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
            'age_limit',
            'gender',
            'categories',
            'start_date',
            'is_all_day',
            'start_time',
            'end_time',
            'location',
            'lat',
            'lng',
            'address'
        )->where('user_id', '!=', $user->uuid)->where('status', '!=', 2);

        if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
            $mixxers->where('start_date', $request->start_date);
        }
        if ($request->has('start_date') && $request->has('to_date')) {
            $mixxers->whereBetween('start_date', [$request->start_date, $request->to_date]);
        }
        if ($request->has('gender') && $request->gender != "") {
            $mixxers->where('gender', $request->gender);
        }
        if ($request->has('min_age') && $request->has('max_age')) {
            $minAge = $request->input('min_age');
            $maxAge = $request->input('max_age');

            $mixxers->whereBetween('age_limit', [$minAge, $maxAge]);
        }
        if ($request->has('categories')) {
            $categories = explode(',', $request->categories);
            $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
            $mixxers->whereIn('id', $mixxer_ids);
        }

        if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
            $userLat = $request->lat;
            $userLng = $request->lng;
            $mixxers->select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
                ->having('distance', '<=', $request->radius)
                ->orderBy('distance');
        }


        $mixxers = $mixxers->latest()->get();

        foreach ($mixxers as $item) {
            $categorieIds = explode(',', $item->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds);
            $item->categories = $categories;
        }
        return response()->json([
            'status' => true,
            'action' => 'Filter Result',
            'data' => $mixxers
        ]);
    }

    public function search(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = Blocklist::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        if ($request->type == 'mixxer') {
            if ($request->keyword != null || $request->keyword != '') {
                $mixxers  = Mixxer::select(
                    'id',
                    'user_id',
                    'cover',
                    'title',
                    'age_limit',
                    'gender',
                    'categories',
                    'start_date',
                    'is_all_day',
                    'start_time',
                    'end_time',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->where("title", "LIKE", "%" . $request->keyword . "%")->where('status', '!=', 2)->latest()->paginate(12);

                foreach ($mixxers as $item) {
                    $categorieIds = explode(',', $item->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds);
                    $item->categories = $categories;
                }

                return response()->json([
                    'status' => true,
                    'action' =>  'Search Result',
                    'data' => $mixxers
                ]);
            }
        }

        if ($request->type == 'user') {
            if ($request->keyword != null || $request->keyword != '') {

                $user  = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

                return response()->json([
                    'status' => true,
                    'action' =>  'Search Result',
                    'data' => $user
                ]);
            }
        }
    }

    public function detail(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $mixxer = Mixxer::with(['user:uuid,first_name,last_name,profile_picture,email,location'])->where('id', $mixxer_id)->first();
        $categories = explode(',', $mixxer->categories);
        $category = Category::select('id', 'name', 'image')->whereIn('id', $categories)->get();
        $mixxer->categories = $category;
        $photos = MixxerMedia::where('mixxer_id', $mixxer->id)->where('type', 'image')->get();
        $doc = MixxerMedia::where('mixxer_id', $mixxer->id)->where('type', 'doc')->get();
        $mixxer->photos = $photos;
        $mixxer->doc = $doc;
        $mixxer->participant_count = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->count();
        $mixxer->join_request_count = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'pending')->count();
        $participantIds = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->pluck('user_id');
        $participants = User::whereIn('uuid', $participantIds)->limit(12)->get();
        $mixxer->participants = $participants;
        $mixxer->reason = '';
        $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $user->uuid)->first();
        if ($request_check) {
            if ($request_check->status == 'pending') {
                $mixxer->is_join = 'cancel';
            }
            if ($request_check->status == 'accept') {
                $mixxer->is_join = 'leave';
            }
            if ($request_check->status == 'leave') {
                $mixxer->is_join = 'join';
            }
            if ($request_check->status == 'invite') {
                $mixxer->is_join = 'accept_invite';
            }
            if ($request_check->status == 'reject') {
                $mixxer->is_join = 'rejected';
                $mixxer->reason = $request_check->reason;
            }
        } else {
            $mixxer->is_join = 'join';
        }

        $is_save = SaveMixxer::where('mixxer_id', $mixxer_id)->where('user_id', $user->uuid)->first();
        if ($is_save) {
            $mixxer->is_save = true;
        } else {
            $mixxer->is_save = false;
        }
        return response()->json([
            'status' => true,
            'action' => 'Mixxer Detail',
            'data' => $mixxer
        ]);
    }

    public function joinRequest(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $mixxer = Mixxer::find($mixxer_id);

        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->first();
        if ($find) {
            $find->delete();
            Notification::where('person_id', $user->uuid)->where('user_id', $mixxer->user_id)->where('type', 'join_request')->delete();

            return response()->json([
                'status' => true,
                'action' => 'Request Cancel',
            ]);
        }

        $create = new MixxerJoinRequest();
        $create->user_id = $user->uuid;
        $create->mixxer_id = $mixxer_id;
        $create->save();

        NewNotification::handle($mixxer->user_id, $user->uuid, $mixxer_id, 'has sent you join request in ' . $mixxer->title, 'mixxer', 'join_mixxer_request');
        $userTokens = UserDevice::where('user_id', $mixxer->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

        $data1 = [
            'data_id' => $request->mixxer_id,
            'type' => 'join_mixxer_request',
        ];

        $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' has sent you join request.', $userTokens, $data1, 1);

        return response()->json([
            'status' => true,
            'action' => 'Request Send',
        ]);
    }

    public function leave(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->first();
        if ($find) {
            $find->delete();
            $chat_message = new Message();
            $chat_message->from = $user->uuid;
            $chat_message->to = 0;
            $chat_message->mixxer_id = $mixxer_id;
            $chat_message->type = 'leave';
            $chat_message->message = 'Left the mixxer chat';
            $chat_message->time = time();
            $chat_message->save();

            $mixxer = Mixxer::find($mixxer_id);

            $ownerToken = UserDevice::where('user_id', $mixxer->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();


            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $request->mixxer_id)->where('status', 'accept')->pluck('user_id');
            $tokens = UserDevice::whereIn('user_id', $mixxerUserIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $combinedTokens = array_merge($tokens, $ownerToken);

            $data = [
                'data_id' => $request->mixxer_id,
                'type' => 'leave_mixxer',
            ];

            $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' ' . $chat_message->message.'.', $combinedTokens, $data, 1);

            return response()->json([
                'status' => true,
                'action' => 'Mixxer Leave',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Request Find',
        ]);
    }

    public function delete(Request $request, $mixxer_id)
    {
        $find = Mixxer::find($mixxer_id);
        if ($find) {
            Message::where('mixxer_id', $mixxer_id)->delete();
            Notification::where('data_id', $mixxer_id)->where('main_type', 'mixxer')->delete();
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Mixxer Deleted',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Fixxer Find',
        ]);
    }

    public function joinRequestList(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $user_Ids = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'pending')->pluck('user_id');
        $participants = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $user_Ids)->get();

        return response()->json([
            'status' => true,
            'action' => 'Request List',
            'data' => $participants
        ]);
    }

    public function rejectRequest(Request $request)
    {

        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'mixxer_id' => 'required|exists:mixxers,id',
            'reason' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $mixxer = Mixxer::find($request->mixxer_id);

        $find = MixxerJoinRequest::where('user_id', $request->user_id)->where('mixxer_id', $request->mixxer_id)->first();
        if ($find) {
            $find->status = 'reject';
            $find->reason = $request->reason;
            $find->save();
            NewNotification::handle($request->user_id, $mixxer->user_id, $mixxer->id, 'Your request to join ' . $mixxer->title . ' is denied.', 'mixxer', 'reject_mixxer_request');

            $tokens = UserDevice::where('user_id', $request->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $data = [
                'data_id' => $mixxer->id,
                'type' => 'reject_mixxer_request',
            ];

            $this->firebaseNotification->sendNotification($mixxer->title,$user->first_name . ' ' . $user->last_name . ' has denied your request.', $tokens, $data,1);

            return response()->json([
                'status' => true,
                'action' => 'Request Reject',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Request Find',
        ]);
    }

    public function acceptRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'mixxer_id' => 'required|exists:mixxers,id',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $user = User::find($request->user_id);
        $mixxer = Mixxer::find($request->mixxer_id);

        $find = MixxerJoinRequest::where('user_id', $request->user_id)->where('mixxer_id', $request->mixxer_id)->first();
        if ($find) {
            $find->status = 'accept';
            $find->save();
            $chat_message = new Message();
            $chat_message->from = $request->user_id;
            $chat_message->to = 0;
            $chat_message->mixxer_id = $request->mixxer_id;
            $chat_message->type = 'join';
            $chat_message->message = 'Joined the mixxer chat';
            $chat_message->time = time();
            $chat_message->save();

            NewNotification::handle($request->user_id, $mixxer->user_id, $mixxer->id, 'has accepted your request to join mixxer ' . $mixxer->title, 'mixxer', 'accept_mixxer_request');

            $userTokens = UserDevice::where('user_id', $request->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

            $owner = User::find($mixxer->user_id);
            $data1 = [
                'data_id' => $request->mixxer_id,
                'type' => 'accept_mixxer_request',
            ];

            $this->firebaseNotification->sendNotification($mixxer->title, $owner->first_name . ' ' . $owner->last_name . ' has accepted your request to join mixxer.', $userTokens, $data1, 1);


            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $request->mixxer_id)->where('status', 'accept')->pluck('user_id');

            $tokens = UserDevice::whereIn('user_id', $mixxerUserIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

            $data = [
                'data_id' => $request->mixxer_id,
                'type' => 'accept_mixxer_request',
            ];

            $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' ' . $chat_message->message.'.', $tokens, $data, 1);

            return response()->json([
                'status' => true,
                'action' => 'Request Accept',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Request Find',
        ]);
    }

    public function participantList(Request $request, $mixxer_id)
    {
        $participantIds = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->pluck('user_id');
        $participants = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $participantIds)->paginate(25);
        return response()->json([
            'status' => true,
            'action' => 'Participants List',
            'data' => $participants,
        ]);
    }

    public function inbox(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $mixxerIds = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $mixxers = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
        )->where('status', '!=', 2)->where('user_id', $user->uuid)->get();
        $mixxers1 = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
        )->whereIn('id', $mixxerIds)->where('status', '!=', 2)->get();
        $combinedMixxers = $mixxers->merge($mixxers1);




        foreach ($combinedMixxers as $item) {
            $message = Message::where('mixxer_id', $item->id)->latest()->first();
            $obj = new stdClass();

            if ($message) {
                $total_message = Message::where('from', '!=', $user->uuid)->where('mixxer_id', $item->id)->count();
                $messageIDs = Message::where('from', '!=', $user->uuid)->where('mixxer_id', $item->id)->pluck('id');
                $total_read_message = MessageRead::whereIn('message_id', $messageIDs)->where('user_id', $user->uuid)->count();

                $total_unread_message = $total_message - $total_read_message;
                $user1 = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where('uuid', $message->from)->first();
                $obj->message = $message->message;
                $obj->type = $message->type;
                $obj->unread_count = $total_unread_message;
                $obj->time = $message->time;
                $obj->user = $user1;
                $item->message  = $obj;
                $item->time = $message->time;
            } else {
                $item->messsage  = $obj;
                $item->time = '';
            }
        }
        $sorted = collect($combinedMixxers)->sortByDesc('time');

        // ---COMMENTED FOR FUTURE USE IF NEEDED FOR PAGINATION---
        // $sorted = $sorted->forPage($request->page, 20);

        $arr1 = [];
        $count = 0;
        foreach ($sorted as $item) {
            $arr1[] = $item;
        }
        return response()->json([
            'status' => true,
            'action' => 'Inbox',
            'data' => $arr1,
        ]);
    }


    public function conversation($mixxer_id)
    {
        $mixxer = Mixxer::find($mixxer_id);
        $owner = User::find($mixxer->user_id);
        $obj = new stdClass();
        $obj->id = $mixxer->id;
        $obj->name = $mixxer->title;
        $obj->image = $mixxer->cover;
        $members = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->count();
        $obj->total_memebers = $members;
        $obj->owner_name  = $owner->first_name . ' ' . $owner->last_name;
        if ($mixxer) {
            $messages = Message::where('mixxer_id', $mixxer_id)->latest()->paginate(5000);
            foreach ($messages as $message) {
                $user = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where('uuid', $message->from)->first();
                $message->user = $user;
            }
            return response()->json([
                'status' => true,
                'action' => "Conversation",
                'mixxer' => $obj,
                'data' => $messages,
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => "No Mixxer found",
        ]);
    }


    public function messageRead(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $readIds = MessageRead::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->pluck('message_id');
        $messageIds = Message::where('mixxer_id', $mixxer_id)->where('from', '!=', $user->uuid)->whereNotIn('id', $readIds)->pluck('id');
        foreach ($messageIds as $id) {
            $create = new MessageRead();
            $create->message_id = $id;
            $create->mixxer_id = $mixxer_id;
            $create->user_id = $user->uuid;
            $create->time = time();
            $create->save();
        }
        return response(['status' => true, 'action' => 'Messages read']);
    }

    public function changeStatus(Request $request, $mixxer_id, $status)
    {
        $mixxer = Mixxer::find($mixxer_id);
        if ($mixxer) {
            $mixxer->status = $status;
            $mixxer->save();
            return response()->json([
                'status' => true,
                'action' => 'Status Change',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Mixxer not found',
        ]);
    }

    public function nearBy(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $nearby_mixxer = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
            'age_limit',
            'gender',
            'categories',
            'start_date',
            'is_all_day',
            'start_time',
            'end_time',
            'location',
            'lat',
            'lng',
            'address'
        )->where('user_id', '!=', $user->uuid)
            ->where('status', '!=', 2);


        if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
            $userLat = $request->lat;
            $userLng = $request->lng;
            $nearby_mixxer->select(
                'id',
                'user_id',
                'cover',
                'title',
                'age_limit',
                'gender',
                'categories',
                'start_date',
                'is_all_day',
                'start_time',
                'end_time',
                'location',
                'lat',
                'lng',
                'address',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
                ->having('distance', '<=', $request->radius)
                ->orderBy('distance');
        }

        $nearby_mixxer = $nearby_mixxer->get();

        foreach ($nearby_mixxer as $item1) {
            $categorieIds = explode(',', $item1->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item1->categories = $categories;
        }
        return response()->json([
            'status' => true,
            'action' => "Mixxer List",
            'data' => $nearby_mixxer
        ]);
    }

    public function inviteUserList(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = Blocklist::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);

        $friendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->whereNotIn('friend_id', $blocked)->pluck('friend_id');
        $friendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->whereNotIn('user_id', $blocked)->pluck('user_id');
        $friendIds = $friendIds->merge($friendIds1);

        $userIDs = User::whereIn('uuid', $friendIds)->whereNotIn('uuid', $blocked)->pluck('uuid');

        if (count($userIDs) > 0) {
            $users = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $userIDs)->latest()->paginate(12);
            foreach ($users as $item) {
                $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $item->uuid)->first();
                if ($request_check) {
                    $item->is_invite = true;
                } else {
                    $item->is_invite = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' => 'Friends',
                'data' => $users
            ]);
        }

        $users = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->latest()->paginate(12);
        foreach ($users as $item) {
            $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $item->uuid)->first();
            if ($request_check) {
                $item->is_invite = true;
            } else {
                $item->is_invite = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' => 'Suggestion',
            'data' => $users
        ]);
    }

    public function sendInvite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,uuid',
            'mixxer_id' => 'required|exists:mixxers,id',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }
        $mixxer = Mixxer::find($request->mixxer_id);
        $find = MixxerJoinRequest::where('user_id', $request->user_id)->where('mixxer_id', $request->mixxer_id)->where('status', 'invite')->first();
        if ($find) {
            $find->delete();
            Notification::where('person_id', $mixxer->user_id)->where('user_id', $request->user_id)->where('type', 'send_mixxer_invite')->delete();

            return response()->json([
                'status' => true,
                'action' => 'Invite Remove',
            ]);
        }
        $user = User::find($request->user_id);

        $create = new MixxerJoinRequest();
        $create->user_id = $request->user_id;
        $create->mixxer_id = $request->mixxer_id;
        $create->status = 'invite';
        $create->save();
        NewNotification::handle($request->user_id, $mixxer->user_id, $request->mixxer_id, 'mixxer', ' has invited you to join ', 'send_mixxer_invite');
        $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

        $data = [
            'data_id' => $request->mixxer_id,
            'type' => 'send_mixxer_invite',
        ];

        $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' has invited you to join.', $tokens, $data, 1);

        return response()->json([
            'status' => true,
            'action' => 'Invite Send',
        ]);
    }

    public function acceptInviteRequest(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $mixxer = Mixxer::find($mixxer_id);

        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->where('status', 'invite')->first();
        if ($find) {
            $find->status = 'accept';
            $find->save();
            $chat_message = new Message();
            $chat_message->from = $user->uuid;
            $chat_message->to = 0;
            $chat_message->mixxer_id = $mixxer_id;
            $chat_message->type = 'join';
            $chat_message->message = 'Joined the mixxer chat';
            $chat_message->time = time();
            $chat_message->save();
            NewNotification::handle($mixxer->user_id, $user->uuid, $mixxer_id, 'has accepted your invitation to join ', 'mixxer', 'accept_mixxer_invite');

            $ownerToken = UserDevice::where('user_id', $mixxer->user_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();


            $data1 = [
                'data_id' => $request->mixxer_id,
                'type' => 'accept_mixxer_invite',
            ];

            $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . 'has accepted your invitation to join.', $ownerToken, $data1, 1);


            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->pluck('user_id');

            $tokens = UserDevice::whereIn('user_id', $mixxerUserIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

            $data = [
                'data_id' => $request->mixxer_id,
                'type' => 'accept_mixxer_invite',
            ];

            $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' ' . $chat_message->message.'.', $tokens, $data, 1);

            return response()->json([
                'status' => true,
                'action' => 'Invite Accept',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Invitation Find',
        ]);
    }
    public function searchUser(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = Blocklist::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);
        if ($request->keyword != null || $request->keyword != '') {

            $users  = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);
            foreach ($users as $item) {
                $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $item->uuid)->first();
                if ($request_check) {
                    $item->is_invite = true;
                } else {
                    $item->is_invite = false;
                }
            }
            return response()->json([
                'status' => true,
                'action' => 'Search',
                'data' => $users
            ]);
        }

        $users  = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);
        foreach ($users as $item) {
            $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $item->uuid)->first();
            if ($request_check) {
                $item->is_invite = true;
            } else {
                $item->is_invite = false;
            }
        }
        return response()->json([
            'status' => true,
            'action' => 'Suggestions',
            'data' => $users
        ]);
    }

    public function deleteMedia($id)
    {
        $find = MixxerMedia::find($id);
        if ($find) {
            $find->delete();
            return response()->json([
                'status' => true,
                'action' => 'Media Deleted',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Media not found',
        ]);
    }

    public function friendlyCheck(Request $request){
        $user = User::find($request->user()->uuid);
        $create = new MixxerFriendlyCheck();
        $create->user_id = $user->uuid;
        $create->mixxer_id = $request->mixxer_id;
        $create->friendly_check = $request->check;
        $create->save();
        return response()->json([
            'status' => true,
            'action' => 'Feedback Send',
        ]);

    }
}
