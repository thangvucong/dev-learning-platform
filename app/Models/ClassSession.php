<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_LIVE = 'live';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_MISSED = 'missed';
    public const STATUS_CANCELLED = 'cancelled';

    public const MEETING_ZOOM = 'zoom';
    public const MEETING_OFFLINE = 'offline';

    protected $fillable = [
        'class_id',
        'session_no',
        'title',
        'start_at',
        'end_at',
        'status',
        'meeting_type',
        'meeting_info',
        'join_url',
        'description',
    ];

    protected $casts = [
        'session_no' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Get class of this session.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(SessionAttendance::class, 'class_session_id');
    }

    public function assignments()
    {
        return $this->hasMany(SessionAssignment::class, 'class_session_id');
    }

    public function materials()
    {
        return $this->hasMany(LearningMaterial::class, 'class_session_id');
    }
}
