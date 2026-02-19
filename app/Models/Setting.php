<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'key';
    protected $fillable = ['key', 'value'];
    public $incrementing = false;
    protected $keyType = 'string';
}
