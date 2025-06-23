<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectTeamController;
use App\Http\Controllers\ProjectFileController;
use App\Http\Controllers\UserSuggestionController;
use App\Http\Controllers\ProjectCustomerController;
use App\Http\Controllers\CustomerSuggestionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');

    // Tasks
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
    Route::put('/projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    Route::delete('/projects/{project}/tasks/{task}/users/{user}', [\App\Http\Controllers\TaskController::class, 'removeUser'])->name('projects.tasks.users.destroy');

    // Notes
    Route::post('/projects/{project}/notes', [NoteController::class, 'store'])->name('projects.notes.store');
    Route::put('/projects/{project}/notes/{note}', [NoteController::class, 'update'])->name('projects.notes.update');
    Route::delete('/projects/{project}/notes/{note}', [NoteController::class, 'destroy'])->name('projects.notes.destroy');
    Route::post('/projects/{project}/notes/{note}/comments', [\App\Http\Controllers\NoteController::class, 'commentsStore'])->name('projects.notes.comments.store');

    // Team
    Route::post('/projects/{project}/team', [ProjectTeamController::class, 'store'])->name('projects.team.store');
    Route::delete('/projects/{project}/team/{user}', [ProjectTeamController::class, 'destroy'])->name('projects.team.destroy');

    // Customers
    Route::post('/projects/{project}/customers', [\App\Http\Controllers\ProjectCustomerController::class, 'store'])->name('projects.customers.store');
    Route::delete('/projects/{project}/customers/{customer}', [\App\Http\Controllers\ProjectCustomerController::class, 'destroy'])->name('projects.customers.destroy');

    // Files
    Route::post('/projects/{project}/files', [ProjectFileController::class, 'store'])->name('projects.files.store');
    Route::delete('/projects/{project}/files/{file}', [ProjectFileController::class, 'destroy'])->name('projects.files.destroy');
    Route::get('/projects/{project}/files/{file}/download', [\App\Http\Controllers\ProjectFileController::class, 'download'])->name('projects.files.download');
    Route::get('/projects/{project}/files/{file}/view', [\App\Http\Controllers\ProjectFileController::class, 'view'])->name('projects.files.view');
});

Route::get('/uploaded-files', function () {
    return Inertia::render('UploadedFiles');
})->middleware(['auth', 'verified'])->name('uploaded-files');

Route::get('/activity', function () {
    return Inertia::render('Activity');
})->middleware(['auth', 'verified'])->name('activity');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API endpoint for user suggestions (autocomplete)
Route::get('/api/users', [UserSuggestionController::class, 'index']);
Route::get('/api/customers', [\App\Http\Controllers\CustomerSuggestionController::class, 'index']);

require __DIR__.'/auth.php';
