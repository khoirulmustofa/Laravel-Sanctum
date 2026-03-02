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



    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function orangTua()
    {
        return $this->hasOne(SiswaOrangTua::class);
    }

    public function alamat()
    {
        return $this->hasOne(\App\Models\SiswaAlamat::class);
    }
}
