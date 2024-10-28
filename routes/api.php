<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MixxerController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('user/auth')->group(function () {
    Route::post('verify', [AuthController::class, 'verify']);
    Route::post('otp/verify', [AuthController::class, 'otpVerify']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('password/forgot', [AuthController::class, 'recover']);
    Route::post('password/reset', [AuthController::class, 'newPassword']);
    Route::post('social', [AuthController::class, 'socialLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('password/change', [AuthController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('edit/image', [AuthController::class, 'editImage']);
        Route::get('remove/image', [AuthController::class, 'removeImage']);
        Route::get('delete', [AuthController::class, 'deleteAccount']);


        Route::post('add/interest', [UserController::class, 'addInterest']);
        Route::post('edit/profile', [UserController::class, 'updateUser']);
        Route::get('block/list', [UserController::class, 'blockList']);
        Route::get('block/user/{block_id}', [UserController::class, 'blockUser']);
    });
});

Route::prefix('user/')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('send/request/{friend_id}', [UserController::class, 'sendRequest']);
        Route::get('reject/request/{friend_id}', [UserController::class, 'rejectRequest']);
        Route::get('accept/request/{friend_id}', [UserController::class, 'acceptRequest']);
        Route::get('profile/{to_id}', [UserController::class, 'profile']);
        Route::post('send/feature/request', [UserController::class, 'featureRequest']);
        Route::post('report', [UserController::class, 'report']);
        Route::get('my/profile', [UserController::class, 'myProfile']);
        Route::get('mixxer/profile/list/{type}/{user_id}', [UserController::class, 'profileMixxer']);
        Route::get('friend/list/{user_id}', [UserController::class, 'friendList']);
        Route::get('counter', [UserController::class, 'unreadCounter']);
        Route::get('notification', [UserController::class, 'notification']);
        Route::get('notification/read', [UserController::class, 'notificationRead']);
        Route::get('notification/status/change/{status}', [UserController::class, 'changeNotifyStatus']);
        Route::get('notification/status/check', [UserController::class, 'NotifyStatusCheck']);
        Route::post('new/feature/request', [UserController::class, 'featureRequestNew']);
        Route::post('report/user', [UserController::class, 'reportUser']);
        Route::post('app/feedback', [UserController::class, 'appFeedback']);


    });
});

Route::prefix('user/ticket')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [TicketController::class, 'create']);
        Route::get('list/{status}', [TicketController::class, 'list']);
        Route::get('close/{ticket_id}', [TicketController::class, 'close']);
        Route::get('conversation/{ticket_id}', [TicketController::class, 'conversation']);
    });
});


Route::prefix('user/message')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('send', [MessageController::class, 'send']);
        Route::get('list/{to_id}', [MessageController::class, 'conversation']);
        Route::get('inbox', [MessageController::class, 'inbox']);
        Route::get('read/{from_to}', [MessageController::class, 'messageRead']);

    });
});

Route::prefix('user/mixxer')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [MixxerController::class, 'create']);
        Route::post('edit', [MixxerController::class, 'edit']);
        Route::get('save/{id}', [MixxerController::class, 'save']);
        Route::post('home', [MixxerController::class, 'home']);
        Route::post('apply/filter', [MixxerController::class, 'applyFilter']);
        Route::post('apply/filter/list/{type}', [MixxerController::class, 'applyFilterList']);
        Route::post('search', [MixxerController::class, 'search']);
        Route::get('detail/{mixxer_id}', [MixxerController::class, 'detail']);
        Route::get('join/request/{mixxer_id}', [MixxerController::class, 'joinRequest']);
        Route::get('leave/{mixxer_id}', [MixxerController::class, 'leave']);
        Route::get('delete/{mixxer_id}', [MixxerController::class, 'delete']);
        Route::get('request/list/{mixxer_id}', [MixxerController::class, 'joinRequestList']);
        Route::post('request/reject', [MixxerController::class, 'rejectRequest']);
        Route::post('request/accept', [MixxerController::class, 'acceptRequest']);
        Route::get('participant/list/{mixxer_id}', [MixxerController::class, 'participantList']);
        Route::get('inbox', [MixxerController::class, 'inbox']);
        Route::get('list/{type}', [MixxerController::class, 'list']);
        Route::get('change/status/{mixxer_id}/{status}', [MixxerController::class, 'changeStatus']);
        Route::get('messages/list/{mixxer_id}', [MixxerController::class, 'conversation']);
        Route::get('messages/read/{mixxer_id}', [MixxerController::class, 'messageRead']);
        Route::post('nearby/list', [MixxerController::class, 'nearBy']);
        Route::post('invite/list/{mixxer_id}', [MixxerController::class, 'inviteUserList']);
        Route::post('send/invite', [MixxerController::class, 'sendInvite']);
        Route::post('accept/invite/request/{mixxer_id}', [MixxerController::class, 'acceptInviteRequest']);
        Route::post('search/list/{mixxer_id}', [MixxerController::class, 'searchUser']);
        Route::get('delete/media/{id}', [MixxerController::class, 'deleteMedia']);
        Route::get('remove/cover/{id}', [MixxerController::class, 'removeCover']);

        Route::post('friendly/check', [MixxerController::class, 'friendlyCheck']);
        Route::post('feedback', [MixxerController::class, 'feedback']);
        Route::post('friendly/feedback', [MixxerController::class, 'friendlyCheckFeedback']);

        Route::get('remove/participant/{mixxer_id}/{user_id}', [MixxerController::class, 'removeParticipant']);
        Route::get('reject/invite/request/{mixxer_id}', [MixxerController::class, 'rejectInviteRequest']);


    });
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('categories/{type}', [SettingController::class, 'categories']);
    Route::get('list/categories/{type}', [SettingController::class, 'listCategories']);
});

Route::prefix('user/subscription')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', [SubscriptionController::class, 'create']);
    });
});

Route::get('app/faqs', [SettingController::class, 'faqs']);
Route::post('app/contact', [SettingController::class, 'contact']);
Route::get('splash/{user_id?}', [SettingController::class, 'splash']);
