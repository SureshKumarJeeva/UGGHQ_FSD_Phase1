<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureTokenIsRedirected;

//API Routes 
// Register
Route::post('/register', [UserController::class, 'register']);
// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);
// Authorization
Route::post('/auth/user', [AuthController::class, 'user'])->middleware([EnsureTokenIsRedirected::class, 'auth:api']);