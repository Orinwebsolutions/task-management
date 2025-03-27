<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterUserController;
use App\Http\Controllers\LoginUserController;
use App\Http\Controllers\TaskController;


Route::middleware('auth:sanctum')->group(function(){
    Route::post("/tasks", [TaskController::class, 'store']);
    Route::get("/tasks", [TaskController::class, 'index']);
    Route::get("/tasks/{id}", [TaskController::class, 'show']);
    Route::put("/tasks/{id}", [TaskController::class, 'update']);
    Route::delete("/tasks/{id}", [TaskController::class, 'destroy']);
});

Route::post('/login', [LoginUserController::class, 'store']);
Route::post('/register', [RegisterUserController::class, 'store']);
