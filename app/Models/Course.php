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

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function attributes()
    {
        return $this->hasMany(CourseAttribute::class);
    }

    public function prices()
    {
        return $this->hasMany(CoursePrice::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
