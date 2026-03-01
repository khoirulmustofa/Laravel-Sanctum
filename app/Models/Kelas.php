<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasUuids;
    use SoftDeletes;
    protected $table = 'kelas';
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

    public function siswa_kelas()
    {
        return $this->hasMany(SiswaKelas::class);
    }
}
