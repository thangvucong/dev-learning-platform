<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'level_id',
        'instructor_id',
        'title',
        'slug',
        'description',
        'thumbnail_url',
        'intro_video_url',
        'duration',
        'status',
        'is_free',
        'published_at',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the level of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Get the instructor of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

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
     * Get the prices of the course.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(CoursePrice::class);
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
}
