<?php

use App\Http\Controllers\AssignedEmployeeController;
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


Route::get('/welcome', function () {
    return inertia('Welcome');
});

Route::get('/', function () {
    return inertia('Welcome');
});

// Route::get('/members', [MemberController::class, 'index']);
Route::resource('members', MemberController::class);
Route::resource('contracts', ContractController::class);
Route::resource('employees', EmployeeController::class);
Route::resource('assigned-employees', AssignedEmployeeController::class, ['except' => ['store', 'delete', 'reassigneEmployee', 'show']]);

Route::get('assigned-employees/{id}', [AssignedEmployeeController::class, 'show']);
Route::post('assign-employee', [AssignedEmployeeController::class, 'store']);
Route::post('reassign-employee', [AssignedEmployeeController::class, 'reassignEmployee']);