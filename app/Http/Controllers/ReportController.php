<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\PetOwner;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClinicReportExport;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function export($format)
    {
        $data = $this->getReportData();
        
        if ($format === 'pdf') {
            return $this->exportToPDF($data);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($data);
        }
        
        abort(404, 'Format not supported');
    }

    private function getReportData()
    {
        $stats = [
            'total_pets' => Pet::count(),
            'total_owners' => PetOwner::count(),
            'total_doctors' => Doctor::count(),
            'total_appointments' => Appointment::count(),
            'total_services' => Service::count(),
            'total_medical_records' => MedicalRecord::count(),
        ];

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

        $recent_appointments = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $monthly_appointments = Appointment::select(
                DB::raw('MONTH(appointment_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('appointment_date', date('Y'))
            ->groupBy('month')
            ->get();

        return [
            'stats' => $stats,
            'revenue_by_service' => $revenue_by_service,
            'appointments_by_status' => $appointments_by_status,
            'pets_by_species' => $pets_by_species,
            'recent_appointments' => $recent_appointments,
            'monthly_appointments' => $monthly_appointments,
        ];
    }

    private function exportToPDF($data)
    {
        $pdf = Pdf::loadView('reports.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('veterinary-clinic-report-' . date('Y-m-d') . '.pdf');
    }

    private function exportToExcel($data)
    {
        return Excel::download(new ClinicReportExport($data), 'veterinary-clinic-report-' . date('Y-m-d') . '.xlsx');
    }

    public function exportPets()
    {
        $pets = Pet::with(['owner.user'])->get();
        
        $pdf = Pdf::loadView('reports.pets-pdf', compact('pets'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('pets-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportAppointments()
    {
        $appointments = Appointment::with(['pet.owner.user', 'doctor.user', 'service'])->get();
        
        $pdf = Pdf::loadView('reports.appointments-pdf', compact('appointments'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('appointments-report-' . date('Y-m-d') . '.pdf');
    }

    public function exportMedicalRecords()
    {
        $medicalRecords = MedicalRecord::with(['pet.owner.user', 'doctor.user', 'appointment'])->get();
        
        $pdf = Pdf::loadView('reports.medical-records-pdf', compact('medicalRecords'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('medical-records-report-' . date('Y-m-d') . '.pdf');
    }
}
