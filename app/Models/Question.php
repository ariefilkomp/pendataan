<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Question extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    public static function booted() {
        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->column_name = strtolower(Str::random(10));
        });
    }
}
