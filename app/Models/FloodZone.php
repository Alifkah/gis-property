<?php

namespace App\Models;

use Database\Factories\FloodZoneFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['area_name', 'risk_level', 'geom'])]
class FloodZone extends Model
{
    /** @use HasFactory<FloodZoneFactory> */
    use HasFactory;

    public $timestamps = false;
}
