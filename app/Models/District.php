<?php

namespace App\Models;

use Database\Factories\DistrictFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'geom'])]
class District extends Model
{
    /** @use HasFactory<DistrictFactory> */
    use HasFactory;

    public $timestamps = false;
}
