<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi Pembuat Jadwal Guru & Pelajaran
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Schedules Management & Generation
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules/generate', [ScheduleController::class, 'generate'])->name('schedules.generate');
    Route::post('/schedules/clear', [ScheduleController::class, 'clear'])->name('schedules.clear');
    Route::post('/schedules/store', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Schedule Printing & Exports
    Route::get('/schedules/print/class/{id}', [ScheduleController::class, 'printClass'])->name('schedules.print.class');
    Route::get('/schedules/print/teacher/{id}', [ScheduleController::class, 'printTeacher'])->name('schedules.print.teacher');
    Route::get('/schedules/print/all', [ScheduleController::class, 'printAll'])->name('schedules.print.all');

    // Master Data
    Route::resource('teachers', TeacherController::class)->except(['create', 'show', 'edit']);
    Route::resource('subjects', SubjectController::class)->except(['create', 'show', 'edit']);
    Route::resource('classrooms', ClassroomController::class)->except(['create', 'show', 'edit']);
    Route::resource('assignments', AssignmentController::class)->except(['create', 'show', 'edit', 'update']);

    // School Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
