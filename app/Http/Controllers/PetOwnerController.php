<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Service;
use App\Models\Doctor;
use App\Models\Bill;
use App\Models\Notification;
use App\Models\User;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PetOwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:pet_owner');
    }

    public function dashboard()
    {
        $petOwner = Auth::user()->petOwner;
        
        // Calculate statistics
        $totalMedicalRecords = MedicalRecord::whereHas('pet', function ($q) use ($petOwner) {
            $q->where('owner_id', $petOwner->id);
        })->count();
        
        $stats = [
            'total_pets' => $petOwner->pets()->approved()->count(),
            'pending_pets' => $petOwner->pets()->pending()->count(),
            'upcoming_appointments' => Appointment::whereHas('pet', function ($q) use ($petOwner) {
                $q->where('owner_id', $petOwner->id);
            })->where('status', 'scheduled')->count(),
            'total_appointments' => Appointment::whereHas('pet', function ($q) use ($petOwner) {
                $q->where('owner_id', $petOwner->id);
            })->count(),
            'medical_records' => $totalMedicalRecords,
            'total_medical_records' => $totalMedicalRecords,
        ];

        $recent_appointments = Appointment::whereHas('pet', function ($q) use ($petOwner) {
            $q->where('owner_id', $petOwner->id);
        })
        ->with(['pet', 'doctor.user', 'service'])
        ->orderBy('appointment_date', 'desc')
        ->limit(5)
        ->get();

        // Get latest 5 announcements
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pet-owner.dashboard', compact('stats', 'recent_appointments', 'announcements'));
    }

    public function pets()
    {
        $petOwner = Auth::user()->petOwner;
        $pets = $petOwner->pets()->with('appointments', 'medicalRecords')->paginate(10);
        
        return view('pet-owner.pets', compact('pets'));
    }

    public function showPet($id)
    {
        $petOwner = Auth::user()->petOwner;
        
        // Find the pet that belongs to this pet owner
        $pet = Pet::with(['owner.user', 'appointments.service', 'appointments.doctor.user'])
            ->where('owner_id', $petOwner->id)
            ->where('id', $id)
            ->firstOrFail();
        
        // Return the pet-owner specific view (without edit button)
        return view('pet-owner.show', compact('pet'));
    }

    // NEW: Create pet form for pet owners
    public function createPet()
    {
        return view('pet-owner.pets.create');
    }

    // NEW: Store pet with pending approval
    public function storePet(Request $request)
    {
        $petOwner = Auth::user()->petOwner;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'required|integer|min:0|max:30',
            'weight' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'medical_notes' => 'nullable|string',
        ]);

        // Create pet with pending status
        $pet = Pet::create([
            'owner_id' => $petOwner->id,
            'name' => $validated['name'],
            'species' => $validated['species'],
            'breed' => $validated['breed'],
            'age' => $validated['age'],
            'weight' => $validated['weight'],
            'color' => $validated['color'],
            'gender' => $validated['gender'],
            'medical_notes' => $validated['medical_notes'],
            'approval_status' => 'pending',
        ]);

        // Notify all admins
        $petOwnerName = Auth::user()->name;
        $this->notifyAdminsAndDoctors(
            'pet_registration',
            'New Pet Registration',
            "{$petOwnerName} registered a new pet: {$pet->name} ({$pet->species}). Awaiting approval.",
            null
        );

        return redirect()->route('pet-owner.pets')
            ->with('success', 'Pet registered successfully! Waiting for admin approval.');
    }
    
    public function appointments()
    {
        $petOwner = Auth::user()->petOwner;
        $appointments = Appointment::whereHas('pet', function ($q) use ($petOwner) {
            $q->where('owner_id', $petOwner->id);
        })
        ->with(['pet', 'doctor.user', 'service'])
        ->orderBy('appointment_date', 'desc')
        ->paginate(15);

        return view('pet-owner.appointments', compact('appointments'));
    }

    public function createAppointment()
    {
        $petOwner = Auth::user()->petOwner;
        
        // Get only approved pets
        $pets = $petOwner->pets()->approved()->get();
        
        $services = Service::where('is_active', true)->get();
        
        // Get the only doctor
        $doctor = Doctor::with('user')->first();
        
        return view('pet-owner.appointments-schedule', compact('pets', 'services', 'doctor'));
    }

    public function storeAppointment(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $petOwner = Auth::user()->petOwner;
        
        // Verify the pet belongs to the authenticated pet owner AND is approved
        $pet = Pet::where('id', $request->pet_id)
            ->where('owner_id', $petOwner->id)
            ->where('approval_status', 'approved')
            ->first();
        
        if (!$pet) {
            return back()->withErrors(['pet_id' => 'Invalid pet selection or pet not yet approved.']);
        }

        // Get the only doctor
        $doctor = Doctor::first();
        
        if (!$doctor) {
            return back()->withErrors(['error' => 'No doctor available in the system.']);
        }

        // Validate time is between 8 AM and 6 PM
        $time = Carbon::createFromFormat('H:i', $request->appointment_time);
        if ($time->hour < 8 || $time->hour >= 18) {
            return back()->withErrors(['appointment_time' => 'Appointments can only be scheduled between 8:00 AM and 6:00 PM.']);
        }

        // Check for time conflicts
        $conflict = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please select another time.']);
        }

        // Create appointment
        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctor->id,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Prepare notification message
        $petOwnerName = Auth::user()->name;
        $petName = $pet->name;
        $date = Carbon::parse($appointment->appointment_date)->format('M d, Y');
        $time = $appointment->appointment_time;

        // Notify all admins and doctors
        $this->notifyAdminsAndDoctors(
            'appointment_request',
            'New Appointment Request',
            "{$petOwnerName} requested an appointment for {$petName} on {$date} at {$time}.",
            $appointment->id
        );

        return redirect()->route('pet-owner.appointments')
            ->with('success', 'Appointment request submitted successfully. Waiting for approval.');
    }

    public function getAvailableTimeSlots(Request $request)
    {
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $doctor = Doctor::first();
        
        if (!$doctor) {
            return response()->json(['error' => 'No doctor available'], 404);
        }

        // Get all booked times for the selected date
        $bookedTimes = Appointment::where('doctor_id', $doctor->id)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->map(function($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        // Generate time slots from 8 AM to 6 PM (every 30 minutes)
        $timeSlots = [];
        $start = Carbon::createFromFormat('H:i', '08:00');
        $end = Carbon::createFromFormat('H:i', '18:00');

        while ($start < $end) {
            $timeString = $start->format('H:i');
            $timeSlots[] = [
                'time' => $timeString,
                'display' => $start->format('g:i A'),
                'available' => !in_array($timeString, $bookedTimes)
            ];
            $start->addMinutes(30);
        }

        return response()->json($timeSlots);
    }

    public function medicalRecords()
    {
        $petOwner = Auth::user()->petOwner;
        $medicalRecords = MedicalRecord::whereHas('pet', function ($q) use ($petOwner) {
            $q->where('owner_id', $petOwner->id);
        })
        ->with(['pet', 'doctor.user', 'appointment'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('pet-owner.medical-records', compact('medicalRecords'));
    }

    public function bills()
    {
        $petOwner = Auth::user()->petOwner;
        $bills = Bill::whereHas('pet', function ($q) use ($petOwner) {
            $q->where('owner_id', $petOwner->id);
        })
        ->with(['pet', 'doctor.user', 'items'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('pet-owner.bills', compact('bills'));
    }

    public function showBill(Bill $bill)
    {
        $petOwner = Auth::user()->petOwner;
        
        // Verify the bill belongs to one of the pet owner's pets
        if ($bill->pet->owner_id !== $petOwner->id) {
            abort(403, 'Unauthorized action.');
        }

        $bill->load(['pet', 'doctor.user', 'items']);
        return response()->json($bill);
    }

    public function destroyPet(Pet $pet)
    {
        $petOwner = Auth::user()->petOwner;

        // Ensure the pet belongs to the logged-in pet owner
        if ($pet->owner_id !== $petOwner->id) {
            return redirect()->route('pet-owner.pets')->with('error', 'Unauthorized action.');
        }

        $pet->delete();

        return redirect()->route('pet-owner.pets')->with('success', 'Pet deleted successfully.');
    }

    public function destroyAppointment(Appointment $appointment)
    {
        $petOwner = Auth::user()->petOwner;

        // Ensure the appointment belongs to the logged-in pet owner
        if ($appointment->pet->owner_id !== $petOwner->id) {
            return redirect()->route('pet-owner.appointments')->with('error', 'Unauthorized action.');
        }

        $appointment->delete();

        return redirect()->route('pet-owner.appointments')->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Create a notification for a specific user
     */
    private function createNotification($userId, $type, $title, $message, $appointmentId = null)
    {
        Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'appointment_id' => $appointmentId,
        ]);
    }

    /**
     * Notify all admins and doctors
     */
    private function notifyAdminsAndDoctors($type, $title, $message, $appointmentId = null)
    {
        $users = User::whereIn('role', ['admin', 'doctor'])->get();
        
        foreach ($users as $user) {
            $this->createNotification($user->id, $type, $title, $message, $appointmentId);
        }
    }

    public function clinicDetails()
    {
        return view('pet-owner.clinic-details');
    }
}