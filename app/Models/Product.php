<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'price',
        'price_two',
        'cost',
        'type_iva',
        'categorie_id',
        'priceSv',
        'price_twoSv',
        'stock',
        'vm',
        'vd',
    ];
    public function facture_detail()
    {
        return $this->belongsTo('App\Models\FactureDetail');
    }
}
