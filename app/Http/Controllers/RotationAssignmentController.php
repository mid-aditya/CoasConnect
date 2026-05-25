<?php

namespace App\Http\Controllers;

use App\Models\RotationAssignment;
use App\Models\Rotation;
use App\Models\User;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class RotationAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = RotationAssignment::with(['koas', 'rotation', 'academicPeriod', 'supervisor'])->latest('start_date')->paginate(15);
        return view('admin.assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $koas = User::whereHas('role', function ($q) {
            $q->where('name', 'Koas');
        })->get();
        $dosens = User::whereHas('role', function ($q) {
            $q->where('name', 'Dosen Pembimbing');
        })->get();
        $rotations = Rotation::all();
        $academicPeriods = AcademicPeriod::where('is_active', true)->get();

        return view('admin.assignments.create', compact('koas', 'dosens', 'rotations', 'academicPeriods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rotation_id' => 'required|exists:rotations,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'supervisor_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        RotationAssignment::create($request->all());

        return redirect()->route('admin.assignments.index')->with('success', 'Penempatan rotasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RotationAssignment $assignment)
    {
        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RotationAssignment $assignment)
    {
        $koas = User::whereHas('role', function ($q) {
            $q->where('name', 'Koas');
        })->get();
        $dosens = User::whereHas('role', function ($q) {
            $q->where('name', 'Dosen Pembimbing');
        })->get();
        $rotations = Rotation::all();
        $academicPeriods = AcademicPeriod::all();

        return view('admin.assignments.edit', compact('assignment', 'koas', 'dosens', 'rotations', 'academicPeriods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RotationAssignment $assignment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rotation_id' => 'required|exists:rotations,id',
            'academic_period_id' => 'required|exists:academic_periods,id',
            'supervisor_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $assignment->update($request->all());

        return redirect()->route('admin.assignments.index')->with('success', 'Penempatan rotasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RotationAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.assignments.index')->with('success', 'Penempatan rotasi dihapus.');
    }
}
