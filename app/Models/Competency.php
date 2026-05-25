<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'level',
        'target_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'target_count' => 'integer',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Competency::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Competency::class, 'parent_id');
    }

    public function clinicalLogs(): BelongsToMany
    {
        return $this->belongsToMany(ClinicalLog::class, 'clinical_log_competency')
            ->withPivot(['rating', 'feedback'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeDomain($query)
    {
        return $query->where('level', 0);
    }

    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    // Helpers
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    public function getApprovedLogsCount(): int
    {
        return $this->clinicalLogs()
            ->whereHas('evaluations', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();
    }

    public function getProgressPercentage(): float
    {
        if ($this->target_count <= 0) {
            return 100.0;
        }

        $count = $this->getApprovedLogsCount();
        return min(100.0, ($count / $this->target_count) * 100);
    }
}
