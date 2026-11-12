<?php

namespace App\Models;

use Database\Factories\BusRouteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An origin → destination route. Named BusRoute to avoid clashing with the Route facade.
 */
#[Fillable(['origin', 'destination', 'distance_km', 'base_price', 'status'])]
class BusRoute extends Model
{
    /** @use HasFactory<BusRouteFactory> */
    use HasFactory;

    protected $table = 'routes';

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'base_price' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<Trip, $this>
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'route_id');
    }
}
