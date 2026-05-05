<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SepayWebhookEvent extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'sepay_transaction_id',
        'order_id',
        'payload',
        'status',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
    ];

    /**
     * Related order (if matched).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
