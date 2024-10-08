<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $dates = ['expired_at'];

    protected $guarded = [];

    protected $fillable = [
        'email',
        'token',
        'expired_at',
    ];
}
