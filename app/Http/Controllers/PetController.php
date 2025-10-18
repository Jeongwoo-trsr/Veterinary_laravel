<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function createAppointment()
    {
        $petOwner = Auth::user()->petOwner;
        $pets = $petOwner->pets;
        
        // Get the single doctor (assuming there's only one)
        $doctor = Doctor::first();
        
        $services = Service::where('is_active', true)->get();
        
        return view('pet-owner.appointments-schedule', compact('pets', 'doctor', 'services'));
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Get the single doctor
        $doctor = Doctor::first();
        
        if (!$doctor) {
            return back()->withErrors(['error' => 'No doctor available at the moment.']);
        }

        // Verify pet belongs to this owner
        $petOwner = Auth::user()->petOwner;
        $pet = Pet::where('id', $request->pet_id)
                  ->where('owner_id', $petOwner->id)
                  ->first();
                  
        if (!$pet) {
            return back()->withErrors(['pet_id' => 'Invalid pet selection.']);
        }

        // Validate business hours (8 AM to 6 PM)
        $time = \Carbon\Carbon::parse($request->appointment_time);
        $openingTime = \Carbon\Carbon::parse('08:00');
        $closingTime = \Carbon\Carbon::parse('18:00');
        
        if ($time->lt($openingTime) || $time->gte($closingTime)) {
            return back()->withErrors(['appointment_time' => 'Please select a time between 8:00 AM and 6:00 PM.']);
        }

        // Check for conflicts
        $conflict = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please choose another time.']);
        }

        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctor->id,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Notify admin(s) and doctor(s) about new appointment request
        $petName = $appointment->pet->name;
        $date = $appointment->appointment_date->format('M d, Y');
        $time = $appointment->appointment_time;

        // notify all admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Http\Controllers\AdminController::createNotification(
                $admin->id,
                'appointment_requested',
                'New Appointment Request',
                "Appointment request for {$petName} on {$date} at {$time}.",
                $appointment->id
            );
        }

        // notify assigned doctor
        $doctorUserId = $appointment->doctor->user_id ?? null;
        if ($doctorUserId) {
            \App\Http\Controllers\AdminController::createNotification(
                $doctorUserId,
                'appointment_requested',
                'New Appointment Request',
                "You have a new appointment request for {$petName} on {$date} at {$time}.",
                $appointment->id
            );
        }

        return redirect()->route('pet-owner.appointments')
            ->with('success', 'Appointment request submitted successfully. Waiting for approval.');
    }

    // API endpoint to check available time slots
    public function getAvailableTimeSlots(Request $request)
    {
        $date = $request->input('date');
        $doctor = Doctor::first();
        
        if (!$doctor) {
            return response()->json(['error' => 'No doctor available'], 404);
        }

        // Get all booked appointments for this date
        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->map(function($time) {
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // Generate time slots from 8 AM to 6 PM (every 30 minutes)
        $timeSlots = [];
        $start = \Carbon\Carbon::parse('08:00');
        $end = \Carbon\Carbon::parse('18:00');
        
        while ($start->lt($end)) {
            $timeString = $start->format('H:i');
            $timeSlots[] = [
                'time' => $timeString,
                'display' => $start->format('g:i A'),
                'available' => !in_array($timeString, $bookedSlots)
            ];
            $start->addMinutes(30);
        }

        return response()->json($timeSlots);
    }

    // List pets with optional search
   // Replace the index method in PetController.php

public function index(Request $request)
{
    $search = $request->input('search');
    $species = $request->input('species');

    $petsQuery = Pet::with('owner.user');

    // Search functionality
    if ($search) {
        $petsQuery->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('species', 'like', "%{$search}%")
              ->orWhere('breed', 'like', "%{$search}%")
              ->orWhereHas('owner.user', function($ownerQuery) use ($search) {
                  $ownerQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    // Species filter
    if ($species) {
        $petsQuery->where('species', 'like', $species);
    }

    $pets = $petsQuery->orderBy('name')->paginate(10)->withQueryString();

    // Handle AJAX requests for live search
    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return view('pets.index', compact('pets', 'search'))->render();
    }

    return view('pets.index', compact('pets', 'search'));
}

    public function show($id)
    {
        $pet = Pet::with(['owner.user', 'appointments.doctor.user', 'appointments.service'])->findOrFail($id);
        return view('pets.show', compact('pet'));
    }
    
    public function edit($id)
    {
        $pet = Pet::findOrFail($id);
        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        $validated = $request->validate([
            'owner_id' => 'required|exists:pet_owners,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'required|integer|min:0|max:30',
            'weight' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'microchip_id' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string',
        ]);

        try {
            $pet->update($validated);

            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'pet' => $pet]);
            }

            return redirect()->route('pets.show', $pet->id)
                ->with('success', 'Pet information updated successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error updating pet: ' . $e->getMessage()], 500);
            }

            return back()->with('error', 'Error updating pet details. Please try again.');
        }
    }

    // Add missing store method to handle resource route POST /pets
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:pet_owners,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:30',
            'weight' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'medical_notes' => 'nullable|string',
        ]);

        $pet = Pet::create($validated);

        return redirect()->route('pets.index')
            ->with('success', 'Pet created successfully.');
    }
}