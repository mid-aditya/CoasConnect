<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClinicalLog;
use App\Models\Competency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClinicalLogController extends Controller
{
    /**
     * Display a listing of clinical logs.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = ClinicalLog::with(['assignment.patient', 'competencies', 'evaluations']);

        // Filter by role
        if ($user->isCoas()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isDoctor()) {
            $query->whereHas('assignment', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('activity_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('activity_date', '<=', $request->to_date);
        }

        $logs = $query->orderBy('activity_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($logs);
    }

    /**
     * Store a newly created clinical log.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', ClinicalLog::class);

        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'rotation_id' => 'required|exists:rotations,id',
            'activity_date' => 'required|date|before_or_equal:today',
            'activity_type' => 'required|in:anamnesis,physical_exam,procedure,education,consultation,other',
            'description' => 'required|string|min:50',
            'patient_condition' => 'required|in:stable,improving,worsening,critical',
            'reflection' => 'nullable|string|min:20',
            'competency_ids' => 'required|array|min:1',
            'competency_ids.*' => 'exists:competencies,id',
        ]);

        // Verify COAS has active assignment to this patient
        $user = $request->user();
        $assignment = \App\Models\Assignment::findOrFail($validated['assignment_id']);

        if ($assignment->coas_id !== $user->id || !$assignment->isActive()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $log = ClinicalLog::create([
            'assignment_id' => $validated['assignment_id'],
            'rotation_id' => $validated['rotation_id'],
            'user_id' => $user->id,
            'activity_date' => $validated['activity_date'],
            'activity_type' => $validated['activity_type'],
            'description' => $validated['description'],
            'patient_condition' => $validated['patient_condition'],
            'reflection' => $validated['reflection'] ?? null,
            'status' => ClinicalLog::STATUS_DRAFT,
        ]);

        // Attach competencies
        $log->competencies()->attach($validated['competency_ids']);

        return response()->json([
            'message' => 'Clinical log created successfully',
            'log' => $log->load('competencies'),
        ], 201);
    }

    /**
     * Display the specified clinical log.
     */
    public function show(Request $request, ClinicalLog $clinicalLog): JsonResponse
    {
        Gate::authorize('view', $clinicalLog);

        $clinicalLog->load([
            'assignment.patient',
            'assignment.coas',
            'assignment.doctor',
            'competencies',
            'evaluations.evaluator',
            'attachments',
        ]);

        return response()->json($clinicalLog);
    }

    /**
     * Update the specified clinical log.
     */
    public function update(Request $request, ClinicalLog $clinicalLog): JsonResponse
    {
        Gate::authorize('update', $clinicalLog);

        $validated = $request->validate([
            'activity_date' => 'sometimes|date|before_or_equal:today',
            'activity_type' => 'sometimes|in:anamnesis,physical_exam,procedure,education,consultation,other',
            'description' => 'sometimes|string|min:50',
            'patient_condition' => 'sometimes|in:stable,improving,worsening,critical',
            'reflection' => 'nullable|string|min:20',
            'competency_ids' => 'sometimes|array|min:1',
            'competency_ids.*' => 'exists:competencies,id',
        ]);

        $clinicalLog->update($validated);

        if (isset($validated['competency_ids'])) {
            $clinicalLog->competencies()->sync($validated['competency_ids']);
        }

        return response()->json([
            'message' => 'Clinical log updated successfully',
            'log' => $clinicalLog->load('competencies'),
        ]);
    }

    /**
     * Submit clinical log for review.
     */
    public function submit(Request $request, ClinicalLog $clinicalLog): JsonResponse
    {
        Gate::authorize('submit', $clinicalLog);

        $clinicalLog->submit();

        return response()->json([
            'message' => 'Clinical log submitted for review',
            'log' => $clinicalLog->load('competencies'),
        ]);
    }

    /**
     * Remove the specified clinical log.
     */
    public function destroy(Request $request, ClinicalLog $clinicalLog): JsonResponse
    {
        Gate::authorize('delete', $clinicalLog);

        $clinicalLog->delete();

        return response()->json([
            'message' => 'Clinical log deleted successfully',
        ]);
    }
}
