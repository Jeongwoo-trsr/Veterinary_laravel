<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load(['petOwner', 'doctor']);
        
        return response()->json([
            'user' => $user,
            'role_data' => $this->getRoleSpecificData($user)
        ]);
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ];
        
        // Add role-specific validation
        if ($user->isPetOwner()) {
            $rules['emergency_contact'] = 'nullable|string|max:255';
            $rules['emergency_phone'] = 'nullable|string|max:20';
        } elseif ($user->isDoctor()) {
            $rules['specialization'] = 'required|string|max:255';
            $rules['license_number'] = 'required|string|max:50|unique:doctors,license_number,' . $user->doctor->id;
            $rules['experience_years'] = 'required|integer|min:0';
            $rules['bio'] = 'nullable|string|max:1000';
        }
        
        $validated = $request->validate($rules);
        
        // Verify current password if changing password
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 422);
            }
            $validated['password'] = Hash::make($request->new_password);
        }
        
        // Update user data
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'address' => $validated['address'] ?? $user->address,
        ]);
        
        if (isset($validated['password'])) {
            $user->update(['password' => $validated['password']]);
        }
        
        // Update role-specific data
        if ($user->isPetOwner() && $user->petOwner) {
            $user->petOwner->update([
                'emergency_contact' => $validated['emergency_contact'] ?? $user->petOwner->emergency_contact,
                'emergency_phone' => $validated['emergency_phone'] ?? $user->petOwner->emergency_phone,
            ]);
        } elseif ($user->isDoctor() && $user->doctor) {
            $user->doctor->update([
                'specialization' => $validated['specialization'],
                'license_number' => $validated['license_number'],
                'experience_years' => $validated['experience_years'],
                'bio' => $validated['bio'] ?? $user->doctor->bio,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user->fresh(['petOwner', 'doctor'])
        ]);
    }
    
    private function getRoleSpecificData($user)
    {
        if ($user->isPetOwner() && $user->petOwner) {
            return [
                'total_pets' => $user->petOwner->pets()->count(),
                'total_appointments' => \App\Models\Appointment::whereHas('pet', function($q) use ($user) {
                    $q->where('owner_id', $user->petOwner->id);
                })->count(),
                'emergency_contact' => $user->petOwner->emergency_contact,
                'emergency_phone' => $user->petOwner->emergency_phone,
            ];
        } elseif ($user->isDoctor() && $user->doctor) {
            return [
                'specialization' => $user->doctor->specialization,
                'license_number' => $user->doctor->license_number,
                'experience_years' => $user->doctor->experience_years,
                'bio' => $user->doctor->bio,
                'total_appointments' => $user->doctor->appointments()->count(),
                'total_patients' => \App\Models\Pet::whereHas('appointments', function($q) use ($user) {
                    $q->where('doctor_id', $user->doctor->id);
                })->distinct()->count(),
            ];
        }
        
        return [];
    }
}