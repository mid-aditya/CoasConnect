<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'content',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Type constants
    const TYPE_WELCOME = 'welcome';
    const TYPE_REMINDER = 'reminder';
    const TYPE_EDUCATION = 'education';
    const TYPE_EMERGENCY = 'emergency';
    const TYPE_CUSTOM = 'custom';

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
