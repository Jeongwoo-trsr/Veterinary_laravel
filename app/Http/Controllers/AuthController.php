<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PetOwner;
use App\Models\Doctor;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'doctor':
                    return redirect()->route('doctor.dashboard');
                case 'pet_owner':
                    return redirect()->route('pet-owner.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:pet_owner,doctor',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => $request->role,
        ]);

        // Create role-specific profile
        if ($request->role === 'pet_owner') {
            PetOwner::create([
                'user_id' => $user->id,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'notes' => $request->notes,
            ]);
        } elseif ($request->role === 'doctor') {
            $request->validate([
                'specialization' => 'required|string|max:255',
                'license_number' => 'required|string|max:50|unique:doctors',
                'experience_years' => 'required|integer|min:0',
                'bio' => 'nullable|string|max:1000',
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'license_number' => $request->license_number,
                'experience_years' => $request->experience_years,
                'bio' => $request->bio,
            ]);
        }

        Auth::login($user);

        return redirect()->route($user->role . '.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
