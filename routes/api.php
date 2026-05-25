<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\ClinicalLogController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| CoasConnect API endpoints
|
*/

// WhatsApp Webhook (no auth required)
Route::prefix('webhooks')->group(function () {
    Route::get('/whatsapp', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/whatsapp', [WhatsAppWebhookController::class, 'webhook']);
});

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/sessions', [AuthController::class, 'sessions']);
    });

    // Assignments
    Route::apiResource('assignments', AssignmentController::class);
    Route::get('/assignments/available-coas', [AssignmentController::class, 'availableCoas']);

    // Clinical Logs
    Route::apiResource('clinical-logs', ClinicalLogController::class);
    Route::post('/clinical-logs/{clinicalLog}/submit', [ClinicalLogController::class, 'submit']);

    // Patients (filtered by role)
    Route::get('/patients', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $query = \App\Models\Patient::query();

        if ($user->isCoas()) {
            // COAS can only see assigned patients
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('coas_id', $user->id)
                  ->whereIn('status', ['pending', 'active']);
            });
        } elseif ($user->isDoctor()) {
            // Doctor can see patients they supervise
            $query->whereHas('assignments', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        }

        // Admin/coordinator can see all
        $patients = $query->with('activeAssignment.coas')
            ->paginate($request->get('per_page', 15));

        return response()->json($patients);
    });

    // Progress
    Route::get('/progress', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        if (!$user->isCoas()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $calculator = new \App\Services\Curriculum\ProgressCalculator();
        $progress = $calculator->calculateUserProgress($user);

        return response()->json($progress);
    });

    // Pending reviews (for doctors)
    Route::get('/reviews/pending', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        if (!$user->isDoctor()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logs = \App\Models\ClinicalLog::with(['user', 'assignment.patient', 'competencies'])
            ->whereHas('assignment', function ($q) use ($user) {
                $q->where('doctor_id', $user->id);
            })
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'asc')
            ->paginate($request->get('per_page', 15));

        return response()->json($logs);
    });

    // Evaluations
    Route::post('/evaluations', function (\Illuminate\Http\Request $request) {
        $user = $request->user();

        if (!$user->isDoctor()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'clinical_log_id' => 'required|exists:clinical_logs,id',
            'status' => 'required|in:approved,revision_requested,rejected',
            'feedback' => 'required|string',
            'ratings' => 'nullable|array',
            'ratings.*' => 'integer|min:1|max:5',
        ]);

        $log = \App\Models\ClinicalLog::findOrFail($validated['clinical_log_id']);

        // Verify doctor supervises this COAS
        if ($log->assignment->doctor_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $evaluation = \App\Models\Evaluation::create([
            'clinical_log_id' => $validated['clinical_log_id'],
            'evaluator_id' => $user->id,
            'status' => $validated['status'],
            'feedback' => $validated['feedback'],
            'ratings' => $validated['ratings'] ?? null,
            'evaluated_at' => now(),
        ]);

        // Update log status
        if ($validated['status'] === 'approved') {
            $log->update(['status' => 'reviewed']);
        } elseif ($validated['status'] === 'revision_requested') {
            $log->update(['status' => 'revision_requested']);
        } else {
            $log->update(['status' => 'rejected']);
        }

        return response()->json([
            'message' => 'Evaluation submitted successfully',
            'evaluation' => $evaluation,
        ], 201);
    });
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => config('app.version', '1.0.0'),
    ]);
});
