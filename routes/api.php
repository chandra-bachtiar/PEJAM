<?php

use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\CadidateController;
use App\Http\Controllers\MateriTextController;
use App\Http\Controllers\KategoriKelasController;

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
    Route::post('/import', [UserController::class, 'import']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'cadidate'], function () {
    Route::get('/', [CadidateController::class, 'index']);
    Route::get('/{id}', [CadidateController::class, 'show']);
    Route::post('/', [CadidateController::class, 'store']);
    Route::put('/{id}', [CadidateController::class, 'update']);
    Route::delete('/{id}', [CadidateController::class, 'destroy']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'vote'], function () {
    Route::get('/', [VoteController::class, 'index']);
    Route::get('/cadidate', [VoteController::class, 'indexVote']);
    Route::get('/check', [VoteController::class, 'checkVoteUser']);
    Route::get('/{id}', [VoteController::class, 'show']);
    Route::post('/', [VoteController::class, 'store']);
    Route::put('/{id}', [VoteController::class, 'update']);
    Route::delete('/{id}', [VoteController::class, 'destroy']);
    Route::get('/export', [VoteController::class, 'export']);
});

Route::get('/publicvote', [VoteController::class, 'publicVote']);

Route::post('/test', function (Request $request) {
    return response()->json(['foo' => 'bar']);
});
