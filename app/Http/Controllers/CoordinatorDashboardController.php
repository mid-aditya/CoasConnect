<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use App\Models\PatientLog;
use App\Models\Rotation;
use App\Models\RotationAssignment;
use App\Models\User;
use Illuminate\Http\Request;

class CoordinatorDashboardController extends Controller
{
    public function index()
    {
        $coasUsers = User::role('Koas')->get();
        $coasIds = $coasUsers->pluck('id');

        // Get COAS progress data
        $coasProgress = $coasUsers->map(function ($coas) {
            $approvedLogs = PatientLog::where('user_id', $coas->id)
                ->where('status', 'reviewed')
                ->count();
            $target = 30;
            $progress = min(100, round(($approvedLogs / $target) * 100));

            return [
                'name' => $coas->name,
                'progress' => $progress,
                'approved_logs' => $approvedLogs,
            ];
        });

        // Calculate stats
        $stats = [
            'total_coas' => $coasUsers->count(),
            'active_rotations' => Rotation::count(),
            'avg_progress' => round($coasProgress->avg('progress') ?? 0),
            'pending_reviews' => PatientLog::whereIn('user_id', $coasIds)
                ->where('status', 'submitted')->count(),
        ];

        // Competency summary (placeholder)
        $competencies = [
            'profesional' => 65,
            'keterampilan' => 45,
            'pengetahuan' => 70,
        ];

        return view('coordinator.dashboard', compact('stats', 'coasProgress', 'competencies'));
    }
}
