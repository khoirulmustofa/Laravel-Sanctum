<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sekolah extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'sekolah';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];



    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
