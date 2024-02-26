<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LeadController;
use App\Http\Controllers\API\OpcLeadController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// auth
Route::post('login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/leads', [LeadController::class, 'index']);
    Route::resource('opc-leads', OpcLeadController::class, ['except' => ['show', 'edit', 'create', 'index']]);

    //encrypt password
    Route::post('/encrypt', [OpcLeadController::class, 'encryptMobilePassword']);
    Route::post('/decrypt', [OpcLeadController::class, 'decryptMobilePassword']);
    Route::post('opc-lead-bulk', [OpcLeadController::class, 'storeBulk']);
});

Route::post('/mobile/login', [OpcLeadController::class, 'loginMobile']);
