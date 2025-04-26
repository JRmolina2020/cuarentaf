<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'date_facture',
        'subtot',
        'disc',
        'tot',
        'efecty',
        'other',
        'status',
        'type_sale',
        'user_id',
        'cufe',
        'numbering_range_id',
        'phonef'
    ];
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
    function facture_details()
    {
        return $this->hasMany('App\Model\FactureDetail');
    }
}
