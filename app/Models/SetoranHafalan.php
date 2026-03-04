<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SetoranHafalan extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'setoran_hafalan';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    /**
     * Get the associated Halaqoh
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function halaqoh()
    {
        return $this->belongsTo(Halaqoh::class, 'halaqoh_id');
    }

    /**
     * Get the associated Siswa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
