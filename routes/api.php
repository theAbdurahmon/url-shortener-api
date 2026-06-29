<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PreviewController;

Route::post("/register", [AuthController::class, "create"]);
Route::post("/login", [AuthController::class, "login"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::apiResource("/links", LinkController::class);
    Route::get("/links/{slug}/stats", [AnalyticsController::class, "stats"]);
    Route::get("/links/{slug}/stats/timeline", [AnalyticsController::class, "timeline"]);
});

Route::get("/preview/{slug}", PreviewController::class);

Route::middleware("throttle:8,1")->group(function () {
    Route::get("/{slug}", RedirectController::class);
    Route::get("/{slug}/unlock", [RedirectController::class, "unlock"]);
});