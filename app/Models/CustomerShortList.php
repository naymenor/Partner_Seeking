<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerShortList extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'user_id',
        'shortlistedUsers',
        'status',
    ];
}
