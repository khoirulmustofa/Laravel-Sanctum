<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Halaqoh extends Model
{
    use HasUuids;
    use SoftDeletes;


    protected $table = 'halaqoh';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    // Define relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Siswa (Many-to-Many)
     */
    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'halaqoh_siswa', 'halaqoh_id', 'siswa_id')
            ->using(HalaqohSiswa::class) // Memberitahu Laravel untuk menggunakan model pivot custom
            ->withPivot('id', 'tahun_ajaran', 'semester', 'aktif')
            ->withTimestamps()
            ->wherePivot('deleted_at', null); // Filter agar data yang di-soft-delete tidak muncul
    }

    /**
     * Contoh Scope untuk memfilter siswa yang AKTIF saja di semester berjalan
     */
    public function siswaAktif()
    {
        return $this->siswas()->wherePivot('aktif', true);
    }

    
    /**
     * Get the associated SetoranHafalans
     *
     * Mengembalikan informasi tentang setoran hafalan yang terdaftar di halaqoh.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function setoranHafalans()
    {
        return $this->hasMany(SetoranHafalan::class, 'halaqoh_id');
    }
}
