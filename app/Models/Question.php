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

    public function form(){
        return $this->belongsTo(Form::class);
    }

    public function section(){
        return $this->belongsTo(Section::class);
    }

    public function answers(){
        return $this->hasMany(Answer::class);
    }
    
    public static function booted() {
        static::creating(function ($model) {
            $model->id = $model->id ?? Str::uuid();
            $model->column_name = $model->column_name ?? 'col_'.strtolower(Str::random(10));
        });
    }
}
