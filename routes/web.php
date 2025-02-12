<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\UserController;
use App\Models\Form;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sso/callback', [AuthController::class, 'ssoCallback'])->name('ssoCallback');
Route::get('/sso/login', [AuthController::class, 'ssoLogin'])->name('ssoLogin');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'gettable']);
    
    Route::get('/forms', [FormController::class, 'forms'])->name('forms');
    Route::post('/forms', [FormController::class, 'gettable']);

    Route::post('/form-submit', [FormController::class, 'submit'])->name('form-submit');
    Route::post('/upload-file', [FileController::class, 'upload'])->name('upload-file');
    
    Route::middleware(['role:admin|opd'])->group(function () {
        Route::get('/create-form', [FormController::class, 'create'])->name('create-form');
        Route::post('/create-form', [FormController::class, 'store']);
        
        Route::get('/edit-form/{id}', [FormController::class, 'edit'])->name('edit-form');
        Route::patch('/edit-form', [FormController::class, 'update']);
        Route::get('/edit-form/{id}/{section_id}', [FormController::class, 'editWithSection'])->name('edit-form-section');

        Route::delete('/delete-question', [QuestionController::class, 'delete'])->name('delete-question');

        Route::post('/add-section', [SectionController::class, 'store'])->name('add-section');
        Route::patch('/edit-section', [SectionController::class, 'update'])->name('edit-section');
        Route::delete('/delete-section', [SectionController::class, 'destroy'])->name('delete-section');

        Route::post('/add-question', [QuestionController::class, 'store'])->name('add-question');
        Route::post('/update-question', [QuestionController::class, 'update'])->name('update-question');

        Route::post('/stat-table', [StatController::class, 'table'])->name('stat-table');
        Route::get('/download/{id}', [StatController::class, 'xlsx'])->name('download');
        Route::get('/data/{form_id}', [StatController::class, 'show'])->name('stat-form');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [UserController::class, 'table'])->name('admin.users.table');
        Route::patch('/edit-role', [UserController::class, 'updateRole'])->name('edit-role');
        Route::post('/form-approval', [FormController::class, 'formApproval'])->name('form-approval');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/{slug}', [FormController::class, 'show'])->name('show-form');
Route::get('/{slug}/{section_id}', [FormController::class, 'showWithSection'])->name('show-form-with-section');