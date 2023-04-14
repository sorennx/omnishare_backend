<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\UserAboutController;
use App\Http\Controllers\Post\PostChannelController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Feed\FeedChannelController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user-about', [UserAboutController::class, 'index']);
Route::get('/user-about/{id}', [UserAboutController::class, 'show']);
Route::get('/user-about/search/{name}', [UserAboutController::class, 'search']);



// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/user-about', [UserAboutController::class, 'store']);
    Route::put('/user-about/{id}', [UserAboutController::class, 'update']);
    Route::delete('/user-about/{id}', [UserAboutController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
   

    // Post section
    Route::post('/post/post-channel', [PostChannelController::class, 'store']);
    Route::get('/post/post-channel', [PostChannelController::class, 'index']);
    Route::get('/post/post-channel/{id}', [PostChannelController::class, 'show']);

    Route::post('/post', [PostController::class, 'store']);
    Route::get('/post', [PostController::class, 'index']);
    Route::put('/post/{id}', [PostController::class, 'update']);
    Route::get('/post/{id}', [PostController::class, 'show']);
    Route::delete('/post/{id}', [PostController::class, 'destroy']);

    // Feed section
    Route::get('/feed', [FeedChannelController::class, 'index']);
    Route::get('/feed/channel/{id}', [FeedChannelController::class, 'show']);
    Route::post('/feed/channel', [FeedChannelController::class, 'store']);
    Route::get('/explore', [FeedChannelController::class, 'getUnsubscribedChannels']);
    
    
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});