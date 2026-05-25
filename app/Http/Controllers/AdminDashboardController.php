<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Calculate stats
        $stats = [
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_coas' => User::role('Koas')->count(),
            'active_assignments' => Assignment::where('status', 'active')->count(),
            'total_logs' => PatientLog::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
