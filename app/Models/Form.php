<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory, HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public static function booted() {
        static::creating(function ($model) {
            $model->id = $model->id ?? Str::uuid();
        });
    }
    
    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function sections(){
        return $this->hasMany(Section::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
