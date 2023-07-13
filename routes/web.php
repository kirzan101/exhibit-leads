<?php

use App\Http\Controllers\AssignedEmployeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// auth
Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');


// Route::get('/welcome', function () {
//     return inertia('Welcome');
// });

Route::middleware('auth')->group(function () {
    Route::resource('members', MemberController::class);
    Route::resource('contracts', ContractController::class);
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/reset-password/{id}', [EmployeeController::class, 'resetPassword']);
    Route::resource('assigned-employees', AssignedEmployeeController::class, ['except' => ['store', 'delete', 'reassigneEmployee', 'show']]);

    Route::get('assigned-employees/{id}', [AssignedEmployeeController::class, 'show']);
    Route::post('assign-employee', [AssignedEmployeeController::class, 'store']);
    Route::post('reassign-employee', [AssignedEmployeeController::class, 'reassignEmployee']);
    Route::post('remarks', [MemberController::class, 'remarks']);
    Route::post('remove-assign', [AssignedEmployeeController::class, 'removeAssignment']);

    Route::get('/', function () {
        return inertia('Welcome');
    })->name('home');

    // update profile
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    Route::put('/profile/edit', [EmployeeController::class, 'profileEdit']);

    // invites
    Route::get('invites', [MemberController::class, 'indexInvite'])->name('invites');
    Route::post('invites', [MemberController::class, 'invite']);
    Route::post('invites/cancel', [MemberController::class, 'inviteCancel']);

    Route::get('paginate', [MemberController::class, 'indexPaginate']);
});