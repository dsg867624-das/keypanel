<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

    Route::get('/keys', [App\Http\Controllers\KeyController::class, 'index'])->name('keys.index');
    Route::get('/keys/create', [App\Http\Controllers\KeyController::class, 'create'])->name('keys.create');
    Route::post('/keys', [App\Http\Controllers\KeyController::class, 'store'])->name('keys.store');
    Route::post('/keys/{key}/ban', [App\Http\Controllers\KeyController::class, 'ban'])->name('keys.ban');
    Route::delete('/keys/{key}', [App\Http\Controllers\KeyController::class, 'destroy'])->name('keys.destroy');
});
