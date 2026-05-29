<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ContainerController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// LOGIN
Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    // PROTECTED ROUTES
    Route::middleware('auth:api')->group(function () {

        // PROFILE
        Route::get('/profile', [AuthController::class, 'profile']);

        // LOGOUT
        Route::post('/logout', [AuthController::class, 'logout']);

        // API GATEWAY
        Route::prefix('gateway')->group(function () {

            // GET ALL CONTAINERS
            Route::get('/containers', [ContainerController::class, 'index']);

            // SEARCH CONTAINER
            Route::get('/containers/search', [ContainerController::class, 'search']);

            // GET TRACKING LOGS
            Route::get('/containers/{id}/logs', [ContainerController::class, 'logs']);

            // CREATE CONTAINER
            Route::post('/containers', [ContainerController::class, 'store']);

            // ARCHIVE CONTAINER
            Route::patch('/containers/{id}/archive', [ContainerController::class, 'archive']);

            // DELETE CONTAINER
            Route::delete('/containers/{id}', [ContainerController::class, 'destroy']);
        });
    });
});