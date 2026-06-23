<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueMemberController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->name('profile.')->group(function () {
        Route::get('/profile', 'edit')->name('edit');
        Route::patch('/profile', 'update')->name('update');
        Route::delete('/profile', 'destroy')->name('destroy');
    });

    Route::resource('projects', ProjectController::class);

    Route::get('issues/search', [IssueController::class, 'search'])->name('issues.search');
    Route::resource('issues', IssueController::class);

    Route::controller(TagController::class)->name('tags.')->group(function () {
        Route::get('tags', 'index')->name('index');
        Route::post('tags', 'store')->name('store');
    });

    Route::prefix('issues/{issue}')->name('issues.')->group(function (): void {
        Route::prefix('tags')->name('tags.')->controller(IssueTagController::class)->group(function () {
            Route::post('/', 'store')->name('store');
            Route::delete('{tag}', 'destroy')->name('destroy');
        });

        Route::prefix('comments')->controller(CommentController::class)->name('comments.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
        });

        Route::prefix('members')->controller(IssueMemberController::class)->name('members.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::delete('/{user}', 'destroy')->name('destroy');
        });
    });
});

require __DIR__.'/auth.php';
