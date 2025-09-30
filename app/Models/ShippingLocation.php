<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingLocation extends Model
{
    protected $fillable = [
        'type',
        'rajaongkir_id',
        'name',
        'province_id',
        'type_name',
        'postal_code',
    ];

    /**
     * Get all provinces
     */
    public static function getProvinces()
    {
        return self::where('type', 'province')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get cities by province
     */
    public static function getCitiesByProvince(int $provinceId)
    {
        return self::where('type', 'city')
            ->where('province_id', $provinceId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all cities
     */
    public static function getCities()
    {
        return self::where('type', 'city')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get province relationship (for cities)
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(ShippingLocation::class, 'province_id');
    }

    /**
     * Get cities relationship (for provinces)
     */
    public function cities(): HasMany
    {
        return $this->hasMany(ShippingLocation::class, 'province_id');
    }

    /**
     * Find location by RajaOngkir ID and type
     */
    public static function findByRajaongkirId(int $rajaongkirId, string $type)
    {
        return self::where('rajaongkir_id', $rajaongkirId)
            ->where('type', $type)
            ->first();
    }
}
