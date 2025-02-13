<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RejectMessage extends Model
{
    use HasUuids;
    protected $fillable = [
        'form_id',
        'user_id',
        'message',
    ];
}
