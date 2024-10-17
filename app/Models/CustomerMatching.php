<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMatching extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'user_id',
        'matchingUser',
        'acceptBysender',
        'acceptByreceiver',
        'approveByAdmin',
        'status',
    ];
}
