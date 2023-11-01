<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\EmployeeVenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => [
                        'email' => 'The account is deactivated.'
                    ]
                ], 401);
            }

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

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'user' => $user,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'The provided credentials do not match our records.',
        ], 401);
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

        return response()->json([
            'status' => true,
            'message' => 'Successfully loggged out.',
        ], 201);
    }
}
