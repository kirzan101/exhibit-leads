<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ActivityLogService;
use App\Services\EmployeeVenueService;

class AuthController extends Controller
{
    private ActivityLogService $activityLogService;
    private EmployeeVenueService $employeeVenueService;

    public function __construct(ActivityLogService $activityLogService, EmployeeVenueService $employeeVenueService)
    {
        $this->activityLogService = $activityLogService;
        $this->employeeVenueService = $employeeVenueService;
    }

    /**
     * render login page
     *
     * @return void
     */
    public function index()
    {
        if (Auth::user()) {
            return redirect('/');
        }

        return Inertia::render('Login', []);
    }

    /**
     * login the user
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            // if account is deactivated
            if(!Auth::user()->is_active) {

                Auth::logout();

                return back()->withErrors([
                    'email' => 'The account is deactivated.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            //encrypt passwords
            $request->merge(['password' => bcrypt($request->password)]);

            // log access
            $log = [
                'name' => 'auth',
                'description' => 'Successfully logged in!',
                'event' => 'login',
                'status' => 'success',
                'properties' => json_encode($request->toArray()),
                'subject_id' => Auth::user()->id
            ];

            $this->activityLogService->createActivityLog($log);

            //delete venue records for confirmers
            // exclude admin account
            if (Helper::checkAccess('confirms', 'read') && Auth::user()->employee->userGroup->id != 1) {
                $this->employeeVenueService->deleteEmployeeVenueByEmployeeId(Auth::user()->employee->id);
            }

            return redirect()->intended();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * logout user
     *
     * @return void
     */
    public function logout()
    {
        $log = [
            'name' => 'auth',
            'description' => 'Successfully logged out!',
            'event' => 'logout',
            'status' => 'success',
            'properties' => '{"user_id":' . Auth::user()->id . '}',
            'subject_id' => Auth::user()->id
        ];

        $this->activityLogService->createActivityLog($log);

        //delete venue records for confirmers
        // exclude admin account
        if (Helper::checkAccess('confirms', 'read') && Auth::user()->employee->userGroup->id != 1) {
            $this->employeeVenueService->deleteEmployeeVenueByEmployeeId(Auth::user()->employee->id);
        }

        Auth::logout();

        return redirect()->route('login');
    }
}
