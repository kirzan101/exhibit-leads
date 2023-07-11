<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * render login page
     *
     * @return void
     */
    public function index()
    {
        if(Auth::user()) {
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
        Auth::logout();

        return redirect()->route('login');
    }
}
