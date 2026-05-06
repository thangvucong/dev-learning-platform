<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'session_no',
        'title',
        'start_at',
        'end_at',
        'status',
        'meeting_type',
        'meeting_info',
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
}

