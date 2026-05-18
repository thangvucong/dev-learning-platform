<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDiscount extends Model
{
    use HasFactory;

    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';
    public const TYPE_FINAL_PRICE = 'final_price';

    public const REPEAT_NONE = 'none';
    public const REPEAT_WEEKLY = 'weekly';

    protected $fillable = [
        'course_id',
        'type',
        'amount',
        'starts_at',
        'ends_at',
        'repeat_type',
        'day_of_week',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'day_of_week' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

