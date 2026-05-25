<?php

namespace App\Services\Curriculum;

use App\Models\Competency;
use App\Models\ClinicalLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProgressCalculator
{
    /**
     * Calculate progress for a user across all competencies.
     */
    public function calculateUserProgress(User $user): array
    {
        $competencies = Competency::active()
            ->root()
            ->with(['children' => function ($query) {
                $query->active();
            }])
            ->get();

        $progress = [];

        foreach ($competencies as $domain) {
            $domainProgress = $this->calculateDomainProgress($user, $domain);
            $progress['domains'][] = $domainProgress;

            $progress['overall'] += $domainProgress['progress_percentage'];
            $progress['total_competencies'] += $domainProgress['competency_count'];
        }

        if (!empty($progress['domains'])) {
            $progress['overall'] /= count($progress['domains']);
        }

        return $progress;
    }

    /**
     * Calculate progress for a specific domain.
     */
    public function calculateDomainProgress(User $user, Competency $domain): array
    {
        $children = $domain->children()->active()->get();
        $totalProgress = 0;
        $completedCount = 0;

        foreach ($children as $competency) {
            $competencyProgress = $this->calculateCompetencyProgress($user, $competency);
            $totalProgress += $competencyProgress['progress_percentage'];

            if ($competencyProgress['progress_percentage'] >= 100) {
                $completedCount++;
            }
        }

        $competencyCount = $children->count();
        $averageProgress = $competencyCount > 0 ? $totalProgress / $competencyCount : 0;

        return [
            'domain' => [
                'id' => $domain->id,
                'name' => $domain->name,
                'code' => $domain->code,
            ],
            'competency_count' => $competencyCount,
            'completed_count' => $completedCount,
            'progress_percentage' => $averageProgress,
            'competencies' => $children->map(function ($competency) use ($user) {
                return $this->calculateCompetencyProgress($user, $competency);
            }),
        ];
    }

    /**
     * Calculate progress for a specific competency.
     */
    public function calculateCompetencyProgress(User $user, Competency $competency): array
    {
        $approvedLogs = $this->getApprovedLogsForCompetency($user, $competency);
        $achievedCount = $approvedLogs->count();
        $targetCount = $competency->target_count > 0 ? $competency->target_count : 3;
        $progressPercentage = min(100, ($achievedCount / $targetCount) * 100);

        return [
            'competency' => [
                'id' => $competency->id,
                'name' => $competency->name,
                'code' => $competency->code,
                'target' => $targetCount,
            ],
            'achieved_count' => $achievedCount,
            'target_count' => $targetCount,
            'progress_percentage' => $progressPercentage,
            'status' => $this->getProgressStatus($progressPercentage),
            'recent_logs' => $approvedLogs->take(5)->map(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => $log->activity_date->format('Y-m-d'),
                    'type' => $log->activity_type,
                ];
            }),
        ];
    }

    /**
     * Get approved logs that include this competency.
     */
    private function getApprovedLogsForCompetency(User $user, Competency $competency)
    {
        return ClinicalLog::where('user_id', $user->id)
            ->whereHas('competencies', function ($query) use ($competency) {
                $query->where('competencies.id', $competency->id);
            })
            ->whereHas('evaluations', function ($query) {
                $query->where('status', 'approved');
            })
            ->orderBy('activity_date', 'desc');
    }

    /**
     * Get progress status based on percentage.
     */
    private function getProgressStatus(float $percentage): string
    {
        if ($percentage >= 80) {
            return 'excellent';
        }
        if ($percentage >= 50) {
            return 'good';
        }
        if ($percentage >= 25) {
            return 'progressing';
        }
        return 'needs_attention';
    }

    /**
     * Calculate weekly/monthly summary for a user.
     */
    public function getPeriodicSummary(User $user, string $period = 'week'): array
    {
        $startDate = $period === 'week'
            ? now()->startOfWeek()
            : now()->startOfMonth();

        $logs = ClinicalLog::where('user_id', $user->id)
            ->where('activity_date', '>=', $startDate)
            ->where('status', '!=', 'draft')
            ->get();

        return [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'total_logs' => $logs->count(),
            'submitted_logs' => $logs->where('status', 'submitted')->count(),
            'approved_logs' => $logs->where('status', 'reviewed')->count(),
            'logs_by_type' => $logs->groupBy('activity_type')->map->count(),
            'competencies_practiced' => $logs->pluck('competencies')
                ->flatten()
                ->unique('id')
                ->count(),
        ];
    }

    /**
     * Get ranking among peers.
     */
    public function getPeerRanking(User $user, ?int $rotationId = null): array
    {
        $coasUsers = User::role('Koas')
            ->where('id', '!=', $user->id)
            ->get();

        $rankings = [];

        foreach ($coasUsers as $coas) {
            $progress = $this->calculateUserProgress($coas);
            $rankings[] = [
                'user_id' => $coas->id,
                'name' => $coas->name,
                'overall_progress' => $progress['overall'],
            ];
        }

        // Add current user
        $userProgress = $this->calculateUserProgress($user);
        $rankings[] = [
            'user_id' => $user->id,
            'name' => $user->name,
            'overall_progress' => $userProgress['overall'],
        ];

        // Sort by progress
        usort($rankings, fn($a, $b) => $b['overall_progress'] <=> $a['overall_progress']);

        // Find user's rank
        $userRank = null;
        foreach ($rankings as $index => $ranking) {
            if ($ranking['user_id'] === $user->id) {
                $userRank = $index + 1;
                break;
            }
        }

        return [
            'user_rank' => $userRank,
            'total_peers' => count($rankings),
            'rankings' => $rankings,
        ];
    }
}
