<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    public const MODE_ZOOM = 'zoom';
    public const MODE_OFFLINE = 'offline';
    public const MODE_HYBRID = 'hybrid';

    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Classes table
     *
     * @var string
     */
    protected $table = 'classes';

    protected $fillable = [
        'course_id',
        'instructor_id',
        'name',
        'code',
        'mode',
        'status',
        'capacity',
        'start_at',
        'end_at',
        'location',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Get the course of the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor assigned to the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the students assigned to the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'class_enrollments', 'class_id', 'user_id')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps();
    }
    
    public function students()
    {
        return $this->users();
    }

    /**
     * Get sessions of this class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions()
    {
        return $this->hasMany(ClassSession::class, 'class_id')->orderBy('start_at');
    }

    /**
     * Get the class enrollment records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classEnrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'class_id');
    }
}
