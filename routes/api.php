<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::post('/login', [UserController::class, 'login']);

Route::middleware(['jwt-auth'])->group(function() {
    // Rute untuk produk
    Route::post('/product', [ProductController::class, 'apiStore']);
    Route::get('/product', [ProductController::class, 'showAll']);
    Route::get('/product/{id}', [ProductController::class, 'showByID']);
    Route::get('/product/name/{name}', [ProductController::class, 'showByName']);
    Route::put('/product/{id}', [ProductController::class, 'apiUpdate']);
    Route::delete('/product/{id}', [ProductController::class, 'apiDestroy']);
    
    // Rute untuk kategori
    Route::post('/category', [CategoryController::class, 'apiStore']);
    Route::get('/category', [CategoryController::class, 'showAll']);
    Route::get('/category/{id}', [CategoryController::class, 'showByID']);
    Route::get('/category/name/{name}', [CategoryController::class, 'showByName']);
    Route::put('/category/{id}', [CategoryController::class, 'apiUpdate']);
    Route::delete('/category/{id}', [CategoryController::class, 'apiDestroy']);

    // Rute untuk user
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user', [UserController::class, 'showAll']);
    Route::get('/user/{id}', [UserController::class, 'showByID']);
    Route::get('/user/name/{name}', [UserController::class, 'showByName']);
    Route::put('/user/{id}', [UserController::class, 'apiUpdate']);
    Route::delete('/user/{id}', [UserController::class, 'apiDestroy']);
    
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
