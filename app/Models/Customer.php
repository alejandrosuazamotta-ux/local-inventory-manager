<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $document_type
 * @property string $document_number
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property float $total_debt
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $sales
 */
class Customer extends Model
{
    protected $fillable = [
        'document_type', 'document_number', 'name', 
        'phone', 'email', 'address', 'total_debt'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
