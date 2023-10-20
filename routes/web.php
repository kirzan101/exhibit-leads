<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AssignedConfirmerController;
use App\Http\Controllers\AssignedEmployeeController;
use App\Http\Controllers\AssignedExhibitorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExhibitController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\PaginateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoiController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\VenueController;
use App\Models\AssignedExhibitor;
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
    Route::post('remarks', [AssignedEmployeeController::class, 'remarks']);

    // assign employee
    // Route::resource('assigned-employees', AssignedEmployeeController::class, ['except' => ['store', 'destroy', 'reassignEmployee', 'show', 'edit', 'update', 'create']]);
    Route::get('assigned-employees', [AssignedEmployeeController::class, 'index'])->name('assigned-employees');
    Route::get('assigned-employees/{lead}', [AssignedEmployeeController::class, 'showLead'])->name('assigned-employees-show');
    Route::post('assigned-employees', [AssignedEmployeeController::class, 'store']);
    Route::get('assigned-employees/edit/{lead}', [AssignedEmployeeController::class, 'editLead']);
    Route::put('assigned-employees/{lead}', [AssignedEmployeeController::class, 'updateLead']);
    Route::post('reassign-employee', [AssignedEmployeeController::class, 'reassignEmployee']);
    Route::post('remove-assign', [AssignedEmployeeController::class, 'removeAssignment']);

    //assign confirmer
    // Route::resource('assigned-confirmers', AssignedConfirmerController::class, ['except' => ['store', 'destroy', 'reassignConfirmer', 'show', 'update', 'create']]);
    Route::get('assigned-confirmers', [AssignedConfirmerController::class, 'index'])->name('assigned-confirmers');
    Route::get('assigned-confirmers/{lead}', [AssignedConfirmerController::class, 'showLead'])->name('assigned-confirmers-show');
    Route::post('assigned-confirmers', [AssignedConfirmerController::class, 'store']);
    Route::get('assigned-confirmers/edit/{lead}', [AssignedConfirmerController::class, 'editLead']);
    Route::put('assigned-confirmers/{lead}', [AssignedConfirmerController::class, 'updateLead']);
    Route::post('reassign-confirmer', [AssignedConfirmerController::class, 'reassignConfirmer']);
    Route::post('remove-assign-confirmer', [AssignedConfirmerController::class, 'removeAssignment']);

    //assign exhibitor
    // Route::resource('assigned-exhibitors', AssignedExhibitorController::class, ['except' => ['store', 'destroy', 'reassignConfirmer', 'show', 'edit', 'update', 'create']]);
    Route::get('assigned-exhibitors', [AssignedExhibitorController::class, 'index'])->name('assigned-confirmers');
    Route::get('assigned-exhibitors/{lead}', [AssignedExhibitorController::class, 'showLead'])->name('assigned-exhibitors-show');
    Route::post('assign-exhibitor', [AssignedExhibitorController::class, 'store']);
    Route::get('assigned-exhibitors/edit/{lead}', [AssignedExhibitorController::class, 'editLead']);
    Route::put('assigned-exhibitors/{lead}', [AssignedExhibitorController::class, 'updateLead']);
    Route::post('reassign-exhibitor', [AssignedExhibitorController::class, 'reassignExhibitor']);
    Route::post('remove-assign-exhibitor', [AssignedExhibitorController::class, 'removeAssignment']);

    Route::get('/', function () {
        return inertia('Welcome');
    })->name('home');

    // update profile
    Route::get('/profile', [EmployeeController::class, 'profile'])->name('profile');
    Route::put('/profile/edit', [EmployeeController::class, 'profileEdit']);

    // confirm
    Route::post('confirm', [AssignedConfirmerController::class, 'confirm']);
    Route::get('confirms', [LeadController::class, 'indexDoneLead'])->name('confirms');

    //confirmed
    // Route::get('/confirmed', [LeadController::class, 'indexConfirmed'])->name('confirmed');
    Route::get('/confirmed', [LeadController::class, 'indexPaginateConfirmed'])->name('confirmed');
    // Route::get('/confirmed/remove', [LeadController::class, 'removeConfirmed']);
    Route::post('/confirmed/remove', [AssignedConfirmerController::class, 'removeConfirmedLeads']);

    // assign done status
    Route::post('done', [LeadController::class, 'done']);
    Route::post('done/cancel', [LeadController::class, 'cancelDone']);

    // assign done status by confirmer
    Route::post('confirmer/done', [LeadController::class, 'doneConfirmer']);
    Route::post('confirmer/done/cancel', [LeadController::class, 'cancelDoneConfirmer']);

    //mark as no show
    Route::post('/showed', [LeadController::class, 'showedLead']);

    //venues
    Route::resource('/venues', VenueController::class);

    //sources
    Route::resource('/sources', SourceController::class);

    //user groups
    Route::get('/usergroups', [UserGroupController::class, 'index']);

    //lead paginate
    Route::get('/paginate/leads', [PaginateController::class, 'leadPaginate']);
    Route::get('/paginate/leads/request', [PaginateController::class, 'leadPaginateIndex']);

    //ROI
    Route::get('/rois', [RoiController::class, 'index'])->name('rois');
    Route::get('/rois/{lead}', [RoiController::class, 'showLead'])->name('rois-lead-show');
    Route::get('/rois/{lead}/edit', [RoiController::class, 'editLead']);
    Route::put('/rois/{lead}', [RoiController::class, 'updateLead']);

    //Survey
    Route::get('/surveys', [SurveyController::class, 'index'])->name('surveys');
    Route::get('/surveys/{lead}', [SurveyController::class, 'showLead'])->name('survey-lead-show');
    Route::get('/surveys/{lead}/edit', [SurveyController::class, 'editLead']);
    Route::put('/surveys/{lead}', [SurveyController::class, 'updateLead']);

    //Exhibit
    Route::get('/exhibits', [ExhibitController::class, 'index'])->name('exhibits');
    Route::get('/exhibits/{lead}', [ExhibitController::class, 'showLead'])->name('exhibit-lead-show');
    Route::get('/exhibits/{lead}/edit', [ExhibitController::class, 'editLead']);
    Route::put('/exhibits/{lead}', [ExhibitController::class, 'updateLead']);
    
    //Lead Status
    Route::get('/lead-status', [LeadStatusController::class, 'index'])->name('lead-status');

    //reports
    Route::get('/reports/confirmed', [ReportController::class, 'confirmedReport']);

    // activity logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index']);
});
