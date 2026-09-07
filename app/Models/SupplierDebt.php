<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property float $debt_amount
 * @property float $paid_amount
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SupplierDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'debt_amount',
        'paid_amount',
        'status',
    ];
}
