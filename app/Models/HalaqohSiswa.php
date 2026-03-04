<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class HalaqohSiswa extends Pivot // Ubah dari Model ke Pivot
{
    use HasUuids;
    use SoftDeletes;


    protected $table = 'halaqoh_siswa';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    // Define relationship with Halaqoh
    public function halaqoh()
    {
        return $this->belongsTo(Halaqoh::class, 'halaqoh_id');
    }

    // Define relationship with Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
