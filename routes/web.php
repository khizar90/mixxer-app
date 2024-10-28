<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMixxerController;
use App\Http\Controllers\Admin\AdminTicketController;
    use App\Http\Controllers\MixxerController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('app/disclaimer', 'disclaimer');
Route::view('app/terms', 'terms');
Route::view('app/privacy', 'privacy');
Route::view('app/eula', 'eula');

Route::get('apple-app-site-association', function () {
    $data = new stdClass();
    $applinks = new stdClass();
    $applinks->apps = [];
    $details = [];
    $obj = new stdClass();
    $obj->appID = 'KYWP8ZD95G.com.mixxerco.app';
    $obj->paths = ['/*'];
    $details[] = $obj;
    $applinks->details = $details;
    $data->applinks = $applinks;
    return response()->json($data);
});

Route::get('shareable/{type}/{loginId}/{id}', [MixxerController::class, 'show']);

Route::prefix('dashboard')->middleware('auth')->name('dashboard-')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('users/', [AdminController::class, 'users'])->name('users');
    Route::get('users/show/{id}', [AdminController::class, 'profile'])->name('profile');
    Route::get('user/delete/{id}', [AdminController::class, 'deleteUser'])->name('user-delete');
    Route::get('users/export/', [AdminController::class, 'exportCSV'])->name('users-export-csv');

    Route::prefix('category')->name('category-')->group(function () {
        Route::get('/{type}', [AdminCategoryController::class, 'list']);
        Route::post('/add', [AdminCategoryController::class, 'add'])->name('add');
        Route::post('/edit/{id}', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::get('/delete/{id}', [AdminCategoryController::class, 'delete'])->name('delete');
    });

    Route::prefix('ticket')->name('ticket-')->group(function () {
        Route::get('/{status}', [AdminTicketController::class, 'ticket'])->name('ticket');
        Route::get('close-ticket/{id}', [AdminTicketController::class, 'closeTicket'])->name('close-ticket');
        Route::get('{status}/messages/{from_to}', [AdminTicketController::class, 'messages'])->name('messages');
        Route::post('send-message', [AdminTicketController::class, 'sendMessage'])->name('send-message');
    });
    Route::prefix('faqs')->name('faqs-')->group(function () {
        Route::get('/', [AdminController::class, 'faqs']);
        Route::post('add', [AdminController::class, 'addFaq'])->name('add');
        Route::post('edit/{id}', [AdminController::class, 'editFaq'])->name('edit');
        Route::get('delete-faq/{id}', [AdminController::class, 'deleteFaq'])->name('delete');
    });

    Route::prefix('mixxer')->name('mixxer-')->group(function () {
        Route::get('/', [AdminMixxerController::class, 'analytics']);
        Route::get('/{type}', [AdminMixxerController::class, 'list'])->name('list');
        Route::get('/delete/{mixxer_id}', [AdminMixxerController::class, 'delete'])->name('delete');
        Route::get('/{type}/detail/{mixer_id}', [AdminMixxerController::class, 'detail'])->name('detail');
        Route::get('{type}/feedbacks/{id}', [AdminMixxerController::class, 'feedbacks'])->name('feedbacks');
        Route::get('{type}/check-in/feedbacks/{id}', [AdminMixxerController::class, 'checkInfeedbacks'])->name('check-in-feedbacks');
        Route::get('{type}/feedback/detail/{id}', [AdminMixxerController::class, 'feedbackDetail'])->name('feedback-detail');
        Route::get('/feedback/delete/{id}', [AdminMixxerController::class, 'feedbackDelete'])->name('feedback-delete');

    });

    Route::prefix('feature')->name('feature-')->group(function () {
        Route::get('request', [AdminController::class, 'featureRequest'])->name('request');
        Route::get('delete/{id}', [AdminController::class, 'featureRequestDelete'])->name('delete');
        Route::get('detail/{id}', [AdminController::class, 'featureRequestDetail'])->name('detail');
    });

    Route::prefix('feedback')->name('feedback-')->group(function () {
        Route::get('/', [AdminController::class, 'appFeedback']);
        Route::get('/delete/{id}', [AdminController::class, 'appFeedbackDelete'])->name('delete');
        Route::get('detail/{id}', [AdminController::class, 'appFeedbackDetail'])->name('detail');
        Route::get('mixxer', [AdminController::class, 'mixxerFeedback'])->name('mixxer');
        Route::get('mixxer/detail/{id}', [AdminController::class, 'mixxerFeedbackDetail'])->name('mixxer-detail');
        Route::get('mixxer/check-in', [AdminController::class, 'mixxerCheckInFeedback'])->name('mixxer-check-in');
    });
    Route::prefix('report')->name('report-')->group(function () {
        Route::get('/user', [AdminController::class, 'reportedUser'])->name('user');
        Route::get('/user/Detail/{id}', [AdminController::class, 'reportedUserDetail'])->name('user-detail');
        Route::get('/user/delete/{id}', [AdminController::class, 'reportUserDelete'])->name('user-delete');

    });
    Route::prefix('version')->name('version-')->group(function () {
        Route::get('/{type}', [AdminController::class, 'version']);
        Route::post('save/{type}', [AdminController::class, 'versionSave'])->name('save');
    });
});

Route::get('send-notification', [AdminController::class, 'sendNotification']);

require __DIR__ . '/auth.php';
