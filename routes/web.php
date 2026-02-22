<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role->name ?? '';
    if ($role == 'Administrator Fakultas')
        return redirect()->route('admin.dashboard');
    if ($role == 'Koordinator Program')
        return redirect()->route('coordinator.dashboard');
    if ($role == 'Dosen Pembimbing')
        return redirect()->route('dosen.dashboard');
    return redirect()->route('koas.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Administrator Fakultas'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('admin/assignments', \App\Http\Controllers\RotationAssignmentController::class)->names([
        'index' => 'admin.assignments.index',
        'create' => 'admin.assignments.create',
        'store' => 'admin.assignments.store',
        'show' => 'admin.assignments.show',
        'edit' => 'admin.assignments.edit',
        'update' => 'admin.assignments.update',
        'destroy' => 'admin.assignments.destroy',
    ]);
});

Route::middleware(['auth', 'role:Koordinator Program'])->group(function () {
    Route::get('/coordinator/dashboard', function () {
        return view('coordinator.dashboard');
    })->name('coordinator.dashboard');
});

Route::middleware(['auth', 'role:Dosen Pembimbing'])->group(function () {
    Route::get('/dosen/dashboard', function () {
        return view('dosen.dashboard');
    })->name('dosen.dashboard');
});

Route::middleware(['auth', 'role:Koas'])->group(function () {
    Route::get('/koas/dashboard', function () {
        return view('koas.dashboard');
    })->name('koas.dashboard');

    Route::resource('patients', \App\Http\Controllers\PatientController::class);
    Route::resource('logs', \App\Http\Controllers\PatientLogController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
