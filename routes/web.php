<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Route::resource('user_activities', UserActivityController::class)->middleware(['auth', 'verified']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/leaderboard', [UserActivityController::class, 'index'])->name('leaderboard.index');
Route::get('/activies_data', [UserActivityController::class, 'activies_data'])->name('leaderboard.activies_data');
Route::get('/leaderboard/recalculate', [UserActivityController::class, 'recalculate'])->name('leaderboard.recalculate');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
