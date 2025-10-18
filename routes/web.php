<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PetOwnerController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InventoryController;


// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'doctor':
                return redirect()->route('doctor.dashboard');
            case 'pet_owner':
                return redirect()->route('pet-owner.dashboard');
            default:
                return redirect()->route('login');
        }
    }
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    
    // Get available time slots for appointments (MUST BE BEFORE resource routes)
    Route::get('/appointments/available-slots', [AppointmentController::class, 'getAvailableTimeSlots'])->name('appointments.available-slots');
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/pet-owners', [AdminController::class, 'petOwners'])->name('pet-owners');
        Route::post('/pet-owners/store', [AdminController::class, 'storePetOwner'])->name('pet-owners.store');        
        Route::get('/pet-owners/{petOwner}', [AdminController::class, 'showPetOwner'])->name('pet-owners.show');
        Route::get('/pet-owners/{petOwner}/edit', [AdminController::class, 'editPetOwner'])->name('pet-owners.edit');
        Route::put('/pet-owners/{petOwner}', [AdminController::class, 'updatePetOwner'])->name('pet-owners.update');
        Route::get('/pets', [AdminController::class, 'pets'])->name('pets');
        Route::get('/doctors', [AdminController::class, 'doctors'])->name('doctors');
        Route::get('/appointments', [AdminController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{appointment}/approve', [AdminController::class, 'approveAppointment'])->name('appointments.approve');
        Route::post('/appointments/{appointment}/reject', [AdminController::class, 'rejectAppointment'])->name('appointments.reject');
        
        // Admin cancellation approval routes
        Route::post('/appointments/{appointment}/approve-cancellation', [AppointmentController::class, 'approveCancellation'])->name('appointments.approve-cancellation');
        Route::post('/appointments/{appointment}/decline-cancellation', [AppointmentController::class, 'declineCancellation'])->name('appointments.decline-cancellation');
        
        Route::get('/services', [AdminController::class, 'services'])->name('services');
        Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
        Route::get('/medical-records', [AdminController::class, 'medicalRecords'])->name('medical-records');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/pets/export', [ReportController::class, 'exportPets'])->name('reports.pets.export');
        Route::get('/reports/appointments/export', [ReportController::class, 'exportAppointments'])->name('reports.appointments.export');
        Route::get('/reports/medical-records/export', [ReportController::class, 'exportMedicalRecords'])->name('reports.medical-records.export');
        Route::get('/inventory/filter/{type}', [AdminController::class, 'inventoryFilter'])->name('inventory.filter');
        Route::get('/inventory/{inventory}', [InventoryController::class, 'show'])->name('inventory.show');
       
        // Delete routes
        Route::delete('/pets/{pet}', [AdminController::class, 'destroyPet'])->name('pets.destroy');
        Route::delete('/pet-owners/{petOwner}', [AdminController::class, 'destroyPetOwner'])->name('pet-owners.destroy');
        Route::delete('/doctors/{doctor}', [AdminController::class, 'destroyDoctor'])->name('doctors.destroy');
    });


    // Doctor routes
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [DoctorController::class, 'appointments'])->name('appointments');
        Route::post('/appointments/{appointment}/approve', [DoctorController::class, 'approveAppointment'])->name('appointments.approve');
        Route::post('/appointments/{appointment}/reject', [DoctorController::class, 'rejectAppointment'])->name('appointments.reject');
        
        // Doctor cancellation approval routes
        Route::post('/appointments/{appointment}/approve-cancellation', [AppointmentController::class, 'approveCancellation'])->name('appointments.approve-cancellation');
        Route::post('/appointments/{appointment}/decline-cancellation', [AppointmentController::class, 'declineCancellation'])->name('appointments.decline-cancellation');
        
        Route::get('/patients', [DoctorController::class, 'patients'])->name('patients');
        Route::get('/patients/{pet}/details', [DoctorController::class, 'getPatientDetails'])->name('patients.details');
        Route::get('/medical-records', [DoctorController::class, 'medicalRecords'])->name('medical-records');
        
        Route::get('/bills', [DoctorController::class, 'bills'])->name('bills');
        Route::get('/bills/create', [DoctorController::class, 'createBill'])->name('bills.create');
        Route::post('/bills', [DoctorController::class, 'storeBill'])->name('bills.store');
        Route::get('/bills/{bill}', [DoctorController::class, 'showBill'])->name('bills.show');
        Route::put('/bills/{bill}/update-status', [DoctorController::class, 'updateBillStatus'])->name('bills.update-status');
        Route::put('/bills/{bill}/update-items', [DoctorController::class, 'updateBillItems'])->name('bills.update-items');
        
        Route::delete('/appointments/{appointment}', [DoctorController::class, 'destroyAppointment'])->name('appointments.destroy');
    });

    // Pet Owner routes
    Route::middleware(['auth', 'role:pet_owner'])
        ->prefix('pet-owner')
        ->name('pet-owner.')
        ->group(function () {
            Route::get('/dashboard', [PetOwnerController::class, 'dashboard'])->name('dashboard');
            Route::get('/pets', [PetOwnerController::class, 'pets'])->name('pets');
            Route::get('/appointments', [PetOwnerController::class, 'appointments'])->name('appointments');
            Route::get('/medical-records', [PetOwnerController::class, 'medicalRecords'])->name('medical-records');

            // Pet Owner specific pet view route (MUST be before other pets routes)
            Route::get('/pets/{id}', [PetOwnerController::class, 'showPet'])->name('pets.show');

            // Bills
            Route::get('/bills', [PetOwnerController::class, 'bills'])->name('bills');
            Route::get('/bills/{bill}', [PetOwnerController::class, 'showBill'])->name('bills.show');
                
            // Appointment scheduling
            Route::get('/appointments/create', [PetOwnerController::class, 'createAppointment'])->name('appointments.create');
            Route::post('/appointments', [PetOwnerController::class, 'storeAppointment'])->name('appointments.store');
            Route::get('/appointments/available-slots', [PetOwnerController::class, 'getAvailableTimeSlots'])->name('appointments.available-slots');
            Route::post('/appointments/{appointment}/request-cancellation', [AppointmentController::class, 'requestCancellation'])->name('appointments.request-cancellation');   
           
            Route::get('/medical-records', [PetOwnerController::class, 'medicalRecords'])->name('medical-records');
            
            // Pet owner cancellation request route  
            // Delete pet
            Route::delete('/pets/{pet}', [PetOwnerController::class, 'destroyPet'])->name('pets.destroy');
            Route::delete('/appointments/{appointment}', [PetOwnerController::class, 'destroyAppointment'])->name('appointments.destroy');
        });


    
    // Notification routes
    Route::middleware('auth')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    });

    Route::middleware('auth')->get('/api/notifications', function() {
        $notifications = Auth::user()->notifications()->limit(10)->get();
        $unreadCount = Auth::user()->unreadNotifications()->count();
        
        return response()->json([
            'notifications' => $notifications->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'icon' => $notif->icon,
                    'color' => $notif->color,
                    'is_read' => $notif->is_read,
                    'time_ago' => $notif->created_at->diffForHumans(),
                ];
            }),
            'unread_count' => $unreadCount,
        ]);
    });

    // Common resource routes
    Route::resource('pets', PetController::class);
    Route::resource('appointments', AppointmentController::class);
    // medical-records management (create/edit/delete) only for admin and doctor
    Route::middleware('role:admin,doctor')->group(function() {
        Route::resource('medical-records', MedicalRecordController::class)->except(['show']);
    });
    // Allow authenticated users to view a medical record
    Route::middleware('auth')->get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
    Route::resource('services', ServiceController::class);
    Route::resource('inventory', InventoryController::class);
    Route::post('/inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    
    // General cancellation routes (accessible by authenticated users)
    Route::post('/appointments/{appointment}/approve-cancellation', [AppointmentController::class, 'approveCancellation'])->name('appointments.approve-cancellation');
    Route::post('/appointments/{appointment}/decline-cancellation', [AppointmentController::class, 'declineCancellation'])->name('appointments.decline-cancellation');
    Route::post('/appointments/{appointment}/request-cancellation', [AppointmentController::class, 'requestCancellation'])->name('appointments.request-cancellation');
    
    // Additional routes
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
    
    // Medical record document routes
    Route::post('/medical-records/{medicalRecord}/upload-document', [MedicalRecordController::class, 'uploadDocument'])->name('medical-records.upload-document');
    Route::get('/documents/{documentId}/download', [MedicalRecordController::class, 'downloadDocument'])->name('documents.download');

    
});