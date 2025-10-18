<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Doctor;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $appointments = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])
                ->orderBy('appointment_date', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'doctor') {
            $doctor = $user->doctor;
            $appointments = Appointment::where('doctor_id', $doctor->id)
                ->with(['pet.owner.user', 'service'])
                ->orderBy('appointment_date', 'desc')
                ->paginate(15);
        } else {
            $petOwner = $user->petOwner;
            $appointments = Appointment::whereHas('pet', function ($q) use ($petOwner) {
                $q->where('owner_id', $petOwner->id);
            })
            ->with(['pet', 'doctor.user', 'service'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(15);
        }
        
        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            $pets = $petOwner->pets;
            $doctor = Doctor::first();
        } elseif ($user->role === 'admin') {
            $pets = Pet::with('owner')->get();
            $doctor = Doctor::first();
        } else {
            $pets = Pet::with('owner')->get();
            $doctor = $user->doctor;
        }
        $services = Service::where('is_active', true)->get();
        $doctors = Doctor::with('user')->get();
        return view('appointments.create', compact('pets', 'services', 'doctors', 'doctor'));
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Verify pet ownership for pet owners
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            $pet = Pet::where('id', $request->pet_id)
                ->where('owner_id', $petOwner->id)
                ->first();
            
            if (!$pet) {
                return back()->withErrors(['pet_id' => 'Invalid pet selection.']);
            }
        }

        // Get doctor (use provided or get the first available)
        $doctorId = $request->doctor_id ?? Doctor::first()->id;
        
        if (!$doctorId) {
            return back()->withErrors(['error' => 'No doctor available in the system.']);
        }

        // Validate time is between 8 AM and 6 PM
        $time = Carbon::createFromFormat('H:i', $request->appointment_time);
        if ($time->hour < 8 || $time->hour >= 18) {
            return back()->withErrors(['appointment_time' => 'Appointments can only be scheduled between 8:00 AM and 6:00 PM.']);
        }

        // Check for time conflicts
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please select another time.']);
        }

        Appointment::create([
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctorId,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        $petOwnerName = Auth::user()->name;
        $petName = $appointment->pet->name;
        $date = $appointment->appointment_date->format('M d, Y');
    
    $this->notifyAdminsAndDoctors(
        'appointment_request',
        'New Appointment Request',
        "{$petOwnerName} requested an appointment for {$petName} on {$date}",
        $appointment->id
    );


        $redirectRoute = $user->role === 'pet_owner' ? 'pet-owner.appointments' : 'appointments.index';
        return redirect()->route($redirectRoute)
            ->with('success', 'Appointment request submitted successfully. Waiting for approval.');
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            if ($appointment->pet->owner_id !== $petOwner->id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($user->role === 'doctor') {
            $doctor = $user->doctor;
            if ($appointment->doctor_id !== $doctor->id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        $appointment->load(['pet.owner.user', 'doctor.user', 'service']);
        
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment
     */
    public function edit(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            if ($appointment->pet->owner_id !== $petOwner->id) {
                abort(403, 'Unauthorized action.');
            }
            
            // Pet owners can only edit pending appointments
            if ($appointment->status !== 'pending') {
                return back()->with('error', 'You can only edit pending appointments.');
            }
        }
        
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            $pets = $petOwner->pets;
        } else {
            $pets = Pet::with('owner')->get();
        }
        
        $services = Service::where('is_active', true)->get();
        $doctors = Doctor::with('user')->get();
        
        return view('appointments.edit', compact('appointment', 'pets', 'services', 'doctors'));
    }

    /**
     * Update the specified appointment
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,scheduled,completed,cancelled',
        ]);

        $user = Auth::user();
        
        // Authorization check for pet owners
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            
            // Verify pet ownership
            $pet = Pet::where('id', $request->pet_id)
                ->where('owner_id', $petOwner->id)
                ->first();
            
            if (!$pet) {
                return back()->withErrors(['pet_id' => 'Invalid pet selection.']);
            }
            
            // Pet owners can only edit pending appointments
            if ($appointment->status !== 'pending') {
                return back()->with('error', 'You can only edit pending appointments.');
            }
        }

        $doctorId = $request->doctor_id ?? $appointment->doctor_id;

        // Validate time is between 8 AM and 6 PM
        $time = Carbon::createFromFormat('H:i', $request->appointment_time);
        if ($time->hour < 8 || $time->hour >= 18) {
            return back()->withErrors(['appointment_time' => 'Appointments can only be scheduled between 8:00 AM and 6:00 PM.']);
        }

        // Check for time conflicts (excluding current appointment)
        $conflict = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['appointment_time' => 'This time slot is already booked. Please select another time.']);
        }

        $updateData = [
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctorId,
            'service_id' => $request->service_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
        ];

        // Only admin and doctor can update status
        if (in_array($user->role, ['admin', 'doctor']) && $request->has('status')) {
            $updateData['status'] = $request->status;
        }

        $appointment->update($updateData);

        $redirectRoute = $user->role === 'pet_owner' ? 'pet-owner.appointments' : 'appointments.index';
        return redirect()->route($redirectRoute)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment
     */
    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            if ($appointment->pet->owner_id !== $petOwner->id) {
                return back()->with('error', 'Unauthorized action.');
            }
            
            // Pet owners can only delete pending appointments
            if ($appointment->status !== 'pending') {
                return back()->with('error', 'You can only delete pending appointments.');
            }
        }
        
        $appointment->delete();
        
        $redirectRoute = $user->role === 'pet_owner' ? 'pet-owner.appointments' : 'appointments.index';
        return redirect()->route($redirectRoute)
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableTimeSlots(Request $request)
    {
        $date = $request->input('date');
        $doctorId = $request->input('doctor_id');
        
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Get doctor
        if ($doctorId) {
            $doctor = Doctor::find($doctorId);
        } else {
            $doctor = Doctor::first();
        }
        
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

    /**
     * Show calendar view of appointments
     */
    public function calendar()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $appointments = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])->get();
        } elseif ($user->role === 'doctor') {
            $doctor = $user->doctor;
            $appointments = Appointment::where('doctor_id', $doctor->id)
                ->with(['pet.owner.user', 'service'])
                ->get();
        } else {
            $petOwner = $user->petOwner;
            $appointments = Appointment::whereHas('pet', function ($q) use ($petOwner) {
                $q->where('owner_id', $petOwner->id);
            })
            ->with(['pet', 'doctor.user', 'service'])
            ->get();
        }
        
        return view('appointments.calendar', compact('appointments'));
    }

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

        private function notifyAdminsAndDoctors($type, $title, $message, $appointmentId = null)
{
    $users = User::whereIn('role', ['admin', 'doctor'])->get();
    
    foreach ($users as $user) {
        $this->createNotification($user->id, $type, $title, $message, $appointmentId);
    }
}
    /**
     * Request cancellation for an appointment (Pet Owner)
     */
    public function requestCancellation(Request $request, Appointment $appointment)
    {
        // Validate the cancellation reason
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        
        // Check if user is a pet owner and owns this appointment
        if ($user->role === 'pet_owner') {
            $petOwner = $user->petOwner;
            
            // Verify the appointment belongs to this pet owner
            if ($appointment->pet->owner_id !== $petOwner->id) {
                return redirect()->route('pet-owner.appointments')
                    ->with('error', 'Unauthorized action.');
            }
        }

        // Check if appointment can be cancelled
        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed appointment.');
        }

        if ($appointment->status === 'cancelled') {
            return back()->with('error', 'This appointment is already cancelled.');
        }

        if ($appointment->cancellation_status === 'pending') {
            return back()->with('error', 'A cancellation request is already pending for this appointment.');
        }

        // Update appointment with cancellation request
        $appointment->update([
            'cancellation_status' => 'pending',
            'cancellation_reason' => $request->cancellation_reason,
            'cancellation_requested_at' => now(),
        ]);
    $petOwnerName = Auth::user()->name;
    $petName = $appointment->pet->name;
    $date = $appointment->appointment_date->format('M d, Y');
    
    $this->notifyAdminsAndDoctors(
        'cancellation_request',
        'Cancellation Request',
        "{$petOwnerName} requested to cancel appointment for {$petName} on {$date}",
        $appointment->id
    );

    return redirect()->route('pet-owner.appointments')
        ->with('success', 'Cancellation request submitted successfully.');
}

    /**
     * Approve cancellation request (Admin/Doctor)
     */
    public function approveCancellation(Appointment $appointment)
    {
        $user = Auth::user();
        
        // Only admin or doctor can approve
        if (!in_array($user->role, ['admin', 'doctor'])) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if there's a pending cancellation request
        if ($appointment->cancellation_status !== 'pending') {
            return back()->with('error', 'No pending cancellation request for this appointment.');
        }

        // Update appointment status
        $appointment->update([
            'status' => 'cancelled',
            'cancellation_status' => 'approved',
        ]);

       $petOwnerUserId = $appointment->pet->owner->user_id;
    $petName = $appointment->pet->name;
    $date = $appointment->appointment_date->format('M d, Y');
    
    $this->createNotification(
        $petOwnerUserId,
        'cancellation_approved',
        'Cancellation Approved',
        "Your cancellation request for {$petName}'s appointment on {$date} has been approved.",
        $appointment->id
    );

    return back()->with('success', 'Cancellation request approved.');
}

    /**
     * Decline cancellation request (Admin/Doctor)
     */
    public function declineCancellation(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        
        // Only admin or doctor can decline
        if (!in_array($user->role, ['admin', 'doctor'])) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if there's a pending cancellation request
        if ($appointment->cancellation_status !== 'pending') {
            return back()->with('error', 'No pending cancellation request for this appointment.');
        }

        // Update appointment status
        $appointment->update([
            'cancellation_status' => 'declined',
        ]);
 $petOwnerUserId = $appointment->pet->owner->user_id;
    $petName = $appointment->pet->name;
    $date = $appointment->appointment_date->format('M d, Y');
    
    $this->createNotification(
        $petOwnerUserId,
        'cancellation_declined',
        'Cancellation Declined',
        "Your cancellation request for {$petName}'s appointment on {$date} has been declined.",
        $appointment->id
    );

    return back()->with('success', 'Cancellation request declined.');
}

 
}