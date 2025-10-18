<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PetOwner;
use App\Models\Pet;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_pets' => Pet::count(),
            'total_owners' => PetOwner::count(),
            'total_doctors' => Doctor::count(),
            'total_appointments' => Appointment::count(),
            'pending_appointments' => Appointment::where('status', 'scheduled')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
            'total_services' => Service::count(),
            'total_medical_records' => MedicalRecord::count(),
        ];

        $recent_appointments = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $monthly_appointments = Appointment::select(
                DB::raw('MONTH(appointment_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('appointment_date', date('Y'))
            ->groupBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_appointments', 'monthly_appointments'));
    }

   public function petOwners(Request $request)
{
    $query = PetOwner::with(['user', 'pets']);
    
    // Add search functionality
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->whereHas('user', function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }
    
    $petOwners = $query->paginate(15);
    return view('admin.pet-owners', compact('petOwners'));
}

public function storePetOwner(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'emergency_contact' => 'nullable|string|max:255',
        'emergency_phone' => 'nullable|string|max:20',
    ]);

    // Create user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'address' => $request->address,
        'role' => 'pet_owner',
    ]);

    // Create pet owner profile
    PetOwner::create([
        'user_id' => $user->id,
        'emergency_contact' => $request->emergency_contact,
        'emergency_phone' => $request->emergency_phone,
    ]);

    return redirect()->route('admin.pet-owners')->with('success', 'Pet owner added successfully.');
}



public function showPetOwner(PetOwner $petOwner)
{
    $petOwner->load(['user', 'pets']);
    return view('pet-owner.show', compact('petOwner'));
}

public function editPetOwner(PetOwner $petOwner)
{
    $petOwner->load('user');
    return view('pet-owner.edit', compact('petOwner'));
}

public function updatePetOwner(Request $request, PetOwner $petOwner)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $petOwner->user_id,
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'emergency_contact' => 'nullable|string|max:255',
        'emergency_phone' => 'nullable|string|max:20',
    ]);

    // Update user information
    $petOwner->user->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
    ]);

    // Update pet owner profile
    $petOwner->update([
        'emergency_contact' => $request->emergency_contact,
        'emergency_phone' => $request->emergency_phone,
    ]);

    return redirect()->route('admin.pet-owners')->with('success', 'Pet owner updated successfully.');
}


  // Replace the pets method in AdminController.php

public function pets(Request $request)
{
    $query = Pet::with(['owner.user']);
    
    // Search functionality
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('species', 'like', "%{$search}%")
              ->orWhere('breed', 'like', "%{$search}%")
              ->orWhereHas('owner.user', function($ownerQuery) use ($search) {
                  $ownerQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    // Species filter
    if ($request->filled('species')) {
        $species = $request->input('species');
        $query->where('species', 'like', $species);
    }
    
    $pets = $query->paginate(15);

    // Handle AJAX requests for live search
    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return view('admin.pets', compact('pets'))->render();
    }

    return view('admin.pets', compact('pets'));
}

    public function doctors()
    {
        $doctors = Doctor::with('user')->paginate(15);
        return view('admin.doctors', compact('doctors'));
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

    public function appointments(Request $request)
{
    $query = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])
        ->orderBy('appointment_date', 'desc');

    // Search filter
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->whereHas('pet', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })->orWhereHas('pet.owner.user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })->orWhereHas('doctor.user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })->orWhereHas('service', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            });
        });
    }

    // Status filter (including today's appointments)
    if ($request->filled('status')) {
        $status = $request->input('status');
        if ($status === 'today') {
            $query->whereDate('appointment_date', today());
        } else {
            $query->where('status', $status);
        }
    }

    $appointments = $query->paginate(15);
    
    // Get pending appointments separately (not paginated)
    $pendingAppointments = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])
        ->where('status', 'pending')
        ->orderBy('appointment_date', 'desc')
        ->get();
    
    return view('admin.appointments', compact('appointments', 'pendingAppointments'));
}



public function approveAppointment(Appointment $appointment)
{
    $appointment->update(['status' => 'scheduled']);

  // NOTIFY PET OWNER
    $petOwnerUserId = $appointment->pet->owner->user_id;
    $petName = $appointment->pet->name;
    $date = $appointment->appointment_date->format('M d, Y');
    $time = $appointment->appointment_time;
    
    $this->createNotification(
        $petOwnerUserId,
        'appointment_approved',
        'Appointment Approved',
        "Your appointment for {$petName} on {$date} at {$time} has been approved!",
        $appointment->id
    );

    return redirect()->route('admin.appointments')
        ->with('success', 'Appointment approved successfully.');
}


public function rejectAppointment(Appointment $appointment)
{
    $appointment->update(['status' => 'cancelled']);
  // NOTIFY PET OWNER
    $petOwnerUserId = $appointment->pet->owner->user_id;
    $petName = $appointment->pet->name;
    $date = $appointment->appointment_date->format('M d, Y');
    
    $this->createNotification(
        $petOwnerUserId,
        'appointment_rejected',
        'Appointment Rejected',
        "Your appointment request for {$petName} on {$date} has been rejected.",
        $appointment->id
    );

    return redirect()->route('admin.appointments')
        ->with('success', 'Appointment rejected.');
}


    public function services()
    {
        $services = Service::paginate(15);
        return view('admin.services', compact('services'));
    }

    public function medicalRecords(Request $request)
{
    $query = MedicalRecord::with(['pet.owner.user', 'doctor.user', 'appointment']);

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
            ->orWhereHas('doctor.user', function($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%");
            })
            ->orWhere('diagnosis', 'like', "%{$search}%")
            ->orWhere('treatment', 'like', "%{$search}%");
        });
    }

    $medicalRecords = $query->orderBy('created_at', 'desc')->paginate(15);

    // Handle AJAX requests for live search
    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
        return view('admin.medical-records', compact('medicalRecords'))->render();
    }

    return view('admin.medical-records', compact('medicalRecords'));
}

    public function reports()
    {
        $revenue_by_service = Service::select('name', DB::raw('SUM(price) as total_revenue'))
            ->join('appointments', 'services.id', '=', 'appointments.service_id')
            ->where('appointments.status', 'completed')
            ->groupBy('services.id', 'services.name')
            ->get();

        $appointments_by_status = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $pets_by_species = Pet::select('species', DB::raw('COUNT(*) as count'))
            ->groupBy('species')
            ->get();

        return view('admin.reports', compact('revenue_by_service', 'appointments_by_status', 'pets_by_species'));
    }

    public function inventory()
{
    $items = InventoryItem::orderBy('created_at', 'desc')->paginate(15);
    
    $totalItems = InventoryItem::count();
    $lowStockCount = InventoryItem::lowStock()->count();
    $expiredCount = InventoryItem::expired()->count();
    $topUsedItems = InventoryItem::orderBy('current_stock', 'asc')
                                   ->where('current_stock', '>', 0)
                                   ->limit(4)
                                   ->get();

    return view('admin.inventory', compact('items', 'totalItems', 'lowStockCount', 'expiredCount', 'topUsedItems'));
}


    public function destroyPet(Pet $pet)
    {
        $pet->delete();
        return redirect()->route('admin.pets')->with('success', 'Pet deleted successfully.');
    }

    public function destroyPetOwner(PetOwner $petOwner)
    {
        $petOwner->delete();
        return redirect()->route('admin.pet-owners')->with('success', 'Pet owner deleted successfully.');
    }

    public function destroyDoctor(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.doctors')->with('success', 'Doctor deleted successfully.');
    }

    public function editAppointment(Appointment $appointment)
    {
        return view('admin.appointments-edit', compact('appointment'));
    }

    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointment->update($request->all());

        return redirect()->route('admin.appointments')->with('success', 'Appointment updated successfully.');
    }

    public function destroyAppointment(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments')->with('success', 'Appointment deleted successfully.');
    }


    public function inventoryFilter($type)
{
    switch($type) {
        case 'low-stock':
            $items = InventoryItem::lowStock()->orderBy('created_at', 'desc')->paginate(15);
            $filterTitle = 'Low Stock Items';
            break;
        case 'expired':
            $items = InventoryItem::expired()->orderBy('created_at', 'desc')->paginate(15);
            $filterTitle = 'Expired Items';
            break;
        case 'top-used':
            $items = InventoryItem::orderBy('current_stock', 'asc')
                                   ->where('current_stock', '>', 0)
                                   ->paginate(15);
            $filterTitle = 'Top Used Items';
            break;
        default:
            return redirect()->route('admin.inventory');
    }
    
    $totalItems = InventoryItem::count();
    $lowStockCount = InventoryItem::lowStock()->count();
    $expiredCount = InventoryItem::expired()->count();
    $topUsedItems = InventoryItem::orderBy('current_stock', 'asc')
                                   ->where('current_stock', '>', 0)
                                   ->limit(4)
                                   ->get();

    return view('admin.inventory', compact('items', 'totalItems', 'lowStockCount', 'expiredCount', 'topUsedItems', 'filterTitle'));
}


}
