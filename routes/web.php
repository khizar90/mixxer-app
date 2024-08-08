<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminMixxerController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::view('app/disclaimer', 'disclaimer');
Route::view('app/terms', 'terms');
Route::view('app/privacy', 'privacy');
Route::view('app/eula', 'eula');

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
        Route::get('/detail/{mixer_id}', [AdminMixxerController::class, 'detail'])->name('detail');
    });

    Route::get('feature/request', [AdminController::class, 'featureRequest'])->name('feature-request');
});
require __DIR__ . '/auth.php';
