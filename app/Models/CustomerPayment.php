<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'user_id',
        'payment_type',
        'payment_for',
        'payment_amount',
        'payment_reference',
        'payment_date',
        'status',
    ];
}
