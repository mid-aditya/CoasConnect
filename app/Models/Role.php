<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = ["name", "guard_name"];

    /**
     * Get users with this role through Spatie's pivot table.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config("permission.models.user"),
            config("permission.table_names.model_has_roles"),
            "role_id",
            config("permission.column_names.model_morph_key"),
        );
    }
}
