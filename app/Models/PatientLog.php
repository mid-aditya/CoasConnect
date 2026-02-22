<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'rotation_id',
        'log_date',
        'condition_summary',
        'key_examination',
        'treatment_plan',
        'procedures_performed',
        'clinical_reflection',
        'status',
        'supervisor_comment'
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function koas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rotation()
    {
        return $this->belongsTo(Rotation::class);
    }

    public function procedureValidations()
    {
        return $this->hasMany(ProcedureValidation::class);
    }
}
