<?php

namespace App\Http\Controllers\Api;

use App\Actions\NewNotification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\AddInterestRequest;
use App\Http\Requests\Api\ReportRequest;
use App\Models\BlockList;
use App\Models\Category;
use App\Models\FeatureRequest;
use App\Models\FriendRequest;
use App\Models\Message;
use App\Models\MessageRead;
use App\Models\Mixxer;
use App\Models\MixxerJoinRequest;
use App\Models\Notification;
use App\Models\Report;
use App\Models\SaveMixxer;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserInterest;
use App\Models\UserSubscription;
use App\Services\FirebaseNotificationService;
use DateTime;
use Illuminate\Foundation\Mix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UserController extends Controller
{
    protected $firebaseNotification;

    public function __construct(FirebaseNotificationService $firebaseNotification)
    {
        $this->firebaseNotification = $firebaseNotification;
    }
    public function addInterest(AddInterestRequest $request)
    {
        $user = User::find($request->user()->uuid);
        UserInterest::where('user_id', $user->uuid)->delete();
        $categoriesIds = explode(',', $request->categories);

        foreach ($categoriesIds as $category) {
            $find = Category::find($category);
            if ($find) {
                $create = new UserInterest();
                $create->user_id = $user->uuid;
                $create->category_id = $category;
                $create->category_name = $find->name;
                $create->save();
            } else {
                return response()->json([
                    'status' => false,
                    'action' => $category . " Catgeory id is inValid"
                ]);
            }
        }
        return response()->json([
            'status' => true,
            'action' => "Interest Added",
        ]);
    }
    public function updateUser(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }

        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        if ($request->has('email')) {
            if (User::where('email', $request->email)->where('uuid', '!=', $user->uuid)->exists()) {
                return response()->json([
                    'status' => false,
                    'action' => 'Email Address is already registered'
                ]);
            } else {
                $user->email = $request->email;
            }
        }

        if ($request->has('location')) {
            if ($request->location == null) {
                $user->location = '';
                $user->lat = '';
                $user->lng = '';
            } else {
                $user->location = $request->location;
                $user->lat = $request->lat;
                $user->lng = $request->lng;
            }
        }
        if ($request->has('instagram_username')) {
            if ($request->instagram_username == null) {
                $user->instagram_username = '';
            } else {
                $user->instagram_username = $request->instagram_username;
            }
        }
        if ($request->has('instagram_profile')) {
            if ($request->instagram_profile == null) {
                $user->instagram_profile = '';
            } else {
                $user->instagram_profile = $request->instagram_profile;
            }
        }

        if ($request->has('bio')) {
            if ($request->bio == null) {
                $user->bio = '';
            } else {
                $user->bio = $request->bio;
            }
        }

        if ($request->has('age')) {
            if ($request->age == null) {
                $user->age = '';
            } else {
                // $birthdate = new DateTime($request->age);
                // $today = new DateTime();
                // $age = $today->diff($birthdate)->y;
                $user->age = $request->age;
            }
        }
        if ($request->has('gender')) {
            if ($request->gender == null) {
                $user->gender = '';
            } else {
                $user->gender = $request->gender;
            }
        }

        if ($request->has('religion')) {
            if ($request->religion == null) {
                $user->religion = '';
            } else {
                $user->religion = $request->religion;
            }
        }

        if ($request->has('education')) {
            if ($request->education == null) {
                $user->education = '';
            } else {
                $user->education = $request->education;
            }
        }
        if ($request->has('occupation')) {
            if ($request->occupation == null) {
                $user->occupation = '';
            } else {
                $user->occupation = $request->occupation;
            }
        }
        if ($request->has('ethnicity')) {
            if ($request->ethnicity == null) {
                $user->ethnicity = '';
            } else {
                $user->ethnicity = $request->ethnicity;
            }
        }
        if ($request->has('language')) {
            if ($request->language == null) {
                $user->language = '';
            } else {
                $user->language = $request->language;
            }
        }
        $user->save();
        $token = $request->bearerToken();
        $user->token = $token;
        $interest = UserInterest::where('user_id', $user->uuid)->first();
        if ($interest) {
            $catIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
            $categories  = Category::select('id', 'name', 'image')->whereIn('id', $catIds)->get();
            foreach ($categories as $item) {
                $item->is_added = true;
            }
            $user->interest = $categories;
        } else {
            $user->interest = [];
        }
        $is_subscribe = UserSubscription::where('user_id', $user->uuid)->first();
        if ($is_subscribe) {
            $user->is_subscribe = true;
        } else {
            $user->is_subscribe = false;
        }

        return response()->json([
            'status' => true,
            'action' => "Profile Edit",
            'data' => $user
        ]);
    }

    public function blockUser(Request $request, $block_id)
    {
        $user = User::find($request->user()->uuid);

        $check = Blocklist::where('block_id', $block_id)->where('user_id',  $user->uuid)->first();
        if ($check) {
            $check->delete();
            return response()->json([
                'status' => true,
                'action' => 'User unblocked'
            ]);
        } else {
            $block = new Blocklist;
            $block->block_id = $block_id;
            $block->user_id = $user->uuid;
            $block->save();
            return response()->json([
                'status' => true,
                'action' => 'User blocked'
            ]);
        }
    }

    public function blockList(Request $request)
    {
        $user = User::find($request->user()->uuid);

        $block_ids = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blockUsers = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $block_ids)->get();
        foreach ($blockUsers as $block) {
            $block->block = true;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Block list',
            'data' => $blockUsers
        ]);
    }

    public function sendRequest(Request $request, $friend_id)
    {
        $user = User::find($request->user()->uuid);
        $user1 = User::find($friend_id);
        $find = FriendRequest::where('user_id', $user->uuid)->where('friend_id', $friend_id)->first();
        $find1 = FriendRequest::where('friend_id', $user->uuid)->where('user_id', $friend_id)->first();
        if ($find1) {
            Notification::where('person_id', $friend_id)->where('user_id', $user->uuid)->where('type', 'send_request')->delete();
            $find1->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Firend Remove',
            ]);
        }
        if ($find) {
            Notification::where('person_id', $user->uuid)->where('user_id', $friend_id)->where('type', 'send_request')->delete();
            $find->delete();
            return response()->json([
                'status' => true,
                'action' =>  'Firend Remove',
            ]);
        }
        $create = new FriendRequest();
        $create->user_id = $user->uuid;
        $create->friend_id = $friend_id;
        $create->save();

        NewNotification::handle($friend_id, $user->uuid, 0, 'has sent you a friend request', 'normal', 'send_request');
        $tokens = UserDevice::where('user_id', $user1->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

      
        $data = [
            'data_id' => $user->uuid,
            'type' => 'send_request',
        ];

        $this->firebaseNotification->sendNotification('Friend Request', $user->first_name .' '. $user->last_name. ' has sent you a friend request.', $tokens, $data,1);



        return response()->json([
            'status' => true,
            'action' =>  'Firend Request Send',
        ]);
    }

    public function profile(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = Blocklist::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);
        if ($user->uuid == $to_id) {
            $user->is_block = false;
            $user->is_friend = 'friend';
            $friendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->whereNotIn('friend_id', $blocked)->pluck('friend_id');
            $friendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->whereNotIn('user_id', $blocked)->pluck('user_id');
            $friendIds = $friendIds->merge($friendIds1);
            $total_friend = count($friendIds);
            $user->total_friend = $total_friend;
            $user->friend = User::whereIn('uuid', $friendIds)->limit(4)->pluck('profile_picture');
            $interestIds = UserInterest::where('user_id', $user->uuid)->pluck('category_id');
            $interests = Category::select('id', 'name', 'image')->whereIn('id', $interestIds)->get();
            foreach ($interests as $interest) {
                $interest->is_added = true;
            }
            $user->interest = $interests;
            $user->total_mixxers_hosted = Mixxer::where('user_id', $user->uuid)->count();
            $user->total_mixxers_attended = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->count();
            $user->total_mixxers_together = 0;
            $mixer_hosted = Mixxer::select(
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
            )->where('user_id', $user->uuid)->latest()->limit(12)->get();

            $user->mixxers_hosted = $mixer_hosted;
            $joinedMixxerIds = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
            $joinedMixxer = Mixxer::select(
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
            )->whereIn('id', $joinedMixxerIds)->where('status',2)->latest()->limit(12)->get();
            foreach ($joinedMixxer as $item2) {
                $categorieIds = explode(',', $item2->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item2->categories = $categories;
            }
            $user->mixxers_joined = $joinedMixxer;
            $saved_mixxer_ids = SaveMixxer::where('user_id', $user->uuid)->orderBy('id', 'desc')->pluck('mixxer_id');
            $saved_mixxer = [];
            foreach ($saved_mixxer_ids as $item1) {
                $mixxer = Mixxer::select(
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
                )->where('id', $item1)->first();

                $saved_mixxer[] = $mixxer;
            }
            foreach ($mixer_hosted as $item) {
                $categorieIds = explode(',', $item->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item->categories = $categories;
            }
            foreach ($saved_mixxer as $item1) {
                $categorieIds = explode(',', $item1->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item1->categories = $categories;
            }

            $user->mixxers_saved = $saved_mixxer;
            return response()->json([
                'status' => true,
                'action' =>  'User Profle',
                'data' => $user
            ]);
        } else {
            $user1 = User::find($to_id);
            $block = Blocklist::where('user_id', $user->uuid)->where('block_id', $to_id)->first();
            if ($block)
                $user1->is_block = true;
            else
                $user1->is_block = false;

            $friend = FriendRequest::where('user_id', $user->uuid)->where('friend_id', $user1->uuid)->first();
            $friend1 = FriendRequest::where('friend_id', $user->uuid)->where('user_id', $user1->uuid)->first();
            if ($friend) {
                if ($friend->status == 'pending') {
                    $user1->is_friend = 'cancel_request';
                } elseif ($friend->status == 'accept') {
                    $user1->is_friend = 'friend';
                } else {
                    $user1->is_friend = 'add_friend';
                }
            } elseif ($friend1) {
                if ($friend1->status == 'pending') {
                    $user1->is_friend = 'accept_request';
                } elseif ($friend1->status == 'accept') {
                    $user1->is_friend = 'friend';
                } else {
                    $user1->is_friend = 'add_friend';
                }
            } else {
                $user1->is_friend = 'add_friend';
            }

            $friendIds = FriendRequest::where('user_id', $user1->uuid)->where('status', 'accept')->whereNotIn('friend_id', $blocked)->pluck('friend_id');
            $friendIds1 = FriendRequest::where('friend_id', $user1->uuid)->where('status', 'accept')->whereNotIn('user_id', $blocked)->pluck('user_id');
            $friendIds = $friendIds->merge($friendIds1);
            $total_friend = count($friendIds);
            $user1->total_friend = $total_friend;
            $user1->friend = User::whereIn('uuid', $friendIds)->limit(4)->pluck('profile_picture');
            $interestIds = UserInterest::where('user_id', $to_id)->pluck('category_id');
            $interests = Category::select('id', 'name', 'image')->whereIn('id', $interestIds)->get();
            foreach ($interests as $interest) {
                $interest->is_added = true;
            }

            $user1->interest = $interests;
            $user1->total_mixxers_hosted = Mixxer::where('user_id', $user1->uuid)->count();
            $user1->total_mixxers_attended =  MixxerJoinRequest::where('user_id', $user1->uuid)->where('status', 'accept')->count();
            $userMixxer = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
            $user1Mixxer = MixxerJoinRequest::where('user_id', $user1->uuid)->where('status', 'accept')->pluck('mixxer_id');
            $commonMixxers = $userMixxer->intersect($user1Mixxer);
            $commonMixxerCount = $commonMixxers->count();
            $user1->total_mixxers_together = $commonMixxerCount;
            $mixer_hosted = Mixxer::select(
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
            )->where('user_id', $user1->uuid)->latest()->limit(12)->get();

            foreach ($mixer_hosted as $item) {
                $categorieIds = explode(',', $item->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item->categories = $categories;
            }

            $user1->mixxers_hosted = $mixer_hosted;
            $joinedMixxerIds = MixxerJoinRequest::where('user_id', $user1->uuid)->where('status', 'accept')->pluck('mixxer_id');
            $joinedMixxer = Mixxer::select(
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
            )->whereIn('id', $joinedMixxerIds)->latest()->limit(12)->get();
            foreach ($joinedMixxer as $item1) {
                $categorieIds = explode(',', $item1->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item1->categories = $categories;
            }
            $user1->mixxers_joined = $joinedMixxer;
            $user1->mixxers_saved = [];
            return response()->json([
                'status' => true,
                'action' =>  'User Profle',
                'data' => $user1
            ]);
        }
    }

    public function profileMixxer(Request $request, $type, $user_id)
    {
        if ($type == 'hosted') {
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
            )->where('user_id', $user_id)->latest()->paginate(12);
        }
        if ($type == 'joined') {
            $mixxerIds = MixxerJoinRequest::where('user_id', $user_id)->where('status', 'accept')->pluck('mixxer_id');
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
            )->whereIn('id', $mixxerIds)->where('status',2)->latest()->paginate(12);
        }

        if ($type == 'saved') {
            $saved_mixxer_ids = SaveMixxer::where('user_id', $user_id)->orderBy('id', 'desc')->pluck('mixxer_id');

            $savedMixxers = [];
            foreach ($saved_mixxer_ids as $item1) {
                $mixxer = Mixxer::select(
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
                )->where('id', $item1)->first();

                $savedMixxers[] = $mixxer;
            }

            foreach ($savedMixxers as $item) {
                $categorieIds = explode(',', $item->categories);
                $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                $item->categories = $categories;
            }

            $count  = count($savedMixxers);
            $savedMixxers = collect($savedMixxers);
            $savedMixxers = $savedMixxers->forPage($request->page, 12)->values();

            return response()->json([
                'status' => true,
                'action' =>  'Mixxers',
                'data' => array(
                    'data' => $savedMixxers,
                    'total' => $count
                )
            ]);
        }

        foreach ($mixxers as $item) {
            $categorieIds = explode(',', $item->categories);
            $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
            $item->categories = $categories;
        }

        return response()->json([
            'status' => true,
            'action' =>  'Mixxers',
            'data' => $mixxers
        ]);
    }


    public function acceptRequest(Request $request, $friend_id)
    {
        $user = User::find($request->user()->uuid);
        $find = FriendRequest::where('user_id', $user->uuid)->where('friend_id', $friend_id)->first();
        $find1 = FriendRequest::where('friend_id', $user->uuid)->where('user_id', $friend_id)->first();
        if ($find1) {
            $find1->status = 'accept';
            $find1->save();
            NewNotification::handle($friend_id, $user->uuid, 0, 'has accepted your friend request', 'normal', 'accept_request');

            $tokens = UserDevice::where('user_id', $friend_id)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

            $data = [
                'data_id' => $user->uuid,
                'type' => 'accept_request',
            ];
    
            $this->firebaseNotification->sendNotification('Friend Request', $user->first_name .' '. $user->last_name. ' has accepted your friend request.', $tokens, $data,1);
    
            return response()->json([
                'status' => true,
                'action' =>  'Firend Request Accept',
            ]);
        }
        if ($find) {
            $find->status = 'accept';
            $find->save();
            NewNotification::handle($user->uuid, $friend_id, 0, 'has accepted your friend request', 'normal', 'accept_request');
            $tokens = UserDevice::where('user_id', $user->uuid)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();

            $data = [
                'data_id' => $user->uuid,
                'type' => 'accept_request',
            ];
    
            $this->firebaseNotification->sendNotification('Friend Request', $user->first_name .' '. $user->last_name . ' has accepted your friend request.', $tokens, $data,1);
    
            return response()->json([
                'status' => true,
                'action' =>  'Firend Request Accept',
            ]);
        }
    }

    public function featureRequest(Request $request)
    {
        $user = User::find($request->user()->uuid);
        $validator = Validator::make($request->all(), [
            'experience' => "required",
            'about' => 'required',
            'feature' => 'required'
        ]);
        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }

        $create = new FeatureRequest();
        $create->user_id = $user->uuid;
        $create->experience = $request->experience;
        $create->about = $request->about;
        $create->feature = $request->feature;
        $create->save();
        return response()->json([
            'status' => true,
            'action' =>  'Feature Request Added',
        ]);
    }


    public function report(ReportRequest $request)
    {
        $user = User::find($request->user()->uuid);
        $create = new Report();
        $create->user_id = $user->uuid;
        $create->type = $request->type;
        $create->reported_id = $request->reported_id;
        $create->message = $request->message;
        $create->save();

        return response()->json([
            'status' => true,
            'action' =>  'Report Added',
        ]);
    }

    public function myProfile(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            return response()->json([
                'status' => true,
                'action' =>  'My Profile',
                'data' => $user
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'User not Found',
        ]);
    }

    public function friendList(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);
        $blocked = BlockList::where('user_id', $user->uuid)->pluck('block_id');
        $blocked1 = Blocklist::where('block_id', $user->uuid)->pluck('user_id');
        $blocked = $blocked->merge($blocked1);

        if ($user->uuid == $to_id) {


            $friendIds = FriendRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('friend_id');
            $friendIds1 = FriendRequest::where('friend_id', $user->uuid)->where('status', 'accept')->pluck('user_id');
            $friendIds = $friendIds->merge($friendIds1);

            $friends = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $friendIds)->whereNotIn('uuid', $blocked)->paginate(12);
            foreach ($friends as $item) {
                $item->is_friend = true;
            }
        } else {
            $user1 = User::find($to_id);
            $friendIds = FriendRequest::where('user_id', $user1->uuid)->where('status', 'accept')->pluck('friend_id');
            $friendIds1 = FriendRequest::where('friend_id', $user1->uuid)->where('status', 'accept')->pluck('user_id');
            $friendIds = $friendIds->merge($friendIds1);

            $friends = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->whereIn('uuid', $friendIds)->whereNotIn('uuid', $blocked)->paginate(12);
            foreach ($friends as $item) {
                $check = FriendRequest::where('user_id', $item->uuid)->where('friend_id', $user->uuid)->where('status', 'accept')->first();
                $check1 = FriendRequest::where('friend_id', $item->uuid)->where('user_id', $user->uuid)->where('status', 'accept')->first();
                if ($check || $check1) {
                    $item->is_friend = true;
                } else {
                    $item->is_friend = false;
                }
            }
        }
        return response()->json([
            'status' => true,
            'action' =>  'Friend List',
            'data' => $friends
        ]);
    }

    public function unreadCounter(Request $request)
    {

        $user = User::find($request->user()->uuid);

        $message_count = Message::where('to', $user->uuid)->where('is_read', 0)->count();
        $notification_count = Notification::where('user_id', $user->uuid)->where('is_read', 0)->count();
        $ticket_unread_counter = Message::where('to', $user->uuid)->where('ticket_id', '!=', 0)->where('is_read', 0)->count();
        $mixxerIds = MixxerJoinRequest::where('user_id', $user->uuid)->where('status', 'accept')->pluck('mixxer_id');
        $mixxers = Mixxer::where('status', '!=', 2)->where('user_id', $user->uuid)->pluck('id');
        $mixxers1 = Mixxer::whereIn('id', $mixxerIds)->where('status', '!=', 2)->pluck('id');
        $combinedMixxers = $mixxers->merge($mixxers1);
        $total_message = Message::where('from','!=',$user->uuid)->whereIn('mixxer_id', $combinedMixxers)->count();
        $messageIDs = Message::where('from','!=',$user->uuid)->whereIn('mixxer_id', $combinedMixxers)->pluck('id');
        $total_read_message = MessageRead::whereIn('message_id', $messageIDs)->where('user_id', $user->uuid)->count();
        $total_unread_message = $total_message - $total_read_message;

        return response()->json([
            'status' => true,
            'action' =>  'Counter',
            'data' => array(
                'message_count' => $message_count,
                'mixxer_count' => $total_unread_message,
                'notification_count' => $notification_count,
                'ticket_message_count' => $ticket_unread_counter
            )
        ]);
    }

    public function notification(Request $request)
    {
        $user = User::find($request->user()->uuid);
        if ($user) {
            $notifications = Notification::where('user_id', $user->uuid)->latest()->paginate(12);
            // Notification::where('user_id', $user_id)->where('is_read', 0)->update(['is_read' => 1]);
            foreach ($notifications as $index => $notif) {

                $person = User::select('uuid', 'first_name', 'last_name', 'profile_picture', 'email', 'location')->where('uuid', $notif->person_id)->first();

                $checkDate = $notif->date;
                if ($index == 0 && !$request->page || $request->page == 1 && $index == 0) {
                    $notif->first = true;
                } elseif ($index == 0 && $request->page && $request->page != 1) {
                    $notisOld = Notification::select('date')->where('date', '!=', '')->where('user_id',  $user->uuid)->limit(12)->skip(($request->page - 1) * 12)->orderBy('date', 'DESC')->get();
                    $current = date_format(date_create($checkDate), 'Y-m-d');
                    $previousDate = $notisOld[0]->date;
                    $next = date_format(date_create($previousDate), 'Y-m-d');
                    if ($current == $next)
                        $notif->first = false;
                    else
                        $notif->first = true;
                } else {
                    if ($index - 1 >= 0) {
                        $current = date_format(date_create($checkDate), 'Y-m-d');
                        $previousDate = $notifications[$index - 1]->date;
                        $next = date_format(date_create($previousDate), 'Y-m-d');
                        if ($current == $next)
                            $notif->first = false;
                        else
                            $notif->first = true;
                    }
                }
                $dbCheck = date_format(date_create($checkDate), 'Y-m-d');
                $date = date_format(date_create($checkDate), 'D, d F');
                $tomorrow = date("Y-m-d", strtotime("-1 days"));
                $todayDate = date('Y-m-d');
                if ($dbCheck == $tomorrow)
                    $notif->date = 'Yesterday';
                elseif ($dbCheck == $todayDate)
                    $notif->date = 'Today';
                else
                    $notif->date = $date;
                $mixxer = Mixxer::select(
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
                )->where('id', $notif->data_id)->first();
                if ($mixxer) {
                    $categorieIds = explode(',', $mixxer->categories);
                    $categories = Category::select('id', 'name', 'image')->whereIn('id', $categorieIds)->get();
                    $mixxer->categories = $categories;
                    $notif->mixxer = $mixxer;
                } else {
                    $notif->mixxer = new stdClass();
                }
                $notif->user = $person;
            }
            return response()->json([
                'status' => true,
                'action' =>  'Notifications',
                'data' => $notifications,
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'No User Found',
        ]);
    }
    public function notificationRead(Request $request)
    {
        $user = User::find($request->user()->uuid);

        if ($user) {
            Notification::where('user_id', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);
            return response()->json([
                'status' => true,
                'action' =>  'Notification Read',
            ]);
        }
        return response()->json([
            'status' => false,
            'action' =>  'No User Found',
        ]);
    }
}
