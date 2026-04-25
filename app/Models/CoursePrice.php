<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'currency_id',
        'price',
        'compare_price',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
