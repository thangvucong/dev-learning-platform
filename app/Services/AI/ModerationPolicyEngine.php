<?php

namespace App\Services\AI;

use App\DTO\AI\ModerationResult;
use App\Models\Post;

class ModerationPolicyEngine
{
    /**
     * @param  \App\DTO\AI\ModerationResult  $result
     * @return array<string, mixed>
     */
    public function decide(ModerationResult $result): array
    {
        $attributes = [
            'ai_review_status' => Post::AI_STATUS_COMPLETED,
            'ai_decision' => $result->decision,
            'ai_confidence' => $result->confidence,
            'ai_severity' => $result->severity,
            'ai_flags' => $result->flags,
            'ai_summary' => $result->summary,
            'ai_explanation' => $result->explanation,
            'ai_escalation_reason' => $result->escalationReason,
            'ai_reviewed_at' => now(),
            'ai_model' => (string) config('ai.gemini.model', 'gemini-1.5-flash'),
            'ai_error_code' => null,
            'ai_error_message' => null,
        ];

        if ($this->shouldAutoApprove($result)) {
            return $attributes + [
                'status' => Post::STATUS_PUBLISHED,
                'reject_reason' => null,
            ];
        }

        if ($this->shouldAutoReject($result)) {
            return $attributes + [
                'status' => Post::STATUS_REJECTED,
                'reject_reason' => $this->buildRejectReason($result),
            ];
        }

        $attributes['ai_escalation_reason'] = $result->escalationReason ?: $this->buildEscalationReason($result);

        return $attributes + [
            'status' => Post::STATUS_PENDING_HUMAN_REVIEW,
            'reject_reason' => null,
        ];
    }

    protected function shouldAutoApprove(ModerationResult $result): bool
    {
        $threshold = (float) config('ai.moderation.auto_approve_threshold', 0.93);

        return $result->decision === Post::AI_DECISION_APPROVE
            && $result->safeToPublish
            && $result->confidence >= $threshold
            && in_array($result->severity, ['none', 'low'], true)
            && !$this->hasEscalationFlag($result);
    }

    protected function shouldAutoReject(ModerationResult $result): bool
    {
        $threshold = (float) config('ai.moderation.auto_reject_threshold', 0.90);

        return $result->decision === Post::AI_DECISION_REJECT
            && $result->confidence >= $threshold
            && in_array($result->severity, ['high', 'critical'], true)
            && $this->hasRejectableFlag($result);
    }

    protected function hasRejectableFlag(ModerationResult $result): bool
    {
        $rejectable = [
            'spam',
            'scam',
            'unsafe_links',
            'hate_speech',
            'sexual_content',
            'violence_extremism',
            'toxic_language',
            'political_extremism',
        ];

        foreach ($result->flags as $flag) {
            if (in_array((string) ($flag['category'] ?? ''), $rejectable, true)) {
                return true;
            }
        }

        return false;
    }

    protected function hasEscalationFlag(ModerationResult $result): bool
    {
        $manual = [
            'misinformation',
            'educational_sensitive',
            'other',
        ];

        foreach ($result->flags as $flag) {
            $category = (string) ($flag['category'] ?? '');
            if (in_array($category, $manual, true)) {
                return true;
            }

            if (!in_array($category, [
                'spam',
                'scam',
                'unsafe_links',
                'hate_speech',
                'sexual_content',
                'violence_extremism',
                'toxic_language',
                'misinformation',
                'political_extremism',
                'clickbait',
                'low_quality_ai_generated',
                'educational_sensitive',
                'other',
            ], true)) {
                return true;
            }
        }

        return false;
    }

    protected function buildRejectReason(ModerationResult $result): string
    {
        $reason = trim($result->explanation ?: $result->summary);

        return $reason !== '' ? $reason : 'Bài viết vi phạm chính sách nội dung.';
    }

    protected function buildEscalationReason(ModerationResult $result): string
    {
        if ($result->decision === Post::AI_DECISION_HUMAN_REVIEW) {
            return 'AI yêu cầu admin duyệt thủ công.';
        }

        return 'AI confidence thấp hoặc nội dung có dấu hiệu cần admin kiểm tra.';
    }
}
