<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HarvesterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassificationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::resource('harvester', HarvesterController::class);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
