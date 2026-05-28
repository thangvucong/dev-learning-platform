<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_AI_REVIEW = 'pending_ai_review';
    public const STATUS_PENDING_HUMAN_REVIEW = 'pending_human_review';
    /** @deprecated Use STATUS_PENDING_HUMAN_REVIEW. */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    public const AI_STATUS_PENDING = 'pending';
    public const AI_STATUS_COMPLETED = 'completed';
    public const AI_STATUS_FAILED = 'failed';
    public const AI_STATUS_SKIPPED = 'skipped';

    public const AI_DECISION_APPROVE = 'approve';
    public const AI_DECISION_REJECT = 'reject';
    public const AI_DECISION_HUMAN_REVIEW = 'human_review';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'description',
        'thumbnail',
        'image',
        'views_count',
        'status',
        'reject_reason',
        'ai_review_status',
        'ai_decision',
        'ai_confidence',
        'ai_severity',
        'ai_flags',
        'ai_summary',
        'ai_explanation',
        'ai_escalation_reason',
        'ai_review_attempts',
        'ai_reviewed_at',
        'ai_model',
        'ai_error_code',
        'ai_error_message',
        'reviewed_by',
        'human_reviewed_at',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'ai_confidence' => 'float',
        'ai_flags' => 'array',
        'ai_review_attempts' => 'integer',
        'ai_reviewed_at' => 'datetime',
        'human_reviewed_at' => 'datetime',
    ];

    /**
     * Get the author of the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PENDING_AI_REVIEW,
            self::STATUS_PENDING_HUMAN_REVIEW,
        ], true);
    }

    /**
     * @return bool
     */
    public function isPendingAiReview(): bool
    {
        return $this->status === self::STATUS_PENDING_AI_REVIEW;
    }

    /**
     * @return bool
     */
    public function isPendingHumanReview(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PENDING_HUMAN_REVIEW], true);
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
