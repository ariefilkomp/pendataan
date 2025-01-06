<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StatController;
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
    Route::patch('/edit-form', [FormController::class, 'update']);
    Route::get('/edit-form/{id}/{section_id}', [FormController::class, 'editWithSection'])->name('edit-form-section');

    Route::post('/add-section', [SectionController::class, 'store'])->name('add-section');
    Route::delete('/delete-section', [SectionController::class, 'destroy'])->name('delete-section');

    Route::post('/add-question', [QuestionController::class, 'store'])->name('add-question');
    Route::post('/update-question', [QuestionController::class, 'update'])->name('update-question');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::post('/stat-table', [StatController::class, 'table'])->name('stat-table');

Route::get('/statistik/{form_id}', [StatController::class, 'show'])->name('stat-form');
Route::get('/{slug}', [FormController::class, 'show'])->name('show-form');
Route::get('/{slug}/{section_id}', [FormController::class, 'showWithSection'])->name('show-form-with-section');
Route::post('/form-submit', [FormController::class, 'submit'])->name('form-submit');
Route::post('/upload-file', [FileController::class, 'upload'])->name('upload-file');