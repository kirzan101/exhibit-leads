<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ActivityLogService;

class AuthController extends Controller
{
    private ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
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
            $request->session()->regenerate();

            // log access
            $log = [
                'name' => 'auth',
                'description' => 'Successfully logged in!',
                'event' => 'login',
                'status' => 'success',
                'properties' => json_encode($request),
                'subject_id' => Auth::user()->id
            ];

            $this->activityLogService->createActivityLog($log);

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
        
        Auth::logout();

        return redirect()->route('login');
    }
}
