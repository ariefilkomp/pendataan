<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

class File extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'files';
    protected $fillable = ['id', 'user_id', 'form_id', 'question_id', 'answer_id', 'path', 'name', 'extension', 'mime_type', 'size'];

    public static function booted() {
        static::creating(function ($model) {
            $model->id = $model->id ?? Str::uuid();
        });
    }
}
