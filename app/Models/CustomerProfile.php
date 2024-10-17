<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'user_id',
        'registration_fee',
        'personal_infos',
        'demographic_infos',
        'educational_infos',
        'employment_infos',
        'marital_infos',
        'referees_infos',
        'preferance_infos',
        'religious_infos',
        'is_verified',
        'created_by',
        'status',
    ];
}
