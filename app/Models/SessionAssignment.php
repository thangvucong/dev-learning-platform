<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionAssignment extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';

    public const SUBMISSION_TEXT = 'text';
    public const SUBMISSION_FILE = 'file';
    public const SUBMISSION_BOTH = 'both';

    protected $fillable = [
        'class_session_id',
        'teacher_id',
        'title',
        'content',
        'attachment_disk',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'submission_type',
        'due_at',
        'status',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'session_assignment_id');
    }
}
