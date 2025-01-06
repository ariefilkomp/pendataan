<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

use Illuminate\Support\Str;

class Permission extends SpatiePermission
{
    use HasFactory;
    use HasUuids;
    protected $primaryKey = 'uuid';

    public static function booted() {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
}
