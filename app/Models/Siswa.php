<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'siswa';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];



    /**
     * Relasi one-to-one dengan model Sekolah.
     *
     * Mengembalikan informasi tentang sekolah siswa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * Relasi many-to-many dengan model OrangTua.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parents()
    {
        return $this->belongsToMany(OrangTua::class, 'siswa_orang_tua', 'siswa_id', 'orang_tua_id')
            ->using(SiswaOrangTua::class)
            ->withPivot('id', 'hubungan', 'kontak_utama')
            ->withTimestamps()
            ->wherePivot('deleted_at', null);
    }


    /**
     * Mengembalikan relasi one-to-one dengan model SiswaAlamat.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function alamat()
    {
        return $this->hasOne(\App\Models\SiswaAlamat::class);
    }

    /**
     * Relasi ke Halaqoh (Many-to-Many)
     */
    public function halaqohs()
    {
        return $this->belongsToMany(Halaqoh::class, 'halaqoh_siswa', 'siswa_id', 'halaqoh_id')
            ->using(HalaqohSiswa::class) // Wajib panggil model pivot tadi
            ->withPivot('id', 'tahun_ajaran', 'semester', 'aktif')
            ->withTimestamps()
            ->wherePivot('deleted_at', null);
    }

    /**
     * Get the associated SetoranHafalans
     *
     * Mengembalikan informasi tentang setoran hafalan yang terdaftar di siswa.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function setoranHafalans()
    {
        return $this->hasMany(SetoranHafalan::class, 'siswa_id');
    }
}
