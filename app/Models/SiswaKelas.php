<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaKelas extends Model
{
    protected $table = 'siswa_kelas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
