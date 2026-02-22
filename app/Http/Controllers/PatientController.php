<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $patients = Patient::where('user_id', $user->id)->latest()->paginate(15);
        return view('koas.patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('koas.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'initials' => 'required|string|max:10',
            'age_category' => 'required|string',
            'gender' => 'required|in:L,P',
            'working_diagnosis' => 'required|string',
            'care_context' => 'required|string',
        ]);

        $patient = new Patient($request->all());
        $patient->user_id = Auth::id();
        $patient->save();

        return redirect()->route('koas.dashboard')->with('success', 'Pasien berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        if ($patient->user_id !== Auth::id()) {
            abort(403);
        }
        $logs = $patient->logs()->latest('log_date')->get();
        return view('koas.patients.show', compact('patient', 'logs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        if ($patient->user_id !== Auth::id()) {
            abort(403);
        }
        return view('koas.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        if ($patient->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'initials' => 'required|string|max:10',
            'age_category' => 'required|string',
            'gender' => 'required|in:L,P',
            'working_diagnosis' => 'required|string',
            'care_context' => 'required|string',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Data pasien diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        if ($patient->user_id !== Auth::id()) {
            abort(403);
        }

        // Prevent deletion if logs exist
        if ($patient->logs()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus pasien yang memiliki log.');
        }

        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Pasien dihapus.');
    }
}
