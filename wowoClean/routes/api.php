<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContainerController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/containers', [ContainerController::class, 'index']);
Route::post('/containers', [ContainerController::class, 'store']);
Route::get('/containers/search', [ContainerController::class, 'search']);
Route::get('/containers/{id}/logs', [ContainerController::class, 'logs']);
Route::patch('/containers/{id}/archive', [ContainerController::class, 'archive']);
Route::delete('/containers/{id}', [ContainerController::class, 'destroy']);

