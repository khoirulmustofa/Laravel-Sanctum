<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    /**
     * Karena kita meng-extend SpatieRole, 
     * konfigurasi table dan guard otomatis terbawa.
     */

    /**
     * Relasi ke User (Customization)
     * Spatie secara default menggunakan morphToMany, 
     * tetapi jika Anda ingin mengambil field tertentu seperti di query Anda:
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('permission.table_names.model_has_roles'),
            'role_id',    // Foreign key di tabel pivot (model_has_roles)
            'model_id'    // Related key di tabel pivot (arah ke users)
        );
    }
}
