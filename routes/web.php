<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HarvesterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\RolController;

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

//routes for farms
Route::get('/farms', [FarmController::class, 'index'])->name('farms.index');
Route::get('/farms-json', [FarmController::class, 'getFarms']);
Route::post('/farms', [FarmController::class, 'store']);
Route::put('/farms/{id}/deactivate', [FarmController::class, 'deactivate']);
Route::put('/farms/{id}', [FarmController::class, 'update'])->name('farms.update');;


//routes for roles
Route::get('/getRoles', [RolController::class, 'getRoles']);
Route::get('/roles',[RolController::class,'index'])->name('roles.index');
Route::post('/roles',[RolController::class,'store']);
Route::put('/roles/{id}',[RolController::class,'update'])->name('roles.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clasificación
    Route::get('classification',              [ClassificationController::class,'index'])->name('classification.index');
    Route::get('classification/create',       [ClassificationController::class,'create'])->name('classification.create');
    Route::post('classification',             [ClassificationController::class,'store'])->name('classification.store');
    Route::get('classification/{batch}',      [ClassificationController::class,'show'])->name('classification.show');
    Route::get('classification/{batch}/edit', [ClassificationController::class,'edit'])->name('classification.edit');
    Route::put('classification/{batch}',      [ClassificationController::class,'update'])->name('classification.update');
    Route::delete('classification/{batch}',   [ClassificationController::class,'destroy'])->name('classification.destroy');
});







require __DIR__.'/auth.php';
