<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 0;
    public const STATUS_PUBLISHED = 1;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail_url',
        'intro_video_url',
        'status',
        'original_price',
        'rating_avg',
        'rating_count',
        'published_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'original_price' => 'integer',
        'rating_avg' => 'decimal:1',
        'rating_count' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Get the enrollments of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the students enrolled in the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot(['status', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get the classes opened for the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }

    /**
     * Get the tracks of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    /**
     * Get the attributes of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes()
    {
        return $this->hasMany(CourseAttribute::class);
    }

    /**
     * Get the discounts of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function discounts()
    {
        return $this->hasMany(CourseDiscount::class);
    }

    /**
     * Get the order items of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get active discounts of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeDiscounts()
    {
        return $this->discounts()->where('is_active', true);
    }
}
