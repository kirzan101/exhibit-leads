<?php

use App\Http\Controllers\AssignedConfirmerController;
use App\Http\Controllers\AssignedEmployeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\VenueController;
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
    Route::resource('leads', LeadController::class);
    Route::resource('contracts', ContractController::class);
    Route::resource('employees', EmployeeController::class);
    Route::post('employees/reset-password/{id}', [EmployeeController::class, 'resetPassword']);
    Route::post('remarks', [LeadController::class, 'remarks']);
    Route::post('confirm', [LeadController::class, 'confirm']);

    // assign employee
    Route::resource('assigned-employees', AssignedEmployeeController::class, ['except' => ['store', 'delete', 'reassignEmployee', 'show']]);
    Route::get('assigned-employees/{id}', [AssignedEmployeeController::class, 'show']);
    Route::post('assign-employee', [AssignedEmployeeController::class, 'store']);
    Route::post('reassign-employee', [AssignedEmployeeController::class, 'reassignEmployee']);
    Route::post('remove-assign', [AssignedEmployeeController::class, 'removeAssignment']);

    //assign confirmer
    Route::resource('assigned-confirmers', AssignedConfirmerController::class, ['except' => ['store', 'delete', 'reassignConfirmer', 'show']]);
    Route::get('assigned-confirmers/{id}', [AssignedConfirmerController::class, 'show']);
    Route::post('assign-confirmer', [AssignedConfirmerController::class, 'store']);
    Route::post('reassign-confirmer', [AssignedConfirmerController::class, 'reassignConfirmer']);
    Route::post('remove-assign-confirmer', [AssignedConfirmerController::class, 'removeAssignment']);

    Route::get('/', function () {
        return inertia('Welcome');
    })->name('home');

    // update profile
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    Route::put('/profile/edit', [EmployeeController::class, 'profileEdit']);

    // invites
    Route::get('invites', [LeadController::class, 'indexInvite'])->name('invites');
    Route::post('invites', [LeadController::class, 'invite']);
    Route::post('invites/cancel', [LeadController::class, 'inviteCancel']);

    //confirm
    Route::get('/confirmed', [LeadController::class, 'indexConfirmed'])->name('confirmed');
    Route::get('/confirmed/remove', [LeadController::class, 'removeConfirmed']);

    //mark as no show
    Route::post('/showed', [LeadController::class, 'showedLead']);

    Route::get('paginate', [LeadController::class, 'indexPaginate']);

    //venues
    Route::resource('/venues', VenueController::class);

    Route::get('/usergroups', [UserGroupController::class, 'index']);
});