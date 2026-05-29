<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CoordinatorDashboardController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\CoasDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientLogController;
use App\Http\Controllers\RotationAssignmentController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("landing");
});

Route::get("/dashboard", function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route("admin.dashboard");
    }
    if ($user->isCoordinator()) {
        return redirect()->route("coordinator.dashboard");
    }
    if ($user->isDoctor()) {
        return redirect()->route("dosen.dashboard");
    }
    return redirect()->route("koas.dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

// Admin routes
Route::middleware(["auth"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/dashboard", [
            AdminDashboardController::class,
            "index",
        ])->name("dashboard");
        Route::resource("assignments", RotationAssignmentController::class);
    });

// Coordinator routes
Route::middleware(["auth"])
    ->prefix("coordinator")
    ->name("coordinator.")
    ->group(function () {
        Route::get("/dashboard", [
            CoordinatorDashboardController::class,
            "index",
        ])->name("dashboard");
    });

// Doctor/Dosen routes
Route::middleware(["auth"])
    ->prefix("dosen")
    ->name("dosen.")
    ->group(function () {
        Route::get("/dashboard", [
            DosenDashboardController::class,
            "index",
        ])->name("dashboard");
    });

// COAS routes
Route::middleware(["auth"])->group(function () {
    Route::get("/koas/dashboard", [
        CoasDashboardController::class,
        "index",
    ])->name("koas.dashboard");
    Route::resource("patients", PatientController::class);
    Route::resource("logs", PatientLogController::class);
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

require __DIR__ . "/auth.php";
