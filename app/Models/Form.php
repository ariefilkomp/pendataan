<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public static function booted() {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
