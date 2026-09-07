<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property float $total
 * @property float $discount
 * @property float $initial_payment
 * @property float $pending_balance
 * @property string $status
 * @property string $payment_method
 * @property string $invoice_type
 * @property string|null $document_number
 * @property string|null $invoice_number
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SaleDetail[] $details
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 */
class Sale extends Model
{
    protected $fillable = [
        'customer_id', 'user_id', 'total', 'status', 'payment_method',
        'invoice_type', 'document_number', 'due_date', 'initial_payment', 'pending_balance', 'discount'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
