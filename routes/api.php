<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



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

Route::post('auth/login', [ AuthController::class, 'login']);
Route::post('auth/register', [ AuthController::class, 'register']);
Route::get('auth/refreshToken', [ AuthController::class, 'refreshToken']);

Route::get('products', [ ProductController::class, 'index']);
Route::get('products/{id}', [ ProductController::class, 'show']);
Route::get('seed', [ ProductController::class, 'seed']);
