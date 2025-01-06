<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

use Illuminate\Support\Str;

class Role extends SpatieRole
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
