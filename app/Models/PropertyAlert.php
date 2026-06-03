<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PropertyAlert extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'min_price',
        'max_price',
        'district_name',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function checkAndNotify(Property $property): void
    {
        $isPgsql = DB::getDriverName() === 'pgsql';
        $districtName = null;

        if ($isPgsql) {
            $districtName = DB::table('districts')
                ->whereRaw('ST_Contains(districts.geom, (select geom from properties where id = ?))', [$property->id])
                ->value('name');
        } else {
            if (preg_match('/POINT\(([-0-9.]+) ([-0-9.]+)\)/', $property->geom ?? '', $matches) === 1) {
                $lng = (float) $matches[1];
                $lat = (float) $matches[2];

                // Fallback district logic
                if ($lng >= 117.05 && $lng < 117.15) {
                    if ($lat >= -0.58 && $lat < -0.548) {
                        $districtName = 'Loa Janan Ilir';
                    } elseif ($lat >= -0.548 && $lat < -0.516) {
                        $districtName = 'Samarinda Seberang';
                    } elseif ($lat >= -0.516 && $lat < -0.484) {
                        $districtName = 'Sungai Kunjang';
                    } elseif ($lat >= -0.484 && $lat < -0.452) {
                        $districtName = 'Samarinda Ilir';
                    } else {
                        $districtName = 'Samarinda Utara';
                    }
                } else {
                    $districtName = 'Samarinda Ulu';
                }
            }
        }

        if (! $districtName) {
            $districtName = 'Samarinda';
        }

        $matchingAlerts = self::query()
            ->where(function ($q) use ($property) {
                $q->whereNull('type')->orWhere('type', $property->type);
            })
            ->where(function ($q) use ($property) {
                $q->whereNull('min_price')->orWhere('min_price', '<=', $property->price);
            })
            ->where(function ($q) use ($property) {
                $q->whereNull('max_price')->orWhere('max_price', '>=', $property->price);
            })
            ->where(function ($q) use ($districtName) {
                $q->whereNull('district_name')->orWhere('district_name', $districtName);
            })
            ->where('user_id', '!=', $property->user_id)
            ->get();

        foreach ($matchingAlerts as $alert) {
            Notification::query()->create([
                'user_id' => $alert->user_id,
                'title' => 'Properti Baru yang Cocok!',
                'message' => 'Properti baru "'.$property->title.'" di '.$districtName.' tersedia dengan harga Rp '.number_format($property->price, 0, ',', '.').'.',
                'url' => route('properties.show', $property->id),
                'is_read' => false,
            ]);
        }
    }
}
