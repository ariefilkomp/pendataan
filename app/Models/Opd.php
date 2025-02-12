<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Opd extends Model
{
    protected $primaryKey = 'kode';
    protected $keyType = 'string';
    public $incrementing = false;

    public static function getOpds() {
        return Opd::select('kode', 'nama_opd')->where('kode','like','%000000')->get();
    }
}
