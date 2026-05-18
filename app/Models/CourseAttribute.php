<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAttribute extends Model
{
    use HasFactory;

    public const TYPE_REQUIREMENT = 'requirement';
    public const TYPE_BENEFIT = 'benefit';
    public const TYPE_TARGET = 'target';

    protected $fillable = [
        'course_id',
        'type',
        'content',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
