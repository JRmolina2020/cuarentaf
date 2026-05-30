<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CreditNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'reference_code',
        'status',
        'qr',
        'cude',
        'validated',
        'gross_value',
        'taxable_amount',
        'tax_amount',
        'discount_amount',
        'surcharge_amount',
        'total',
        'observation'
    ];
}
