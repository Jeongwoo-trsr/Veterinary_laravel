<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;
use App\Models\Bill;
use App\Models\BillItem;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:doctor');
    }

    public function dashboard()
    {
        $doctor = Auth::user()->doctor;
        
        $stats = [
            'total_appointments' => $doctor->appointments()->count(),
            'pending_appointments' => $doctor->appointments()->where('status', 'scheduled')->count(),
            'completed_appointments' => $doctor->appointments()->where('status', 'completed')->count(),
            'total_medical_records' => $doctor->medicalRecords()->count(),
        ];

        $recent_appointments = $doctor->appointments()
            ->with(['pet.owner.user', 'service'])
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();

        return view('doctor.dashboard', compact('stats', 'recent_appointments'));
    }

    public function appointments(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $query = $doctor->appointments()
            ->with(['pet.owner.user', 'service'])
            ->orderBy('appointment_date', 'desc');

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('pet', function($petQuery) use ($search) {
                    $petQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('owner.user', function($ownerQuery) use ($search) {
                            $ownerQuery->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->orWhereHas('service', function($serviceQuery) use ($search) {
                    $serviceQuery->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $appointments = $query->paginate(15);
        
        return view('doctor.appointments', compact('appointments'));
    }

    public function approveAppointment(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;
        
        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->update(['status' => 'scheduled']);
        
        // NOTIFY PET OWNER
        $petOwnerUserId = $appointment->pet->owner->user_id;
        $petName = $appointment->pet->name;
        $date = $appointment->appointment_date->format('M d, Y');
        $time = $appointment->appointment_time;

        \App\Http\Controllers\AdminController::createNotification(
            $petOwnerUserId,
            'appointment_approved',
            'Appointment Approved',
            "Your appointment for {$petName} on {$date} at {$time} has been approved!",
            $appointment->id
        );

        return redirect()->route('doctor.appointments')->with('success', 'Appointment approved and owner notified.');
    }

    public function rejectAppointment(Appointment $appointment)
    {
        $appointment->update(['status' => 'cancelled']);

        // Notify pet owner
        $petOwnerUserId = $appointment->pet->owner->user_id;
        $petName = $appointment->pet->name;
        $date = $appointment->appointment_date->format('M d, Y');
        $time = $appointment->appointment_time;

        \App\Http\Controllers\AdminController::createNotification(
            $petOwnerUserId,
            'appointment_rejected',
            'Appointment Rejected',
            "Your appointment for {$petName} on {$date} at {$time} has been rejected.",
            $appointment->id
        );

        return redirect()->route('doctor.appointments')->with('success', 'Appointment rejected and owner notified.');
    }

    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;
        
        if ($appointment->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,scheduled,completed,cancelled',
        ]);

        $appointment->update(['status' => $request->status]);
        
        return redirect()->route('doctor.appointments')->with('success', 'Appointment status updated successfully.');
    }

    public function patients(Request $request)
    {
        // Show ALL pets in the system, not just those with appointments
        $query = Pet::with(['owner.user', 'medicalRecords']);

        // Species filter
        if ($request->filled('species')) {
            $species = $request->input('species');
            $query->where('species', $species);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('breed', 'like', '%' . $search . '%')
                    ->orWhereHas('owner.user', function($ownerQuery) use ($search) {
                        $ownerQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $pets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('doctor.patients', compact('pets'));
    }

    public function getPatientDetails(Pet $pet)
    {
        $doctor = Auth::user()->doctor;
        
        // Load all relationships
        $pet->load([
            'owner',
            'owner.user',
            'appointments' => function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            },
            'medicalRecords' => function($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        ]);
        
        // Add counts
        $pet->appointments_count = $pet->appointments->count();
        $pet->medical_records_count = $pet->medicalRecords->count();
        
        // Explicitly add owner fields to ensure they're included in JSON
        $petData = $pet->toArray();
        if ($pet->owner) {
            $petData['owner']['emergency_contact'] = $pet->owner->emergency_contact;
            $petData['owner']['emergency_phone'] = $pet->owner->emergency_phone;
            $petData['owner']['notes'] = $pet->owner->notes;
        }
        
        return response()->json($petData);
    }

   public function medicalRecords(Request $request)
{
    $doctorId = auth()->user()->doctor->id;
    
    $query = MedicalRecord::with(['pet.owner.user', 'doctor.user', 'appointment'])
        ->where('doctor_id', $doctorId);

    // Search functionality
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->whereHas('pet', function($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('pet.owner.user', function($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%");
            })
            ->orWhere('diagnosis', 'like', "%{$search}%")
            ->orWhere('treatment', 'like', "%{$search}%");
        });
    }

    $medicalRecords = $query->orderBy('created_at', 'desc')->paginate(15);

    // Handle AJAX requests for live search
    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return view('doctor.medical-records', compact('medicalRecords'))->render();
    }

    return view('doctor.medical-records', compact('medicalRecords'));
}

    public function destroyAppointment(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Ensure the appointment belongs to the logged-in doctor
        if ($appointment->doctor_id !== $doctor->id) {
            return redirect()->route('doctor.appointments')->with('error', 'Unauthorized action.');
        }

        $appointment->delete();

        return redirect()->route('doctor.appointments')->with('success', 'Appointment deleted successfully.');
    }

    public function bills()
    {
        $doctor = Auth::user()->doctor;
        $bills = Bill::where('doctor_id', $doctor->id)
            ->with(['pet.owner.user', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('doctor.bills', compact('bills'));
    }

    public function createBill()
    {
        $doctor = Auth::user()->doctor;
        // Show all pets instead of only those with appointments
        $pets = Pet::with('owner.user')->orderBy('name', 'asc')->get();

        return view('doctor.bills-create', compact('pets'));
    }

    public function storeBill(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $doctor = Auth::user()->doctor;
        $totalAmount = collect($request->items)->sum('amount');

        $bill = Bill::create([
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctor->id,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'balance' => $totalAmount,
            'status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        foreach ($request->items as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'description' => $item['description'],
                'amount' => $item['amount'],
            ]);
        }

        return redirect()->route('doctor.bills')->with('success', 'Bill created successfully.');
    }

    public function showBill(Bill $bill)
    {
        $doctor = Auth::user()->doctor;
        
        if ($bill->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $bill->load(['pet.owner.user', 'items']);
        return response()->json($bill);
    }

    public function updateBillItems(Request $request, Bill $bill)
    {
        $doctor = Auth::user()->doctor;
        
        if ($bill->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Delete existing items
        $bill->items()->delete();

        // Add new items
        $totalAmount = 0;
        foreach ($request->items as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'description' => $item['description'],
                'amount' => $item['amount'],
            ]);
            $totalAmount += $item['amount'];
        }

        // Update bill total
        $bill->total_amount = $totalAmount;
        $bill->calculateBalance();

        return redirect()->route('doctor.bills')->with('success', 'Bill items updated successfully.');
    }

    public function updateBillStatus(Request $request, Bill $bill)
    {
        $doctor = Auth::user()->doctor;
        
        if ($bill->doctor_id !== $doctor->id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:unpaid,partial,paid',
            'paid_amount' => 'required|numeric|min:0|max:' . $bill->total_amount,
        ]);

        $bill->paid_amount = $request->paid_amount;
        $bill->calculateBalance();

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bill status updated successfully.',
                'bill' => $bill
            ]);
        }

        // Return redirect for traditional form submissions
        return redirect()->route('doctor.bills')->with('success', 'Bill status updated successfully.');
    }
}