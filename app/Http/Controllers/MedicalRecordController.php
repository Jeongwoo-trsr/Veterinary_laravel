<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\FollowUpSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MedicalRecord::query();

        // Load relationships
        $query->with(['pet.owner.user', 'doctor.user', 'appointment']);

        if ($user->isAdmin()) {
            // Admin sees all records
            $query = $query;
        } elseif ($user->isDoctor()) {
            // Doctor sees only their records
            $doctor = $user->doctor;
            $query->where('doctor_id', $doctor->id);
        } elseif ($user->isPetOwner()) {
            // Pet owner sees only their pets' records
            $petOwner = $user->petOwner;
            $query->whereHas('pet', function($q) use ($petOwner) {
                $q->where('owner_id', $petOwner->id);
            });
        } else {
            abort(403, 'Unauthorized access.');
        }

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

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'follow-up') {
                $query->whereNotNull('follow_up_date')
                    ->where('follow_up_date', '>=', now()->toDateString());
            } elseif ($status === 'resolved') {
                $query->where(function($q) {
                    $q->whereNull('follow_up_date')
                        ->orWhere('follow_up_date', '<', now()->toDateString());
                });
            }
        }

        // Sorting
        $sort = $request->input('sort', 'recent');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $medicalRecords = $query->paginate(15);

        // Handle AJAX requests for live search
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            // Return appropriate view based on user role
            if ($user->isAdmin()) {
                return view('admin.medical-records', compact('medicalRecords'))->render();
            } elseif ($user->isDoctor()) {
                return view('doctor.medical-records', compact('medicalRecords'))->render();
            }
            return view('pet-owner.medical-records', compact('medicalRecords'))->render();
        }

        // Return appropriate view based on user role
        if ($user->isAdmin()) {
            return view('admin.medical-records', compact('medicalRecords'));
        } elseif ($user->isDoctor()) {
            return view('doctor.medical-records', compact('medicalRecords'));
        }
        
        return view('pet-owner.medical-records', compact('medicalRecords'));
    }

 public function create()
{
    $user = Auth::user();
    
    // Prevent admins from creating medical records
    if ($user->isAdmin()) {
        abort(403, 'Admins cannot create medical records. Only doctors can create medical records.');
    }
    
    // Get all doctors in the system
    $doctors = Doctor::with('user')->get();
    
    if ($doctors->isEmpty()) {
        return redirect()->back()->with('error', 'No doctor available in the system.');
    }
    
    if ($user->isDoctor()) {
        $pets = Pet::with('owner.user')->get();
    } elseif ($user->isPetOwner()) {
        $petOwner = $user->petOwner;
        $pets = Pet::where('owner_id', $petOwner->id)->with('owner.user')->get();
    } else {
        abort(403, 'Unauthorized access.');
    }

    $appointments = Appointment::where('status', 'completed')
        ->with(['pet.owner.user', 'doctor.user', 'service'])
        ->get();
    
    return view('medical-records.create', compact('pets', 'doctors', 'appointments'));
}

    public function store(Request $request)
    {
        // Prevent admins from creating medical records
        if (Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Admins cannot create medical records. Only doctors can create medical records.');
        }

        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'diagnosis' => 'required|string|max:2000',
            'treatment' => 'required|string|max:2000',
            'prescription' => 'nullable|string|max:1000',
            'follow_up_date' => 'nullable|date|after:today',
        ]);

        // Get the only doctor
        $doctor = Doctor::first();
        
        if (!$doctor) {
            return redirect()->back()->with('error', 'No doctor available in the system.');
        }

        $medicalRecord = MedicalRecord::create([
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $request->appointment_id,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescription' => $request->prescription,
            'follow_up_date' => $request->follow_up_date,
        ]);

        // Create prescriptions if provided
        if ($request->has('medications')) {
            foreach ($request->medications as $medication) {
                if (!empty($medication['name'])) {
                    Prescription::create([
                        'medical_record_id' => $medicalRecord->id,
                        'medication_name' => $medication['name'],
                        'dosage' => $medication['dosage'] ?? null,
                        'frequency' => $medication['frequency'] ?? null,
                        'duration_days' => $medication['duration_days'] ?? null,
                        'instructions' => $medication['instructions'] ?? null,
                    ]);
                }
            }
        }

        // Create follow-up schedule if provided
        if ($request->follow_up_date) {
            FollowUpSchedule::create([
                'medical_record_id' => $medicalRecord->id,
                'scheduled_date' => $request->follow_up_date,
                'status' => 'pending',
                'notes' => $request->follow_up_notes ?? null,
            ]);
        }

        // Redirect based on user role
        $user = Auth::user();
        if ($user->isDoctor()) {
            return redirect()->route('doctor.medical-records')->with('success', 'Medical record created successfully.');
        } elseif ($user->isPetOwner()) {
            return redirect()->route('pet-owner.medical-records')->with('success', 'Medical record created successfully.');
        }
        
        return redirect()->back()->with('success', 'Medical record created successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load([
            'pet.owner.user', 
            'doctor.user', 
            'appointment.service',
            'prescriptions',
            'followUpSchedules',
            'documents'
        ]);
        
        return view('medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        // Prevent admins from editing medical records
        if ($user->isAdmin()) {
            abort(403, 'Admins cannot edit medical records. Only doctors can edit medical records.');
        }

        // Authorization check
        if ($user->isDoctor() && $medicalRecord->doctor_id !== $user->doctor->id) {
            abort(403, 'You are not authorized to edit this record.');
        } elseif ($user->isPetOwner() && $medicalRecord->pet->owner_id !== $user->petOwner->id) {
            abort(403, 'You are not authorized to edit this record.');
        }

        $pets = Pet::with('owner.user')->get();
        
        // Get the only doctor
        $doctor = Doctor::first();
        
        $appointments = Appointment::where('status', 'completed')
            ->with(['pet.owner.user', 'doctor.user', 'service'])
            ->get();
        
        $medicalRecord->load(['prescriptions', 'followUpSchedules']);
        
        return view('medical-records.edit', compact('medicalRecord', 'pets', 'doctor', 'appointments'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        // Prevent admins from updating medical records
        if (Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Admins cannot update medical records. Only doctors can update medical records.');
        }

        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'diagnosis' => 'required|string|max:2000',
            'treatment' => 'required|string|max:2000',
            'prescription' => 'nullable|string|max:1000',
            'follow_up_date' => 'nullable|date',
        ]);

        // Get the only doctor
        $doctor = Doctor::first();

        $medicalRecord->update([
            'pet_id' => $request->pet_id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $request->appointment_id,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescription' => $request->prescription,
            'follow_up_date' => $request->follow_up_date,
        ]);

        // Update prescriptions
        if ($request->has('medications')) {
            $medicalRecord->prescriptions()->delete();
            
            foreach ($request->medications as $medication) {
                if (!empty($medication['name'])) {
                    Prescription::create([
                        'medical_record_id' => $medicalRecord->id,
                        'medication_name' => $medication['name'],
                        'dosage' => $medication['dosage'] ?? null,
                        'frequency' => $medication['frequency'] ?? null,
                        'duration_days' => $medication['duration_days'] ?? null,
                        'instructions' => $medication['instructions'] ?? null,
                    ]);
                }
            }
        }

        // Redirect based on user role - FIXED
        $user = Auth::user();
        if ($user->isDoctor()) {
            return redirect()->route('doctor.medical-records')->with('success', 'Medical record updated successfully.');
        } elseif ($user->isPetOwner()) {
            return redirect()->route('pet-owner.medical-records')->with('success', 'Medical record updated successfully.');
        }

        return redirect()->back()->with('success', 'Medical record updated successfully.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        // Prevent admins from deleting medical records
        if (Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Admins cannot delete medical records. Only doctors can delete medical records.');
        }

        $medicalRecord->delete();
        
        // Redirect based on user role - FIXED
        $user = Auth::user();
        if ($user->isDoctor()) {
            return redirect()->route('doctor.medical-records')->with('success', 'Medical record deleted successfully.');
        } elseif ($user->isPetOwner()) {
            return redirect()->route('pet-owner.medical-records')->with('success', 'Medical record deleted successfully.');
        }

        return redirect()->back()->with('success', 'Medical record deleted successfully.');
    }

    public function uploadDocument(Request $request, MedicalRecord $medicalRecord)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('medical-documents', $fileName, 'public');

        $medicalRecord->documents()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function downloadDocument($documentId)
    {
        $document = \App\Models\Document::findOrFail($documentId);
        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document->file_name);
    }
}