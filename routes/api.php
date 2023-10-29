<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CadidateController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\KategoriKelasController;
use App\Http\Controllers\MateriTextController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('validate', [AuthController::class, 'validateToken']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'cadidate'], function () {
    Route::get('/', [CadidateController::class, 'index']);
    Route::get('/{id}', [CadidateController::class, 'show']);
    Route::post('/', [CadidateController::class, 'store']);
    Route::put('/{id}', [CadidateController::class, 'update']);
    Route::delete('/{id}', [CadidateController::class, 'destroy']);
});

Route::post('/test', function (Request $request) {
    return response()->json(['foo' => 'bar']);
});
