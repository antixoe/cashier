<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log the login activity
            ActivityLogService::logLogin();

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Redirecting...',
                    'user' => Auth::user()->name
                ]);
            }

            return redirect()->intended('/');
        }

        $errorMessage = 'The provided credentials are incorrect.';

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [$errorMessage],
        ]);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $customerRole = Role::firstOrCreate(['name' => 'customer'], ['description' => 'Customer']);
        $user->roles()->syncWithoutDetaching([$customerRole->id]);

        Auth::login($user);

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        // Log the logout activity before actually logging out
        ActivityLogService::logLogout();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
