<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable = ['rotation_id', 'name', 'competency_level'];

    public function rotation()
    {
        return $this->belongsTo(Rotation::class);
    }
}
