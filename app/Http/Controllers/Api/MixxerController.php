<?php

namespace App\Http\Controllers\Api;

use App\Actions\BlockedUser;
use App\Actions\NewNotification;
use App\Actions\UserUnreadCount;
use App\Http\Controllers\Controller;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Mixxer;
use App\Models\MixxerCategory;
use App\Models\MixxerFeedback;
use App\Models\MixxerFriendlyCheck;
use App\Models\MixxerFriendlyCheckFeedback;
use App\Models\MixxerInbox;
use App\Models\MixxerJoinRequest;
use App\Models\MixxerMedia;
use App\Models\MixxerView;
use App\Models\Notification;
use App\Models\NotificationAllow;
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
            $path = Storage::disk('s3')->putFile('user/' . $user->uuid . '/mixer/cover', $file);
            $path = Storage::disk('s3')->url($path);
            $create->cover = $path;

            $path1 = Storage::disk('local')->put('user/' . $user->uuid . '/mixer/cover', $file);

            $imagePath = public_path('/uploads/' . $path1);
            list($width, $height) = getimagesize($imagePath);

            $size = $width / $height;
            $size = number_format($size, 2);
            $create->cover_size = $size;

            unlink($imagePath);
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
                $path = Storage::disk('s3')->putFile('user/' . $user->uuid . '/mixer/photos', $file);
                $path = Storage::disk('s3')->url($path);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = $path;
                $photo->type = 'image';
                $photo->save();
            }
        }

        if ($request->hasFile('doc')) {
            $files = $request->file('doc');
            foreach ($files as $file) {
                $path = Storage::disk('s3')->putFile('user/' . $user->uuid . '/mixer/doc', $file);
                $path = Storage::disk('s3')->url($path);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = $path;
                $photo->type = 'doc';
                $photo->save();
            }
        }

        $new = Mixxer::find($create->id);


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
            $path = Storage::disk('s3')->putFile('user/' . $user->uuid . '/mixer/cover', $file);
            $path = Storage::disk('s3')->url($path);
            $create->cover =  $path;
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file) {
                $path = Storage::disk('s3')->putFile('user/' . $user->uuid . '/mixer/photos', $file);
                $path = Storage::disk('s3')->url($path);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = $path;
                $photo->type = 'image';
                $photo->save();
            }
        }

        if ($request->hasFile('doc')) {
            $files = $request->file('doc');
            foreach ($files as $file) {
                $path = Storage::disk('s3')->putFile('user/' . $user->uuid . '/mixer/doc', $file);
                $path = Storage::disk('s3')->url($path);
                $photo = new MixxerMedia();
                $photo->mixxer_id = $create->id;
                $photo->media = $path;
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

        $blocked = BlockedUser::handle($user->uuid);
        $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
        $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

        $userIds = UserSubscription::where('type', '!=', 'free')->pluck('user_id');

        $acceptedMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $rejectMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'reject')->pluck('mixxer_id');

        $allMixxers = Mixxer::where('status', 0)->get();
        $mixxerLimit = [];
        foreach ($allMixxers as $oneMixxer) {
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $oneMixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $oneMixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $mixxerLimit[] = $oneMixxer->id;
            }
        }

        $userGender = $user->gender;
        $userAge = $user->age;
        if ($userGender == 'Male') {
            $userGender =  'Women Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Female') {
            $userGender = 'Men Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Non-Binary') {
            $userGender = 'Men Only';
            $userGender1 = 'Women Only';
        }


        if ($user->age != '' && $user->gender != '') {
            // $feature_mixxer  = Mixxer::select(
            //     'id',
            //     'user_id',
            //     'cover',
            //     'title',
            //     'age_limit',
            //     'gender',
            //     'categories',
            //     'start_date',
            //     'is_all_day',
            //     'start_time',
            //     'start_timestamp',
            //     'end_time',
            //     'end_timestamp',
            //     'location',
            //     'lat',
            //     'lng',
            //     'address'
            // )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->whereIn('user_id', $userIds)->orderby('id', 'desc')
            //     ->where('status', 0)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge);

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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->orderby('id', 'desc')
                ->where('status', 0)->inRandomOrder();

            $feature_mixxer = $feature_mixxer->limit(12)->get();
            foreach ($feature_mixxer as $item) {
                $categorieIds = explode(',', $item->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item->categories = $categories;
                $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
                $item->is_seen = false;
                if ($find_is_seen) {
                    $item->is_seen = true;
                }
            }


            if ($request->has('lat') && $request->has('lng')) {
                $userLat = $request->lat ?: 0;
                $userLng = $request->lng ?: 0;
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                    // ->having('distance', '<=', $request->radius)
                    ->orderBy('distance')
                    ->where('user_id', '!=', $user->uuid)
                    ->where('status', 0)
                    ->whereNotIn('user_id', $blocked)
                    ->whereNotIn('id', $acceptedMixxer)
                    ->whereNotIn('id', $rejectMixxer)
                    ->whereNotIn('id', $mixxerLimit)
                    ->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge);

                $nearby_mixxer = $nearby_mixxer->paginate(12);

                foreach ($nearby_mixxer as $item1) {
                    $categorieIds = explode(',', $item1->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item1->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item1->id)->first();
                    $item1->is_seen = false;
                    if ($find_is_seen) {
                        $item1->is_seen = true;
                    }
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->orderBy('id', 'desc')
                    ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge);

                $nearby_mixxer = $nearby_mixxer->paginate(12);
                foreach ($nearby_mixxer as $item1) {
                    $categorieIds = explode(',', $item1->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item1->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item1->id)->first();
                    $item1->is_seen = false;
                    if ($find_is_seen) {
                        $item1->is_seen = true;
                    }
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
                ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge);
            $friend_mixxer = $friend_mixxer->limit(12)->get();
            foreach ($friend_mixxer as $item2) {
                $categorieIds = explode(',', $item2->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item2->categories = $categories;
                $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item2->id)->first();
                $item2->is_seen = false;
                if ($find_is_seen) {
                    $item2->is_seen = true;
                }
            }
        } else {


            // $feature_mixxer  = Mixxer::select(
            //     'id',
            //     'user_id',
            //     'cover',
            //     'title',
            //     'age_limit',
            //     'gender',
            //     'categories',
            //     'start_date',
            //     'is_all_day',
            // 'start_time',
            // 'start_timestamp',
            // 'end_time',
            // 'end_timestamp',
            //     'location',
            //     'lat',
            //     'lng',
            //     'address'
            // )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->whereIn('user_id', $userIds)->orderby('id', 'desc')
            //     ->where('status', 0);

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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->orderby('id', 'desc')
                ->where('status', 0)->inRandomOrder();

            $feature_mixxer = $feature_mixxer->limit(12)->get();
            foreach ($feature_mixxer as $item) {
                $categorieIds = explode(',', $item->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item->categories = $categories;
                $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
                $item->is_seen = false;
                if ($find_is_seen) {
                    $item->is_seen = true;
                }
            }


            if ($request->has('lat') && $request->has('lng')) {
                $userLat = $request->lat ?: 0;
                $userLng = $request->lng ?: 0;
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address',
                    DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
                )
                    // ->having('distance', '<=', $request->radius)
                    ->orderBy('distance')
                    ->where('user_id', '!=', $user->uuid)
                    ->where('status', 0)
                    ->whereNotIn('user_id', $blocked)
                    ->whereNotIn('id', $acceptedMixxer)
                    ->whereNotIn('id', $rejectMixxer)
                    ->whereNotIn('id', $mixxerLimit);

                $nearby_mixxer = $nearby_mixxer->paginate(12);

                foreach ($nearby_mixxer as $item1) {
                    $categorieIds = explode(',', $item1->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item1->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item1->id)->first();
                    $item1->is_seen = false;
                    if ($find_is_seen) {
                        $item1->is_seen = true;
                    }
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->orderBy('id', 'desc')
                    ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);
                $nearby_mixxer = $nearby_mixxer->paginate(12);
                foreach ($nearby_mixxer as $item1) {
                    $categorieIds = explode(',', $item1->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item1->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item1->id)->first();
                    $item1->is_seen = false;
                    if ($find_is_seen) {
                        $item1->is_seen = true;
                    }
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
                ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);

            $friend_mixxer = $friend_mixxer->limit(12)->get();
            foreach ($friend_mixxer as $item2) {
                $categorieIds = explode(',', $item2->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item2->categories = $categories;
                $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item2->id)->first();
                $item2->is_seen = false;
                if ($find_is_seen) {
                    $item2->is_seen = true;
                }
            }
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
        $blocked = BlockedUser::handle($user->uuid);

        $acceptedMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $rejectMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'reject')->pluck('mixxer_id');

        $allMixxers = Mixxer::where('status', 0)->get();
        $mixxerLimit = [];
        foreach ($allMixxers as $oneMixxer) {
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $oneMixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $oneMixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $mixxerLimit[] = $oneMixxer->id;
            }
        }
        $userGender = $user->gender;
        $userAge = $user->age;
        if ($userGender == 'Male') {
            $userGender =  'Women Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Female') {
            $userGender = 'Men Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Non-Binary') {
            $userGender = 'Men Only';
            $userGender1 = 'Women Only';
        }

        if ($user->age != '' && $user->gender != '') {
            if ($type == 'feature') {
                $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

                $userIds = UserSubscription::where('type', '!=', 'free')->pluck('user_id');
                // $feature_mixxer  = Mixxer::select(
                //     'id',
                //     'user_id',
                //     'cover',
                //     'title',
                //     'age_limit',
                //     'gender',
                //     'categories',
                //     'start_date',
                //     'is_all_day',
                //     'start_time',
                //     'start_timestamp',
                //     'end_time',
                //     'end_timestamp',
                //     'location',
                //     'lat',
                //     'lng',
                //     'address'
                // )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereIn('user_id', $userIds)->orderby('id', 'desc')
                //     ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge)->whereNotIn('id', $mixxerLimit);

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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->orderby('id', 'desc')
                    ->where('status', 0)->inRandomOrder();


                $feature_mixxer = $feature_mixxer->paginate(12);
                foreach ($feature_mixxer as $item) {
                    $categorieIds = explode(',', $item->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
                    $item->is_seen = false;
                    if ($find_is_seen) {
                        $item->is_seen = true;
                    }
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
                    ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge)->whereNotIn('id', $mixxerLimit);
                $friend_mixxer = $friend_mixxer->paginate(12);
                foreach ($friend_mixxer as $item2) {
                    $categorieIds = explode(',', $item2->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item2->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item2->id)->first();
                    $item2->is_seen = false;
                    if ($find_is_seen) {
                        $item2->is_seen = true;
                    }
                }

                return response()->json([
                    'status' => true,
                    'action' => 'Friend Mixxer',
                    'data' => $friend_mixxer

                ]);
            }
        } else {
            if ($type == 'feature') {
                $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
                $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

                $userIds = UserSubscription::where('type', '!=', 'free')->pluck('user_id');
                // $feature_mixxer  = Mixxer::select(
                //     'id',
                //     'user_id',
                //     'cover',
                //     'title',
                //     'age_limit',
                //     'gender',
                //     'categories',
                //     'start_date',
                //     'is_all_day',
                //     'start_time',
                //     'start_timestamp',
                //     'end_time',
                //     'end_timestamp',
                //     'location',
                //     'lat',
                //     'lng',
                //     'address'
                // )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereIn('user_id', $userIds)->orderby('id', 'desc')
                //     ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->orderby('id', 'desc')
                    ->where('status', 0)->inRandomOrder();
                $feature_mixxer = $feature_mixxer->paginate(12);
                foreach ($feature_mixxer as $item) {
                    $categorieIds = explode(',', $item->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
                    $item->is_seen = false;
                    if ($find_is_seen) {
                        $item->is_seen = true;
                    }
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
                    'location',
                    'lat',
                    'lng',
                    'address'
                )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
                    ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);

                $friend_mixxer = $friend_mixxer->paginate(12);
                foreach ($friend_mixxer as $item2) {
                    $categorieIds = explode(',', $item2->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item2->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item2->id)->first();
                    $item2->is_seen = false;
                    if ($find_is_seen) {
                        $item2->is_seen = true;
                    }
                }

                return response()->json([
                    'status' => true,
                    'action' => 'Friend Mixxer',
                    'data' => $friend_mixxer

                ]);
            }
        }
    }

    public function applyFilter(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $acceptedMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $rejectMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'reject')->pluck('mixxer_id');

        $allMixxers = Mixxer::where('status', 0)->get();
        $mixxerLimit = [];
        foreach ($allMixxers as $oneMixxer) {
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $oneMixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $oneMixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $mixxerLimit[] = $oneMixxer->id;
            }
        }

        $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
        $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

        $firendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('friend_id');
        $firendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->pluck('user_id');
        $firendIds = $firendIds->merge($firendIds1);

        // $feature_mixxer  = Mixxer::select(
        //     'id',
        //     'user_id',
        //     'cover',
        //     'title',
        //     'age_limit',
        //     'gender',
        //     'categories',
        //     'start_date',
        //     'is_all_day',
        // 'start_time',
        // 'start_timestamp',
        // 'end_time',
        // 'end_timestamp',
        //     'location',
        //     'lat',
        //     'lng',
        //     'address'
        // )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->whereIn('user_id', $userIds)->orderby('id', 'desc')
        //     ->where('status', 0);

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
            'start_timestamp',
            'end_time',
            'end_timestamp',
            'location',
            'lat',
            'lng',
            'address'
        )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->orderby('id', 'desc')
            ->where('status', 0)->inRandomOrder();

        if ($request->has('lat') && $request->has('lng')) {
            $userLat = $request->lat ?: 0;
            $userLng = $request->lng ?: 0;
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address',
                DB::raw("(6371 * acos(cos(radians($userLat)) * cos(radians(lat)) * cos(radians(lng) - radians($userLng)) + sin(radians($userLat)) * sin(radians(lat)))) AS distance")
            )
                // ->having('distance', '<=', $request->radius)
                ->orderBy('distance')
                ->where('user_id', '!=', $user->uuid)
                ->where('status', 0)
                ->whereNotIn('user_id', $blocked)
                ->whereNotIn('id', $acceptedMixxer)
                ->whereNotIn('id', $rejectMixxer)
                ->whereNotIn('id', $mixxerLimit);
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->orderBy('id', 'desc')
                ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);
        }



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
            'start_timestamp',
            'end_time',
            'end_timestamp',
            'location',
            'lat',
            'lng',
            'address'
        )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
            ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);

        if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $startTimestamp = strtotime($startDate . ' 00:00:00');
            $endTimestamp = strtotime($startDate . ' 23:59:59');
            $feature_mixxer->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
            $friend_mixxer->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
            $nearby_mixxer->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
        }

        if ($request->has('start_date') && $request->has('to_date') && $request->start_date != "" && $request->to_date != "") {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $to_date = date('Y-m-d', strtotime($request->to_date));

            $startTimestamp = strtotime($startDate . ' 00:00:00');
            $endTimestamp = strtotime($to_date . ' 23:59:59');
            $feature_mixxer->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
            $friend_mixxer->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
            $nearby_mixxer->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
        }
        if ($request->has('gender') && $request->gender != "") {
            $feature_mixxer->where('gender', $request->gender);
            $friend_mixxer->where('gender', $request->gender);
            $nearby_mixxer->where('gender', $request->gender);
        }
        if ($request->has('age_limit') && $request->age_limit != "") {
            $feature_mixxer->where('age_limit', '<=', $request->age_limit);
            $friend_mixxer->where('age_limit', '<=', $request->age_limit);
            $nearby_mixxer->where('age_limit', '<=', $request->age_limit);
        }
        if ($request->has('categories') && $request->categories != "") {
            $categories = explode(',', $request->categories);
            $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
            $feature_mixxer->whereIn('id', $mixxer_ids);
            $friend_mixxer->whereIn('id', $mixxer_ids);
            $nearby_mixxer->whereIn('id', $mixxer_ids);
        }

        $feature_mixxer = $feature_mixxer->limit(12)->get();
        $friend_mixxer = $friend_mixxer->limit(12)->get();
        $nearby_mixxer = $nearby_mixxer->paginate(12);
        foreach ($feature_mixxer as $item) {
            $categorieIds = explode(',', $item->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item->categories = $categories;
            $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
            $item->is_seen = false;
            if ($find_is_seen) {
                $item->is_seen = true;
            }
        }
        foreach ($nearby_mixxer as $item1) {
            $categorieIds = explode(',', $item1->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item1->categories = $categories;
            $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item1->id)->first();
            $item1->is_seen = false;
            if ($find_is_seen) {
                $item1->is_seen = true;
            }
        }

        foreach ($friend_mixxer as $item2) {
            $categorieIds = explode(',', $item2->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item2->categories = $categories;
            $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item2->id)->first();
            $item2->is_seen = false;
            if ($find_is_seen) {
                $item2->is_seen = true;
            }
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

    public function applyFilterList(Request $request, $type)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $acceptedMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $rejectMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'reject')->pluck('mixxer_id');

        $allMixxers = Mixxer::where('status', 0)->get();
        $mixxerLimit = [];
        foreach ($allMixxers as $oneMixxer) {
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $oneMixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $oneMixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $mixxerLimit[] = $oneMixxer->id;
            }
        }

        $user_interest = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
        $mixxerIds = MixxerCategory::whereIn('category_id', $user_interest)->pluck('mixxer_id');

        $firendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('friend_id');
        $firendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->pluck('user_id');
        $firendIds = $firendIds->merge($firendIds1);
        if ($type == 'feature') {
            // $feature_mixxer  = Mixxer::select(
            //     'id',
            //     'user_id',
            //     'cover',
            //     'title',
            //     'age_limit',
            //     'gender',
            //     'categories',
            //     'start_date',
            //     'is_all_day',
            // 'start_time',
            // 'start_timestamp',
            // 'end_time',
            // 'end_timestamp',
            //     'location',
            //     'lat',
            //     'lng',
            //     'address'
            // )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->whereIn('user_id', $userIds)->orderby('id', 'desc')
            //     ->where('status', 0);

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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('id', $mixxerIds)->where('user_id', '!=', $user->uuid)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->orderby('id', 'desc')
                ->where('status', 0)->inRandomOrder();
        }
        if ($type == 'friend') {
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->whereIn('user_id', $firendIds)->where('user_id', '!=', $user->uuid)->orderby('id', 'desc')
                ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);
        }

        if ($request->has('start_date') && !$request->has('to_date') && $request->start_date != "" && $request->to_date == "") {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $startTimestamp = strtotime($startDate . ' 00:00:00');
            $endTimestamp = strtotime($startDate . ' 23:59:59');
            $mixxers->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
        }

        if ($request->has('start_date') && $request->has('to_date') && $request->start_date != "" && $request->to_date != "") {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $to_date = date('Y-m-d', strtotime($request->to_date));

            $startTimestamp = strtotime($startDate . ' 00:00:00');
            $endTimestamp = strtotime($to_date . ' 23:59:59');
            $mixxers->whereBetween('start_timestamp', [$startTimestamp, $endTimestamp]);
        }
        if ($request->has('gender') && $request->gender != "") {
            $mixxers->where('gender', $request->gender);
        }
        if ($request->has('age_limit') && $request->age_limit != "") {
            $mixxers->where('age_limit', '<=', $request->age_limit);
        }
        if ($request->has('categories') && $request->categories != "") {
            $categories = explode(',', $request->categories);
            $mixxer_ids = MixxerCategory::whereIn('category_id', $categories)->pluck('mixxer_id');
            $mixxers->whereIn('id', $mixxer_ids);
        }
        $mixxers = $mixxers->paginate(12);

        foreach ($mixxers as $item) {
            $categorieIds = explode(',', $item->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item->categories = $categories;
            $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
            $item->is_seen = false;
            if ($find_is_seen) {
                $item->is_seen = true;
            }
        }

        return response()->json([
            'status' => true,
            'action' => 'Mixxers',
            'data' => $mixxers
        ]);
    }

    public function search(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);
        $acceptedMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $rejectMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'reject')->pluck('mixxer_id');
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
        $allMixxers = Mixxer::where('status', 0)->get();
        $mixxerLimit = [];
        foreach ($allMixxers as $oneMixxer) {
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $oneMixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $oneMixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $mixxerLimit[] = $oneMixxer->id;
            }
        }

        $userGender = $user->gender;
        $userAge = $user->age;
        if ($userGender == 'Male') {
            $userGender =  'Women Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Female') {
            $userGender = 'Men Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Non-Binary') {
            $userGender = 'Men Only';
            $userGender1 = 'Women Only';
        }

        if ($request->type == 'mixxer') {
            if ($request->keyword != null || $request->keyword != '') {
                if ($user->age != '' && $user->gender != '') {
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
                        'start_timestamp',
                        'end_time',
                        'end_timestamp',
                        'location',
                        'lat',
                        'lng',
                        'address'
                    )->where("title", "LIKE", "%" . $request->keyword . "%")->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge)->where('status', 0)->latest()->paginate(12);
                } else {
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
                        'start_timestamp',
                        'end_time',
                        'end_timestamp',
                        'location',
                        'lat',
                        'lng',
                        'address'
                    )->where("title", "LIKE", "%" . $request->keyword . "%")->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit)->where('status', 0)->latest()->paginate(12);
                }
                foreach ($mixxers as $item) {
                    $categorieIds = explode(',', $item->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $item->categories = $categories;
                    $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item->id)->first();
                    $item->is_seen = false;
                    if ($find_is_seen) {
                        $item->is_seen = true;
                    }
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

                $user  = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->latest()->paginate(12);

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
        $blocked = BlockedUser::handle($user->uuid);

        $mixxer = Mixxer::with(['user:uuid,first_name,last_name,profile_picture,email,location'])->where('id', $mixxer_id)->first();
        if ($mixxer) {
            $categories = explode(',', $mixxer->categories);
            $category = Category::select('id', 'name', 'image')->whereIn('id', $categories)->get();
            $mixxer->categories = $category;
            $photos = MixxerMedia::where('mixxer_id', $mixxer->id)->where('type', 'image')->get();
            $doc = MixxerMedia::where('mixxer_id', $mixxer->id)->where('type', 'doc')->get();
            $mixxer->photos = $photos;
            $mixxer->doc = $doc;
            $mixxer->participant_count = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->whereNotIn('user_id', $blocked)->where('status', 'accept')->count();
            $mixxer->join_request_count = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'pending')->count();
            $participantIds = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->pluck('user_id');
            $participants = User::whereIn('uuid', $participantIds)->whereNotIn('uuid', $blocked)->limit(12)->get();
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
            $is_disable = MixxerInbox::where('mixxer_id', $mixxer_id)->first();
            if ($is_disable) {
                $mixxer->is_disable = true;
            } else {
                $mixxer->is_disable = false;
            }

            $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $mixxer->id)->first();
            if (!$find_is_seen) {
                $is_seen_new = new MixxerView;
                $is_seen_new->user_id = $user->uuid;
                $is_seen_new->mixxer_id = $mixxer->id;
                $is_seen_new->save();
            }

            return response()->json([
                'status' => true,
                'action' => 'Mixxer Detail',
                'data' => $mixxer
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'Mixxer not found',
        ]);
    }

    public function joinRequest(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $mixxer = Mixxer::find($mixxer_id);
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');
        $owner = User::find($mixxer->user_id);
        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->first();
        if ($find) {
            $find->delete();
            Notification::where('person_id', $user->uuid)->where('user_id', $mixxer->user_id)->where('data_id', $mixxer->id)->where('type', 'join_mixxer_request')->delete();

            return response()->json([
                'status' => true,
                'action' => 'Request Cancel',
            ]);
        }
        $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->count();
        $mixxerUser = $mixxer->limit_audience - 1;
        if ($mixxerUser <= $mixxerAcceptedCount) {
            return response()->json([
                'status' => false,
                'action' => 'Mixxer audience limit reached. No more requests can be send',
            ]);
        }

        $create = new MixxerJoinRequest();
        $create->user_id = $user->uuid;
        $create->mixxer_id = $mixxer_id;
        $create->save();

        NewNotification::handle($mixxer->user_id, $user->uuid, $mixxer_id, ' is requesting to join ' . $mixxer->title, 'mixxer', 'join_mixxer_request');
        $userTokens = UserDevice::where('user_id', $mixxer->user_id)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

        $data1 = [
            'data_id' => $request->mixxer_id,
            'type' => 'join_mixxer_request',
        ];
        $unreadCounts = UserUnreadCount::handle($owner);
        $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' is requesting to join ' . $mixxer->title, $userTokens, $data1, $unreadCounts);

        return response()->json([
            'status' => true,
            'action' => 'Request Send',
        ]);
    }

    public function leave(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->first();
        $mixxer = Mixxer::find($mixxer_id);
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');

        if ($mixxer->status == 0) {
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

                $ownerToken = UserDevice::where('user_id', $mixxer->user_id)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                $owner = User::find($mixxer->user_id);
                $data1 = [
                    'data_id' => $request->mixxer_id,
                    'type' => 'leave_mixxer',
                ];
                $unreadCounts = UserUnreadCount::handle($owner);
                $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $chat_message->message . '.', $ownerToken, $data1, $unreadCounts);
                Notification::where('user_id', $user->uuid)->where('person_id', $mixxer->user_id)->where('data_id', $mixxer->id)->where('type', 'accept_mixxer_request')->delete();

                $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $request->mixxer_id)->where('status', 'accept')->pluck('user_id');
                foreach ($mixxerUserIDs as $id) {
                    $tokens = UserDevice::where('user_id', $id)->whereNotIn('user_id', $userIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                    $data = [
                        'data_id' => $request->mixxer_id,
                        'type' => 'leave_mixxer',
                    ];
                    $otherUser = User::find($id);
                    $unreadCounts = UserUnreadCount::handle($otherUser);
                    $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $chat_message->message . '.', $tokens, $data, $unreadCounts);
                }
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
        return response()->json([
            'status' => false,
            'action' => 'You cannot leave the Mixxer',
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
        $mixxer = Mixxer::find($mixxer_id);
        $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->count();
        $mixxerUser = $mixxer->limit_audience - 1;
        $limit_cross = false;
        if ($mixxerUser <= $mixxerAcceptedCount) {
            $limit_cross = true;
        }
        $user_Ids = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'pending')->pluck('user_id');
        $participants = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $user_Ids)->get();

        return response()->json([
            'status' => true,
            'limit_cross' => $limit_cross,
            'action' => 'Request List',
            'data' => $participants
        ]);
    }

    public function rejectRequest(Request $request)
    {

        $user = User::find($request->user()->uuid);
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');
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
        $otherUser = User::find($request->user_id);
        $find = MixxerJoinRequest::where('user_id', $request->user_id)->where('mixxer_id', $request->mixxer_id)->first();
        if ($find) {
            $find->status = 'reject';
            $find->reason = $request->reason;
            $find->save();
            NewNotification::handle($request->user_id, $mixxer->user_id, $mixxer->id, 'Your request to join ' . $mixxer->title . ' is denied.', 'mixxer', 'reject_mixxer_request');
            Notification::where('user_id', $mixxer->user_id)->where('person_id', $request->user_id)->where('data_id', $mixxer->id)->where('type', 'join_mixxer_request')->delete();

            $tokens = UserDevice::where('user_id', $request->user_id)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            $data = [
                'data_id' => $mixxer->id,
                'type' => 'reject_mixxer_request',
            ];
            $unreadCounts = UserUnreadCount::handle($otherUser);
            $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' has denied your request.', $tokens, $data, $unreadCounts);

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
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');
        $limit_cross = false;
        $user = User::find($request->user_id);
        $mixxer = Mixxer::find($request->mixxer_id);
        $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->count();
        $mixxerUser = $mixxer->limit_audience - 1;
        if ($mixxerUser <= $mixxerAcceptedCount) {
            $limit_cross = true;
            return response()->json([
                'status' => false,
                'limit_cross' => $limit_cross,
                'action' => 'Mixxer audience limit reached. No more requests can be accepted',
            ]);
        }
        $find = MixxerJoinRequest::where('user_id', $request->user_id)->where('mixxer_id', $request->mixxer_id)->first();
        if ($find) {
            $find->status = 'accept';
            $find->save();
            $chat_message = new Message();
            $chat_message->from = $request->user_id;
            $chat_message->to = 0;
            $chat_message->mixxer_id = $request->mixxer_id;
            $chat_message->type = 'join';
            $chat_message->message = 'joined the Mixxer chat';
            $chat_message->time = time();
            $chat_message->save();

            NewNotification::handle($request->user_id, $mixxer->user_id, $mixxer->id, ' accepted your request to join mixxer ' . $mixxer->title, 'mixxer', 'accept_mixxer_request');
            Notification::where('user_id', $mixxer->user_id)->where('person_id', $request->user_id)->where('data_id', $mixxer->id)->where('type', 'join_mixxer_request')->delete();
            $userTokens = UserDevice::where('user_id', $request->user_id)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

            $owner = User::find($mixxer->user_id);
            $data1 = [
                'data_id' => $request->mixxer_id,
                'type' => 'accept_mixxer_request',
            ];
            $unreadCounts = UserUnreadCount::handle($user);
            $this->firebaseNotification->sendNotification($mixxer->title, $owner->first_name . ' accepted your request to join ' . $mixxer->title, $userTokens, $data1, $unreadCounts);

            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $request->mixxer_id)->where('status', 'accept')->pluck('user_id');
            foreach ($mixxerUserIDs as $id) {
                $tokens = UserDevice::where('user_id', $id)->whereNotIn('user_id', $userIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                $data = [
                    'data_id' => $request->mixxer_id,
                    'type' => 'accept_mixxer_request',
                ];
                $otherUser = User::find($id);
                $unreadCounts = UserUnreadCount::handle($otherUser);
                $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $chat_message->message . '.', $tokens, $data, $unreadCounts);
            }
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $mixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $limit_cross = true;
            }

            return response()->json([
                'status' => true,
                'limit_cross' => $limit_cross,
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
        $user = User::find($request->user()->uuid);
        $blocked = BlockedUser::handle($user->uuid);

        $participantIds = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->whereNotIn('user_id', $blocked)->where('status', 'accept')->pluck('user_id');
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
        $blocked = BlockedUser::handle($user->uuid);

        $mixxerIds = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $mixxers = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
            'start_time',
            'start_timestamp',
            'end_time',
            'end_timestamp',
            'categories',
            'created_at'
        )->where('user_id', $user->uuid)->get();
        $mixxers1 = Mixxer::select(
            'id',
            'user_id',
            'cover',
            'title',
            'start_time',
            'start_timestamp',
            'end_time',
            'end_timestamp',
            'categories',
            'created_at'
        )->whereIn('id', $mixxerIds)->whereNotIn('user_id', $blocked)->get();
        $combinedMixxers = $mixxers->merge($mixxers1);

        foreach ($combinedMixxers as $item) {
            $categorieIds = explode(',', $item->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item->categories = $categories;
            $is_disable = MixxerInbox::where('mixxer_id', $item->id)->first();
            if ($is_disable) {
                $item->is_disable = true;
            } else {
                $item->is_disable = false;
            }
            $item->mixxer_create_time = (string)$item->created_at->timestamp;
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
                $item->message  = $obj;
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
        $is_disable = MixxerInbox::where('mixxer_id', $mixxer->id)->first();
        $obj = new stdClass();
        $obj->id = $mixxer->id;
        $obj->name = $mixxer->title;
        $obj->image = $mixxer->cover;
        $categorieIds = explode(',', $mixxer->categories);
        $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
        $obj->categories = $categories;
        if ($is_disable) {
            $obj->is_disable = true;
        } else {
            $obj->is_disable = false;
        }
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
        $blocked = BlockedUser::handle($user->uuid);
        $acceptedMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $rejectMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'reject')->pluck('mixxer_id');
        $allMixxers = Mixxer::where('status', 0)->get();
        $mixxerLimit = [];
        foreach ($allMixxers as $oneMixxer) {
            $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $oneMixxer->id)->where('status', 'accept')->count();
            $mixxerUser = $oneMixxer->limit_audience - 1;
            if ($mixxerUser <= $mixxerAcceptedCount) {
                $mixxerLimit[] = $oneMixxer->id;
            }
        }

        $userGender = $user->gender;
        $userAge = $user->age;
        if ($userGender == 'Male') {
            $userGender =  'Women Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Female') {
            $userGender = 'Men Only';
            $userGender1 = 'Non-Binary Only';
        }
        if ($userGender == 'Non-Binary') {
            $userGender = 'Men Only';
            $userGender1 = 'Women Only';
        }

        if ($user->age != '' && $user->gender != '') {
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->where('user_id', '!=', $user->uuid)
                ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->where('gender', '!=', $userGender)->where('gender', '!=', $userGender1)->where('age_limit', '<=', $userAge)->whereNotIn('id', $mixxerLimit);

            if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
                $userLat = $request->lat ?: 0;
                $userLng = $request->lng ?: 0;
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
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
                'start_timestamp',
                'end_time',
                'end_timestamp',
                'location',
                'lat',
                'lng',
                'address'
            )->where('user_id', '!=', $user->uuid)
                ->where('status', 0)->whereNotIn('user_id', $blocked)->whereNotIn('id', $acceptedMixxer)->whereNotIn('id', $rejectMixxer)->whereNotIn('id', $mixxerLimit);

            if ($request->has('lat') && $request->has('lng') && $request->has('radius')  && $request->lat != "" && $request->lng != "" && $request->radius != "") {
                $userLat = $request->lat ?: 0;
                $userLng = $request->lng ?: 0;
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
                    'start_timestamp',
                    'end_time',
                    'end_timestamp',
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
        }

        foreach ($nearby_mixxer as $item1) {
            $categorieIds = explode(',', $item1->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item1->categories = $categories;
            $find_is_seen = MixxerView::where('user_id', $user->uuid)->where('mixxer_id', $item1->id)->first();
            $item1->is_seen = false;
            if ($find_is_seen) {
                $item1->is_seen = true;
            }
        }
        return response()->json([
            'status' => true,
            'action' => "Mixxer List",
            'data' => $nearby_mixxer
        ]);
    }

    public function inviteUserList(Request $request, $mixxer_id)
    {
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

        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = BlockList::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);

        $friendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->whereNotIn('friend_id', $blocked)->pluck('friend_id');
        $friendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->whereNotIn('user_id', $blocked)->pluck('user_id');
        $friendIds = $friendIds->merge($friendIds1);

        $userIDs = User::whereIn('uuid', $friendIds)->whereNotIn('uuid', $blocked)->pluck('uuid');


        if ($request->type == 'friend') {
            $users = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $userIDs)->latest()->get();
        }
        if ($request->type == 'explore') {
            $users = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->latest()->get();
        }
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
            'action' => 'Users',
            'data' => $users
        ]);
    }

    public function sendInvite(Request $request)
    {
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');

        $user = User::find($request->user()->uuid);
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
            Notification::where('person_id', $mixxer->user_id)->where('user_id', $request->user_id)->where('data_id', $mixxer->id)->where('type', 'send_mixxer_invite')->delete();

            return response()->json([
                'status' => true,
                'action' => 'Invite Remove',
            ]);
        }
        $user1 = User::find($request->user_id);

        $create = new MixxerJoinRequest();
        $create->user_id = $request->user_id;
        $create->mixxer_id = $request->mixxer_id;
        $create->status = 'invite';
        $create->save();
        NewNotification::handle($request->user_id, $mixxer->user_id, $request->mixxer_id, 'mixxer', ' sent you a Mixxer invite', 'send_mixxer_invite');
        $tokens = UserDevice::where('user_id', $user1->uuid)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

        $data = [
            'data_id' => $request->mixxer_id,
            'type' => 'send_mixxer_invite',
        ];
        $unreadCounts = UserUnreadCount::handle($user1);
        $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' sent you a Mixxer invite.', $tokens, $data, $unreadCounts);

        return response()->json([
            'status' => true,
            'action' => 'Invite Send',
        ]);
    }

    public function acceptInviteRequest(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $mixxer = Mixxer::find($mixxer_id);
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');

        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->where('status', 'invite')->first();
        $mixxerAcceptedCount = MixxerJoinRequest::where('mixxer_id', $mixxer->id)->where('status', 'accept')->count();
        $mixxerUser = $mixxer->limit_audience - 1;
        if ($mixxerUser <= $mixxerAcceptedCount) {
            return response()->json([
                'status' => false,
                'action' => 'Mixxer full. You couldn’t join in time',
            ]);
        }


        if ($find) {
            $find->status = 'accept';
            $find->save();
            $chat_message = new Message();
            $chat_message->from = $user->uuid;
            $chat_message->to = 0;
            $chat_message->mixxer_id = $mixxer_id;
            $chat_message->type = 'join';
            $chat_message->message = 'joined the Mixxer chat';
            $chat_message->time = time();
            $chat_message->save();
            NewNotification::handle($mixxer->user_id, $user->uuid, $mixxer_id, ' accepted your invite to join ', 'mixxer', 'accept_mixxer_invite');

            $ownerToken = UserDevice::where('user_id', $mixxer->user_id)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();


            $data1 = [
                'data_id' => $request->mixxer_id,
                'type' => 'accept_mixxer_invite',
            ];
            $owner = User::find($mixxer->user_id);
            $unreadCounts = UserUnreadCount::handle($owner);
            $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $user->last_name . ' accepted your invite to join ' . $mixxer->title, $ownerToken, $data1, $unreadCounts);


            $mixxerUserIDs = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('status', 'accept')->pluck('user_id');
            foreach ($mixxerUserIDs as $id) {
                $tokens = UserDevice::where('user_id', $id)->whereNotIn('user_id', $userIDs)->where('user_id', '!=', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
                $data = [
                    'data_id' => $request->mixxer_id,
                    'type' => 'accept_mixxer_invite',
                ];
                $otherUser = User::find($id);
                $unreadCounts = UserUnreadCount::handle($otherUser);
                $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' ' . $chat_message->message . '.', $tokens, $data, $unreadCounts);
            }

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

        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = BlockList::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);

        if ($request->type == 'friend') {
            if ($request->keyword != null || $request->keyword != '') {

                $friendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->whereNotIn('friend_id', $blocked)->pluck('friend_id');
                $friendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->whereNotIn('user_id', $blocked)->pluck('user_id');
                $friendIds = $friendIds->merge($friendIds1);


                $users  = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->whereIn('uuid', $friendIds)->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->whereNotIn('uuid', $blocked)->whereIn('uuid', $friendIds)->latest()->get();
                foreach ($users as $item) {
                    $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $item->uuid)->first();
                    if ($request_check) {
                        $item->is_invite = true;
                    } else {
                        $item->is_invite = false;
                    }
                }
            } else {
                $users = new stdClass();
            }
        }
        if ($request->type == 'explore') {
            if ($request->keyword != null || $request->keyword != '') {
                $users  = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->where("first_name", "LIKE", "%" . $request->keyword . "%")->orWhere("last_name", "LIKE", "%" . $request->keyword . "%")->whereNotIn('uuid', $blocked)->where('uuid', '!=', $user->uuid)->latest()->get();
                foreach ($users as $item) {
                    $request_check = MixxerJoinRequest::where('mixxer_id', $mixxer_id)->where('user_id', $item->uuid)->first();
                    if ($request_check) {
                        $item->is_invite = true;
                    } else {
                        $item->is_invite = false;
                    }
                }
            } else {
                $users = new stdClass();
            }
        }



        return response()->json([
            'status' => true,
            'action' => 'Users',
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

    public function friendlyCheck(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new MixxerFriendlyCheck();
        $create->user_id = $user->uuid;
        $create->mixxer_id = $request->mixxer_id;
        $create->friendly_check = $request->check;
        $create->save();
        Notification::where('user_id', $user->uuid)->where('data_id', $request->mixxer_id)->where('type', 'mixxer_friendly_check')->delete();

        return response()->json([
            'status' => true,
            'action' => 'Feedback Send',
        ]);
    }

    public function feedback(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'mixxer_id' => 'required|exists:mixxers,id',
            'experience' => 'required',
            'highlights' => 'required',
            'experience_encourage' => 'required',
            'improvement' => 'required',
            'have_fun' => 'required',
            'experience_socializing' => 'required',
            'group_fun' => 'required',
            'rate_the_venue' => 'required',
            'virtual_setting' => 'required',
            'additional_comment' => 'required',
            'expecting' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = new MixxerFeedback();
        $create->user_id = $user->uuid;
        $create->mixxer_id = $request->mixxer_id;
        $create->experience = $request->experience;
        $create->highlights = $request->highlights;
        $create->experience_encourage = $request->experience_encourage;
        $create->improvement = $request->improvement;
        $create->expecting = $request->expecting;
        $create->have_fun = $request->have_fun;
        $create->experience_socializing = $request->experience_socializing;
        $create->group_fun = $request->group_fun;
        $create->rate_the_venue = $request->rate_the_venue;
        $create->virtual_setting = $request->virtual_setting;
        $create->additional_comment = $request->additional_comment;
        $create->save();

        Notification::where('user_id', $user->uuid)->where('data_id', $request->mixxer_id)->where('type', 'mixxer_chat_disable')->delete();
        return response()->json([
            'status' => true,
            'action' =>  'Feedback Added',
        ]);
    }

    public function friendlyCheckFeedback(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'mixxer_id' => 'required|exists:mixxers,id',
            'issue_you_experiencing' => 'required',
            'provide_more_details' => 'required',
        ]);

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = new MixxerFriendlyCheckFeedback();
        $create->user_id = $user->uuid;
        $create->mixxer_id = $request->mixxer_id;
        $create->experience = '';
        $create->highlights = '';
        $create->experience_encourage = '';
        $create->improvement = '';
        $create->expecting = '';
        $create->have_fun = '';
        $create->experience_socializing = '';
        $create->group_fun = '';
        $create->rate_the_venue = '';
        $create->virtual_setting = '';
        $create->additional_comment = '';
        $create->issue_you_experiencing = $request->issue_you_experiencing;
        $create->provide_more_details = $request->provide_more_details;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Friendly Feedback Added',
        ]);
    }

    public function removeParticipant($mixxer_id, $user_id)
    {
        $find = MixxerJoinRequest::where('user_id', $user_id)->where('mixxer_id', $mixxer_id)->where('status', 'accept')->first();
        if ($find) {
            $find->delete();

            return response()->json([
                'status' => true,
                'action' => 'Participant Removed!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Participant Found',
        ]);
    }

    public function rejectInviteRequest(Request $request, $mixxer_id)
    {
        $user = User::find($request->user()->uuid);
        $mixxer = Mixxer::find($mixxer_id);
        $userIDs = NotificationAllow::where('is_allow', 0)->pluck('user_id');

        $find = MixxerJoinRequest::where('user_id', $user->uuid)->where('mixxer_id', $mixxer_id)->where('status', 'invite')->first();
        if ($find) {
            $find->delete();
            Notification::where('person_id', $mixxer->user_id)->where('user_id', $request->user_id)->where('data_id', $mixxer->id)->where('type', 'send_mixxer_invite')->delete();
            $owner = User::find($mixxer->user_id);
            // NewNotification::handle($mixxer->user_id, $user->uuid, $mixxer_id, ' has rejected your invitation to join ', 'mixxer', 'reject_mixxer_invite');
            // $ownerToken = UserDevice::where('user_id', $mixxer->user_id)->whereNotIn('user_id', $userIDs)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            // $data1 = [
            //     'data_id' => $request->mixxer_id,
            //     'type' => 'reject_mixxer_invite',
            // ];
            // $unreadCounts = UserUnreadCount::handle($owner);
            // $this->firebaseNotification->sendNotification($mixxer->title, $user->first_name . ' has rejected your invitation to join.', $ownerToken, $data1, $unreadCounts);
            return response()->json([
                'status' => true,
                'action' => 'Invitation Rejected!',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' => 'No Invitation Find',
        ]);
    }
}
