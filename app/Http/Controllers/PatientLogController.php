<?php

namespace App\Http\Controllers;

use App\Models\PatientLog;
use App\Models\Patient;
use App\Models\Rotation;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientLogController extends Controller
{
    /**
     * Display a listing of the logs.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isCoas()) {
            $logs = PatientLog::where("user_id", $user->id)
                ->with("patient", "rotation")
                ->latest("log_date")
                ->paginate(15);
            return view("koas.logs.index", compact("logs"));
        } elseif ($user->isDoctor()) {
            $koasIds = $user->supervisedAssignments()->pluck("user_id");
            $logs = PatientLog::whereIn("user_id", $koasIds)
                ->with("patient", "rotation", "koas")
                ->latest("log_date")
                ->paginate(15);
            return view("dosen.logs.index", compact("logs"));
        }

        abort(403);
    }

    /**
     * Show the form for creating a new log.
     */
    public function create()
    {
        $user = Auth::user();

        // Get patient's patients (for COAS)
        $patients = Patient::where("user_id", $user->id)->get();

        // Get active rotation
        $activeAssignment = $user
            ->assignments()
            ->where("end_date", ">=", now())
            ->first();

        if (!$activeAssignment) {
            // Create dummy rotation for testing
            $rotation = Rotation::firstOrCreate(
                ["name" => "Stase Umum"],
                ["min_procedures" => 5],
            );
        } else {
            $rotation = $activeAssignment->rotation;
        }

        return view("koas.logs.create", compact("patients", "rotation"));
    }

    /**
     * Store a newly created log in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "patient_id" => "required|exists:patients,id",
            "rotation_id" => "required|exists:rotations,id",
            "log_date" => "required|date|before_or_equal:today",
            "condition_summary" => "required|string",
            "key_examination" => "required|string",
            "treatment_plan" => "required|string",
            "procedures_performed" => "nullable|string",
            "clinical_reflection" => "required|string",
            "status" => "required|in:draft,submitted",
        ]);

        // Verify patient belongs to this user
        $patient = Patient::find($request->patient_id);
        if ($patient->user_id !== Auth::id()) {
            abort(403, "Pasien tidak valid.");
        }

        PatientLog::create([
            "patient_id" => $request->patient_id,
            "user_id" => Auth::id(),
            "rotation_id" => $request->rotation_id,
            "log_date" => $request->log_date,
            "condition_summary" => $request->condition_summary,
            "key_examination" => $request->key_examination,
            "treatment_plan" => $request->treatment_plan,
            "procedures_performed" => $request->procedures_performed,
            "clinical_reflection" => $request->clinical_reflection,
            "status" => $request->status,
        ]);

        return redirect()
            ->route("logs.index")
            ->with("success", "Logbook berhasil disimpan.");
    }

    /**
     * Display the specified log.
     */
    public function show(PatientLog $log)
    {
        $log->load("patient", "user", "rotation", "procedureValidations");

        $user = Auth::user();

        if ($user->isCoas() && $log->user_id !== $user->id) {
            abort(403);
        }

        if (
            $user->isDoctor() &&
            !$user
                ->supervisedAssignments()
                ->where("user_id", $log->user_id)
                ->exists()
        ) {
            abort(403);
        }

        if ($user->isCoas()) {
            return view("koas.logs.show", compact("log"));
        }

        return view("dosen.logs.show", compact("log"));
    }

    /**
     * Show the form for editing the specified log.
     */
    public function edit(PatientLog $log)
    {
        if (
            $log->user_id !== Auth::id() ||
            in_array($log->status, ["submitted", "reviewed", "approved"])
        ) {
            abort(403, "Tidak dapat mengedit log ini.");
        }

        $patients = Patient::where("user_id", Auth::id())->get();
        return view("koas.logs.edit", compact("log", "patients"));
    }

    /**
     * Update the specified log in storage.
     */
    public function update(Request $request, PatientLog $log)
    {
        $user = Auth::user();

        if ($user->isCoas()) {
            if (
                $log->user_id !== $user->id ||
                in_array($log->status, ["submitted", "reviewed", "approved"])
            ) {
                abort(403);
            }

            $request->validate([
                "condition_summary" => "required|string",
                "key_examination" => "required|string",
                "treatment_plan" => "required|string",
                "procedures_performed" => "nullable|string",
                "clinical_reflection" => "required|string",
                "status" => "required|in:draft,submitted",
            ]);

            $log->update(
                $request->only([
                    "condition_summary",
                    "key_examination",
                    "treatment_plan",
                    "procedures_performed",
                    "clinical_reflection",
                    "status",
                ]),
            );

            return redirect()
                ->route("logs.show", $log)
                ->with("success", "Log diperbarui.");
        }

        abort(403);
    }

    /**
     * Remove the specified log from storage.
     */
    public function destroy(PatientLog $log)
    {
        if (
            $log->user_id !== Auth::id() ||
            in_array($log->status, ["submitted", "reviewed", "approved"])
        ) {
            abort(403, "Tidak dapat menghapus log ini.");
        }

        $log->delete();
        return redirect()->route("logs.index")->with("success", "Log dihapus.");
    }
}
