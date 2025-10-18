<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ClinicReportExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            'Summary' => new SummarySheet($this->data),
            'Revenue by Service' => new RevenueByServiceSheet($this->data['revenue_by_service']),
            'Appointments by Status' => new AppointmentsByStatusSheet($this->data['appointments_by_status']),
            'Pets by Species' => new PetsBySpeciesSheet($this->data['pets_by_species']),
            'Recent Appointments' => new RecentAppointmentsSheet($this->data['recent_appointments']),
        ];
    }
}

class SummarySheet implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Total Pets', $this->data['stats']['total_pets']],
            ['Total Pet Owners', $this->data['stats']['total_owners']],
            ['Total Doctors', $this->data['stats']['total_doctors']],
            ['Total Appointments', $this->data['stats']['total_appointments']],
            ['Total Services', $this->data['stats']['total_services']],
            ['Total Medical Records', $this->data['stats']['total_medical_records']],
            ['Total Revenue', '$' . number_format($this->data['revenue_by_service']->sum('total_revenue'), 2)],
        ];
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Summary';
    }
}

class RevenueByServiceSheet implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->map(function ($item) {
            return [
                $item->name,
                '$' . number_format($item->total_revenue, 2)
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['Service Name', 'Total Revenue'];
    }

    public function title(): string
    {
        return 'Revenue by Service';
    }
}

class AppointmentsByStatusSheet implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->map(function ($item) {
            return [
                ucfirst(str_replace('_', ' ', $item->status)),
                $item->count
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['Status', 'Count'];
    }

    public function title(): string
    {
        return 'Appointments by Status';
    }
}

class PetsBySpeciesSheet implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->map(function ($item) {
            return [
                ucfirst($item->species),
                $item->count
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['Species', 'Count'];
    }

    public function title(): string
    {
        return 'Pets by Species';
    }
}

class RecentAppointmentsSheet implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data->map(function ($appointment) {
            return [
                $appointment->pet->name,
                $appointment->pet->owner->user->name,
                $appointment->doctor->user->name,
                $appointment->service->name,
                $appointment->appointment_date->format('Y-m-d'),
                $appointment->appointment_time,
                ucfirst(str_replace('_', ' ', $appointment->status)),
                $appointment->created_at->format('Y-m-d H:i:s')
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Pet Name',
            'Owner',
            'Doctor',
            'Service',
            'Date',
            'Time',
            'Status',
            'Created At'
        ];
    }

    public function title(): string
    {
        return 'Recent Appointments';
    }
}
