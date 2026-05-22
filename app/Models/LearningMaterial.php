<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningMaterial extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'class_id',
        'class_session_id',
        'uploaded_by',
        'title',
        'description',
        'original_name',
        'mime_type',
        'size',
        'drive_file_id',
        'drive_folder_id',
        'status',
        'published_at',
    ];

    protected $casts = [
        'size' => 'integer',
        'published_at' => 'datetime',
    ];

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class, 'class_id');
    }

    public function session()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
