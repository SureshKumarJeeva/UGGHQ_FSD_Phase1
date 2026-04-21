<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\EnsureTokenIsRedirected;

//API Routes 
// Register
Route::post('/register', [UserController::class, 'register']);
// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);
// Authorization
Route::post('/auth/user', [AuthController::class, 'user'])->middleware([EnsureTokenIsRedirected::class, 'auth:api']);
// Fetch all posts
Route::get('/posts', [PostController::class, 'posts'])->middleware([EnsureTokenIsRedirected::class, 'auth:api']);
// Create a post
Route::post('/post', [PostController::class, 'create'])->middleware([EnsureTokenIsRedirected::class, 'auth:api']);
// Fetch a post
// Route::get('/post/{$id}', [PostController::class, 'fetch'])->middleware([EnsureTokenIsRedirected::class, 'auth:api']);
//Logout user session
Route::post('/logout', [AuthController::class, 'logout'])->middleware([EnsureTokenIsRedirected::class, 'auth:api']);