<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiswaOrangTua extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'siswa_orang_tua';
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
}
