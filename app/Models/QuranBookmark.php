<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class QuranBookmark extends Model
{
    use HasUuids;

    protected $table = 'quran_bookmarks';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    // relation
    public function surah()
    {
        return $this->belongsTo(QuranSurah::class, 'surah_nomor', 'number');
    }

    public function ayat()
    {
        return $this->belongsTo(QuranAyat::class, 'ayat_id', 'id');
    }
}
