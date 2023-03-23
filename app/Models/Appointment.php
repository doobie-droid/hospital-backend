<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Appointment extends Model
{
    use HasFactory, SoftDeletes, Uuid;
    protected $hidden = [
        'payment_id',
        'status',
    ];
    protected $guarded = [
        'id',
    ];

    protected $casts = [];

    protected $guard_name = 'api';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
