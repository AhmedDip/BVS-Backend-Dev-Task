<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ArticleController;

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



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['auth:sanctum','throttle:20,1'])->group(function () {
    Route::post('logout', [AuthController::class,'logout']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole']);
    Route::get('profile', [UserController::class,'profile']);

    Route::get('articles', [ArticleController::class,'index']);
    Route::get('articles/mine', [ArticleController::class,'mine']);
    Route::post('articles', [ArticleController::class,'store']);
    Route::put('articles/{id}', [ArticleController::class,'update']);
    Route::delete('articles/{id}', [ArticleController::class,'destroy']);
    Route::patch('articles/{id}/publish', [ArticleController::class,'publish']);
});


