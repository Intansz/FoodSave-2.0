<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFee extends Model
{
    protected $fillable = [
        'order_id',
        'merchant_id',
        'settlement_id',
        'fee_percentage',
        'transaction_amount',
        'fee_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'fee_percentage' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }
}
