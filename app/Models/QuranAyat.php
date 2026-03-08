<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranAyat extends Model
{

    protected $table = 'quran_ayat';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $guarded = [];

    // Define relationship with QuranSurah
    public function surah()
    {
        return $this->belongsTo(QuranSurah::class, 'surah', 'number');
    }
}
