<?php

namespace App\Http\Controllers;

use App\Models\PatientLog;
use App\Models\RotationAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get supervised COAS
        $coasAssignments = RotationAssignment::where('supervisor_id', $user->id)
            ->with('user')
            ->get();

        $coasIds = $coasAssignments->pluck('user_id')->filter();

        // Get pending reviews
        $pendingLogs = PatientLog::whereIn('user_id', $coasIds)
            ->where('status', 'submitted')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Get COAS list
        $coasList = User::whereIn('id', $coasIds)->get();

        // Calculate stats
        $stats = [
            'total_coas' => $coasIds->count(),
            'pending_reviews' => PatientLog::whereIn('user_id', $coasIds)
                ->where('status', 'submitted')->count(),
            'reviewed_this_week' => PatientLog::whereIn('user_id', $coasIds)
                ->where('status', 'reviewed')
                ->where('updated_at', '>=', now()->startOfWeek())
                ->count(),
            'active_assignments' => RotationAssignment::where('supervisor_id', $user->id)->count(),
        ];

        return view('dosen.dashboard', compact('stats', 'pendingLogs', 'coasList'));
    }
}
