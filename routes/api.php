<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::resource('products', ProductController::class);

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/search/{name}', [ProductController::class, 'search']);

// Route::post('/products', [ProductController::class, 'store']);
// Route::put('/products/{id}', [ProductController::class, 'update']);
// Route::delete('/products/{id}', [ProductController::class, 'destroy']);
// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Route to update wallet
    Route::put('/wallet/update', [AuthController::class, 'updateWallet']);

    // Route to get all users (admin only)
    Route::get('/allUsers', [AuthController::class, 'getAllUsers'])->middleware('isAdmin');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('isAdmin');
    Route::post('/products', [ProductController::class, 'store'])->middleware('isAdmin');
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('isAdmin');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
