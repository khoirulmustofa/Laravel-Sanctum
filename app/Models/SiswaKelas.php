<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaKelas extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'siswa_kelas';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];


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
