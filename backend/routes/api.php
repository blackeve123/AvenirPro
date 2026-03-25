<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // ─── Public routes ────────────────────────────────────────────────────────────

    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);

        Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
        Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    });

    // Public browse
    Route::get('/jobs',            [JobController::class, 'index']);
    Route::get('/jobs/{id}',       [JobController::class, 'show']);
    Route::get('/jobs/{id}/steps', [JobController::class, 'steps']);
    Route::get('/categories',      [JobController::class, 'categories']);

    Route::get('/questions', [QuestionController::class, 'index']);

    // ─── Authenticated routes (Sanctum) ───────────────────────────────────────────

    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
        Route::put('/me',      [AuthController::class, 'updateProfile']);

        // Test
        Route::middleware('throttle:60,1')->group(function () {
            Route::post('/test/answer',    [TestController::class, 'storeAnswer']);
            Route::post('/test/calculate', [TestController::class, 'calculate']);
            Route::delete('/test/reset',   [TestController::class, 'resetTest']);
            Route::get('/test/status',     [TestController::class, 'status']);
        });


        Route::get('/my-results', [TestController::class, 'myResults']);

        // ─── Admin-only routes ────────────────────────────────────────────────────
        Route::middleware(AdminMiddleware::class)->prefix('admin')->group(function () {

            // Jobs
            Route::post('/jobs',        [AdminController::class, 'createJob']);
            Route::put('/jobs/{id}',    [AdminController::class, 'updateJob']);
            Route::delete('/jobs/{id}', [AdminController::class, 'deleteJob']);

            // Steps
            Route::post('/jobs/{jobId}/steps', [AdminController::class, 'createStep']);
            Route::delete('/steps/{id}',       [AdminController::class, 'deleteStep']);

            // Questions
            Route::post('/questions',        [AdminController::class, 'createQuestion']);
            Route::put('/questions/{id}',     [AdminController::class, 'updateQuestion']);
            Route::delete('/questions/{id}', [AdminController::class, 'deleteQuestion']);
        });
    });
});

