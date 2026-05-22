<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'type',
    'title',
    'description',
    'price',
    'land_area',
    'building_area',
    'bedroom',
    'bathroom',
    'status',
    'geom',
])]
class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('order');
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'land_area' => 'integer',
            'building_area' => 'integer',
            'bedroom' => 'integer',
            'bathroom' => 'integer',
        ];
    }
}
