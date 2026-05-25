<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Assignment::with(['patient', 'coas', 'doctor', 'rotation']);

        // Filter by role
        if ($user->isCoas()) {
            $query->where('coas_id', $user->id);
        } elseif ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($assignments);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('create', Assignment::class);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'coas_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'rotation_id' => 'required|exists:rotations,id',
            'notes' => 'nullable|string',
        ]);

        // Check if COAS already has an active assignment for this patient
        $existingAssignment = Assignment::where('patient_id', $validated['patient_id'])
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'message' => 'Patient already has an active assignment',
            ], 422);
        }

        // Check COAS quota
        $coas = User::find($validated['coas_id']);
        $activeAssignments = Assignment::where('coas_id', $coas->id)
            ->where('status', 'active')
            ->count();

        // Get COAS profile for quota
        $profile = $coas->userProfile;
        $quota = $profile?->supervision_quota ?? 5;

        if ($activeAssignments >= $quota) {
            return response()->json([
                'message' => 'COAS has reached maximum assignment quota',
            ], 422);
        }

        $assignment = Assignment::create([
            'patient_id' => $validated['patient_id'],
            'coas_id' => $validated['coas_id'],
            'doctor_id' => $validated['doctor_id'],
            'rotation_id' => $validated['rotation_id'],
            'status' => Assignment::STATUS_PENDING,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'Assignment created successfully',
            'assignment' => $assignment->load(['patient', 'coas', 'doctor', 'rotation']),
        ], 201);
    }

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment): JsonResponse
    {
        Gate::authorize('view', $assignment);

        $assignment->load(['patient', 'coas', 'doctor', 'rotation', 'clinicalLogs', 'whatsappMessages']);

        return response()->json($assignment);
    }

    /**
     * Update assignment status.
     */
    public function update(Request $request, Assignment $assignment): JsonResponse
    {
        Gate::authorize('update', $assignment);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,active,completed,transferred,terminated',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            if ($validated['status'] === 'active') {
                $assignment->markAsActive();
            } elseif ($validated['status'] === 'completed') {
                $assignment->markAsCompleted();
            } elseif ($validated['status'] === 'terminated') {
                $assignment->markAsTerminated($validated['notes'] ?? null);
            } else {
                $assignment->update(['status' => $validated['status']]);
            }
        }

        if (isset($validated['notes'])) {
            $assignment->update(['notes' => $validated['notes']]);
        }

        return response()->json([
            'message' => 'Assignment updated successfully',
            'assignment' => $assignment->load(['patient', 'coas', 'doctor']),
        ]);
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(Assignment $assignment): JsonResponse
    {
        Gate::authorize('delete', $assignment);

        $assignment->delete();

        return response()->json([
            'message' => 'Assignment deleted successfully',
        ]);
    }

    /**
     * Get available COAS for assignment.
     */
    public function availableCoas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rotation_id' => 'nullable|exists:rotations,id',
        ]);

        $coasUsers = User::role('Koas')
            ->with('userProfile')
            ->get()
            ->filter(function ($coas) {
                // Filter COAS who haven't reached quota
                $activeCount = Assignment::where('coas_id', $coas->id)
                    ->where('status', 'active')
                    ->count();
                $quota = $coas->userProfile?->supervision_quota ?? 5;
                return $activeCount < $quota;
            })
            ->map(function ($coas) {
                $activeCount = Assignment::where('coas_id', $coas->id)
                    ->where('status', 'active')
                    ->count();
                $quota = $coas->userProfile?->supervision_quota ?? 5;

                return [
                    'id' => $coas->id,
                    'name' => $coas->name,
                    'email' => $coas->email,
                    'active_assignments' => $activeCount,
                    'quota' => $quota,
                    'available_slots' => $quota - $activeCount,
                ];
            });

        return response()->json($coasUsers->values());
    }
}
