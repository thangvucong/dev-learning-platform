<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    use HasFactory;

    /**
     * Classes table
     *
     * @var string
     */
    protected $table = 'classes';

    protected $fillable = [
        'course_id',
        'name',
        'code',
        'status',
        'start_at',
        'end_at',
        'location',
    ];

    protected $casts = [
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
     * Get the students assigned to the class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'class_user', 'class_id', 'user_id')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps();
    }
    
    public function students()
    {
        return $this->users();
    }
}
