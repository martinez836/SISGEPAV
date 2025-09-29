<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HarvesterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::resource('harvester', HarvesterController::class);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//routes for users
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users-json', [UserController::class, 'getUsers']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}/deactivate', [UserController::class, 'deactivate']);
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');;

//routes for roles
Route::get('/roles', [App\Http\Controllers\RolController::class, 'getRoles']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});







require __DIR__.'/auth.php';
