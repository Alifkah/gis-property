<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketDemand extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'latitude',
        'longitude',
        'created_at',
    ];
}
