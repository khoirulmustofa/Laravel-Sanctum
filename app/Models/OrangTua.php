<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrangTua extends Model
{

    use SoftDeletes, HasUuids;

    protected $table = 'orang_tua';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    /**
     * Get the associated User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi many-to-many dengan model Siswa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'siswa_orang_tua', 'orang_tua_id', 'siswa_id')
            ->using(SiswaOrangTua::class)
            ->withPivot('id', 'hubungan', 'kontak_utama')
            ->withTimestamps()
            ->wherePivot('deleted_at', null);
    }
}
