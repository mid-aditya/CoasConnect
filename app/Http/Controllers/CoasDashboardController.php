<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use Illuminate\Http\Request;

class CoasDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get active patients assigned to this COAS
        $patients = Patient::whereHas('assignments', function ($query) use ($user) {
            $query->where('coas_id', $user->id)
                ->whereIn('status', ['pending', 'active']);
        })->with('activeAssignment.coas')->get();

        // Get recent logs
        $recentLogs = PatientLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate stats
        $stats = [
            'active_patients' => $patients->count(),
            'submitted_logs' => PatientLog::where('user_id', $user->id)
                ->where('status', 'submitted')->count(),
            'approved_logs' => PatientLog::where('user_id', $user->id)
                ->where('status', 'reviewed')->count(),
            'progress' => $this->calculateProgress($user),
        ];

        return view('koas.dashboard', compact('stats', 'patients', 'recentLogs'));
    }

    private function calculateProgress($user)
    {
        // Simple progress calculation based on approved logs vs target
        $totalLogs = PatientLog::where('user_id', $user->id)
            ->where('status', 'reviewed')
            ->count();

        $target = 30; // Target number of approved logs
        $progress = min(100, ($totalLogs / $target) * 100);

        return round($progress);
    }
}
