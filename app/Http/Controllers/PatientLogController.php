<?php

namespace App\Http\Controllers;

use App\Models\PatientLog;
use App\Models\Patient;
use App\Models\Rotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PatientLogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role->name === 'Koas') {
            $logs = PatientLog::where('user_id', $user->id)->with('patient', 'rotation')->latest('log_date')->paginate(15);
            return view('koas.logs.index', compact('logs'));
        } elseif ($user->role->name === 'Dosen Pembimbing') {
            $koasIds = $user->supervisedAssignments()->pluck('user_id');
            $logs = PatientLog::whereIn('user_id', $koasIds)->with('patient', 'rotation', 'koas')->latest('log_date')->paginate(15);
            return view('dosen.logs.index', compact('logs'));
        }

        abort(403);
    }

    /**
     * Show the form for creating a new log.
     */
    public function create()
    {
        $user = Auth::user();
        $patients = Patient::where('user_id', $user->id)->get();
        // Get active rotation
        $activeAssignment = $user->assignments()->where('end_date', '>=', now())->first();
        if (!$activeAssignment) {
            // Mock assignment for local testing
            $rotation = Rotation::firstOrCreate(['name' => 'Stase Dummy', 'min_procedures' => 5]);
            $academicPeriod = \App\Models\AcademicPeriod::firstOrCreate(
                ['name' => '2026/Ganjil'],
                ['start_date' => now(), 'end_date' => now()->addMonths(2)]
            );
            $dosen = \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'Dosen Pembimbing'); })->first();

            if ($dosen) {
                $user->assignments()->create([
                    'rotation_id' => $rotation->id,
                    'academic_period_id' => $academicPeriod->id,
                    'supervisor_id' => $dosen->id,
                    'start_date' => now(),
                    'end_date' => now()->addWeeks(4)
                ]);
            }
        } else {
            $rotation = $activeAssignment->rotation;
        }

        return view('koas.logs.create', compact('patients', 'rotation'));
    }

    /**
     * Store a newly created log in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'rotation_id' => 'required|exists:rotations,id',
            'log_date' => 'required|date|before_or_equal:today',
            'condition_summary' => 'required|string',
            'key_examination' => 'required|string',
            'treatment_plan' => 'required|string',
            'clinical_reflection' => 'required|string',
            'status' => 'required|in:draft,submitted'
        ]);

        $log = new PatientLog($request->all());
        $log->user_id = Auth::id();
        $log->save();

        return redirect()->route('koas.dashboard')->with('success', 'Logbook berhasil disimpan.');
    }

    /**
     * Display the specified log.
     */
    public function show(PatientLog $patientLog)
    {
        $patientLog->load('patient', 'koas', 'rotation', 'procedureValidations');

        $user = Auth::user();
        if ($user->role->name === 'Koas' && $patientLog->user_id !== $user->id) {
            abort(403);
        }
        if ($user->role->name === 'Dosen Pembimbing' && !$user->supervisedAssignments()->where('user_id', $patientLog->user_id)->exists()) {
            abort(403);
        }

        $view = $user->role->name === 'Koas' ? 'koas.logs.show' : 'dosen.logs.show';
        return view($view, compact('patientLog'));
    }

    /**
     * Show the form for editing the specified log.
     */
    public function edit(PatientLog $patientLog)
    {
        if ($patientLog->user_id !== Auth::id() || in_array($patientLog->status, ['submitted', 'approved'])) {
            abort(403, 'Tidak dapat mengedit log ini.');
        }

        $patients = Patient::where('user_id', Auth::id())->get();
        return view('koas.logs.edit', compact('patientLog', 'patients'));
    }

    /**
     * Update the specified log in storage.
     */
    public function update(Request $request, PatientLog $patientLog)
    {
        $user = Auth::user();

        if ($user->role->name === 'Koas') {
            if ($patientLog->user_id !== $user->id || in_array($patientLog->status, ['submitted', 'approved'])) {
                abort(403);
            }

            $request->validate([
                'condition_summary' => 'required|string',
                'key_examination' => 'required|string',
                'treatment_plan' => 'required|string',
                'clinical_reflection' => 'required|string',
                'status' => 'required|in:draft,submitted'
            ]);

            $patientLog->update($request->only(
                'condition_summary',
                'key_examination',
                'treatment_plan',
                'procedures_performed',
                'clinical_reflection',
                'status'
            ));

            return redirect()->route('logs.show', $patientLog)->with('success', 'Log diperbarui.');
        } elseif ($user->role->name === 'Dosen Pembimbing') {

            $request->validate([
                'status' => 'required|in:approved,revised',
                'supervisor_comment' => 'nullable|string'
            ]);

            $patientLog->update([
                'status' => $request->status,
                'supervisor_comment' => $request->supervisor_comment
            ]);

            return redirect()->route('logs.show', $patientLog)->with('success', 'Review log disimpan.');
        }

        abort(403);
    }
}
