<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Veterinary Clinic Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .stats-cell h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #333;
        }
        .stats-cell .number {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h2 {
            background-color: #2563eb;
            color: white;
            padding: 10px;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Veterinary Clinic Management System</h1>
        <p>Comprehensive Report - {{ date('F d, Y') }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <h2>Summary Statistics</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <h3>Total Pets</h3>
                    <div class="number">{{ $stats['total_pets'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Total Pet Owners</h3>
                    <div class="number">{{ $stats['total_owners'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Total Doctors</h3>
                    <div class="number">{{ $stats['total_doctors'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Total Appointments</h3>
                    <div class="number">{{ $stats['total_appointments'] }}</div>
                </div>
            </div>
            <div class="stats-row">
                <div class="stats-cell">
                    <h3>Total Services</h3>
                    <div class="number">{{ $stats['total_services'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Medical Records</h3>
                    <div class="number">{{ $stats['total_medical_records'] }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Total Revenue</h3>
                    <div class="number">${{ number_format($revenue_by_service->sum('total_revenue'), 2) }}</div>
                </div>
                <div class="stats-cell">
                    <h3>Report Date</h3>
                    <div class="number">{{ date('M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Service -->
    <div class="section">
        <h2>Revenue by Service</h2>
        <table>
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenue_by_service as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>${{ number_format($service->total_revenue, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No revenue data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Appointments by Status -->
    <div class="section">
        <h2>Appointments by Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments_by_status as $status)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $status->status)) }}</td>
                    <td>{{ $status->count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No appointment data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pets by Species -->
    <div class="section">
        <h2>Pets by Species</h2>
        <table>
            <thead>
                <tr>
                    <th>Species</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pets_by_species as $species)
                <tr>
                    <td>{{ ucfirst($species->species) }}</td>
                    <td>{{ $species->count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No pet data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Recent Appointments -->
    <div class="section">
        <h2>Recent Appointments</h2>
        <table>
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Doctor</th>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_appointments as $appointment)
                <tr>
                    <td>{{ $appointment->pet->name }}</td>
                    <td>{{ $appointment->pet->owner->user->name }}</td>
                    <td>{{ $appointment->doctor->user->name }}</td>
                    <td>{{ $appointment->service->name }}</td>
                    <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                    <td>{{ $appointment->appointment_time }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No recent appointments</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ date('F d, Y \a\t H:i:s') }} | Veterinary Clinic Management System</p>
    </div>
</body>
</html>
