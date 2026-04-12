<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ComplaintModerationController;
use App\Http\Controllers\Admin\PollController as AdminPollController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProfileController;

Route::get("/", function () {
    return redirect("/login");
});

// Auth Routes
Route::get("/login", [LoginController::class, "showLoginForm"])->name("login");
Route::post("/login", [LoginController::class, "login"]);
Route::post("/logout", [LoginController::class, "logout"])->name("logout");

// Profile Routes
Route::post("/profile/update", [ProfileController::class, "update"])->name("profile.update")->middleware('auth');

// User Routes
Route::middleware(["auth"])->prefix("user")->name("user.")->group(function () {
    Route::get("/dashboard", [ComplaintController::class, "dashboard"])->name("dashboard");
    Route::get("/polls", [ComplaintController::class, "polls"])->name("polls");
    Route::post("/polls/{poll}/vote", [VoteController::class, "vote"])->name("polls.vote");
    Route::resource("complaints", ComplaintController::class);
});

// Admin Routes
Route::middleware(["auth", \App\Http\Middleware\AdminMiddleware::class])->prefix("admin")->name("admin.")->group(function () {
    Route::get("/dashboard", [AdminDashboardController::class, "index"])->name("dashboard");
    
    // Complaint Moderation
    Route::get("/complaints", [ComplaintModerationController::class, "index"])->name("complaints");
    Route::get("/complaints/{complaint}", [ComplaintModerationController::class, "show"])->name("complaints.show");
    Route::post("/complaints/{complaint}/update", [ComplaintModerationController::class, "update"])->name("complaints.update");
    Route::post("/complaints/{complaint}/resolve", [ComplaintModerationController::class, "resolve"])->name("complaints.resolve");
    Route::post("/users/{user}/block", [ComplaintModerationController::class, "blockUser"])->name("users.block");

    // Admin Poll Routes
    Route::get("/polls", [AdminPollController::class, "index"])->name("polls.index");
    Route::get("/polls/create", [AdminPollController::class, "create"])->name("polls.create");
    Route::post("/polls", [AdminPollController::class, "store"])->name("polls.store");
    Route::post("/polls/{poll}/close", [AdminPollController::class, "close"])->name("polls.close");
    Route::delete("/polls/{poll}", [AdminPollController::class, "destroy"])->name("polls.destroy");
});
