<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranBookmark extends Model
{
    protected $table = 'quran_bookmarks';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];

    // relation
    public function surah()
    {
        return $this->belongsTo(QuranSurah::class, 'surah_nomor', 'nomor');
    }

    public function ayat()
    {
        return $this->belongsTo(QuranAyat::class, 'ayat_id', 'id');
    }

}
