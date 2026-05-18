<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'email_verified_at',
        'is_active',
        'last_login_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isTeacher(): bool
    {
        return $this->hasRole(self::ROLE_TEACHER);
    }

    /**
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    /**
     * Get the courses instructed by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function instructedCourses()
    {
        return $this->hasManyThrough(
            Course::class,
            CourseClass::class,
            'instructor_id',
            'id',
            'id',
            'course_id'
        );
    }

    /**
     * Get the classes instructed by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function instructedClasses()
    {
        return $this->hasMany(CourseClass::class, 'instructor_id');
    }

    /**
     * Get the enrollments of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the courses enrolled by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['status', 'enrolled_at', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get the classes assigned to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'class_enrollments', 'user_id', 'class_id')
            ->withPivot(['status', 'assigned_at'])
            ->withTimestamps();
    }

    /**
     * Get the class enrollment records of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classEnrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    /**
     * Get the course discounts created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdCourseDiscounts()
    {
        return $this->hasMany(CourseDiscount::class, 'created_by');
    }

    /**
     * Get all orders of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
