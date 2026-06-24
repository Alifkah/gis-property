<?php

namespace App\Models;

use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

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
    'slug',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, Searchable;

    protected static function booted()
    {
        static::creating(function (Property $property) {
            if (empty($property->slug)) {
                $property->slug = static::generateUniqueSlug($property->title);
            }
        });

        static::updating(function (Property $property) {
            if ($property->isDirty('title')) {
                $property->slug = static::generateUniqueSlug($property->title, $property->id);
            }
        });
    }

    public static function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where($field ?? 'slug', $value)->firstOrFail();
    }

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

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            'price' => (float) $this->price,
            'land_area' => (int) $this->land_area,
            'building_area' => (int) $this->building_area,
            'bedroom' => (int) $this->bedroom,
            'bathroom' => (int) $this->bathroom,
            'status' => $this->status,
        ];
    }
}
