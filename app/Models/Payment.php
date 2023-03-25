<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Payment extends Model
{
    use HasFactory, Uuid;
    protected $hidden = [];
    protected $guarded = [
        'id',
    ];

    protected $casts = [];

    protected $guard_name = 'api';
}
