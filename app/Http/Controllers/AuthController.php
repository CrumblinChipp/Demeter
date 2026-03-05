<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    // Show the page
    public function index()
    {
        return view('login');
    }

    // Handle Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('homepage');
        }

        // If it fails, redirect back to the #login tab
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput()->withFragment('login'); 
    }

    // Handle Registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sr_code' => 'required|string|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            // Redirect back to the #register tab so the user sees the errors
            return redirect(url()->previous() . '#register')
                        ->withErrors($validator)
                        ->withInput();
        }

    $user = User::create([
            'name' => $request->name,
            'sr_code' => $request->sr_code,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log the user in directly
        Auth::login($user);

        return redirect()->route('homepage');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}