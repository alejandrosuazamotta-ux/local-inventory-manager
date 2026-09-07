<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string|null $barcode
 * @property float $price
 * @property float $cost
 * @property int $stock
 * @property int $min_stock
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Product extends Model
{
    protected $fillable = [
        'name', 'barcode', 'price', 
        'cost', 'stock', 'min_stock', 'status'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('orderByName', function ($builder) {
            $builder->orderBy('name', 'asc');
        });
    }
}
