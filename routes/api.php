<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'apiLogin']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'apiUser']);