<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rotation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'min_procedures'];

    public function assignments()
    {
        return $this->hasMany(RotationAssignment::class);
    }

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }
}
