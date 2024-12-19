<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'gettable']);

    Route::get('/create-form', [FormController::class, 'create'])->name('create-form');
    Route::post('/create-form', [FormController::class, 'store']);
    
    Route::get('/edit-form/{id}', [FormController::class, 'edit'])->name('edit-form');
    Route::post('/edit-form', [FormController::class, 'update']);

    Route::post('/add-section', [SectionController::class, 'store'])->name('add-section');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
