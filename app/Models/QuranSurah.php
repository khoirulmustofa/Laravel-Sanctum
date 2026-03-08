<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranSurah extends Model
{
    protected $table = 'quran_surah';

    protected $primaryKey = 'number';

    public $incrementing = false;

    protected $guarded = [];

    // Define relationship with QuranAyat
    public function ayats()
    {
        return $this->hasMany(QuranAyat::class, 'surah', 'number')->orderBy('ayah', 'asc');
    }


    // Define relationship with SetoranHafalan
    public function setoran()
    {
        return $this->hasMany(SetoranHafalan::class, 'surah', 'number');
    }
}
