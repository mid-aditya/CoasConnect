<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureValidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_log_id',
        'procedure_id',
        'user_id',
        'involvement_level',
        'status',
        'validated_by',
        'validated_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function patientLog()
    {
        return $this->belongsTo(PatientLog::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function koas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
