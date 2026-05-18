<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    public const PAYMENT_ONEPAY_DOMESTIC = 'onepay_dom_card';
    public const PAYMENT_ONEPAY_INTERNATIONAL = 'onepay_int_card';
    public const PAYMENT_SEPAY_QR = 'sepay_qr';

    protected $fillable = [
        'user_id',
        'subtotal_amount',
        'discount_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_reference',
        'note',
        'paid_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal_amount' => 'integer',
        'discount_amount' => 'integer',
        'total_amount' => 'integer',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
