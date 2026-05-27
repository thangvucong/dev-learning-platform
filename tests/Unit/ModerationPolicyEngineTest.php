<?php

namespace Tests\Unit;

use App\DTO\AI\ModerationResult;
use App\Models\Post;
use App\Services\AI\ModerationPolicyEngine;
use Tests\TestCase;

class ModerationPolicyEngineTest extends TestCase
{
    public function test_it_auto_publishes_high_confidence_safe_content(): void
    {
        config()->set('ai.moderation.auto_approve_threshold', 0.93);

        $attributes = (new ModerationPolicyEngine())->decide(new ModerationResult(
            Post::AI_DECISION_APPROVE,
            0.97,
            'low',
            [],
            'Không phát hiện rủi ro.',
            'Nội dung phù hợp để xuất bản.',
            null,
            true
        ));

        $this->assertSame(Post::STATUS_PUBLISHED, $attributes['status']);
        $this->assertNull($attributes['reject_reason']);
    }

    public function test_it_auto_rejects_high_confidence_severe_content(): void
    {
        config()->set('ai.moderation.auto_reject_threshold', 0.90);

        $attributes = (new ModerationPolicyEngine())->decide(new ModerationResult(
            Post::AI_DECISION_REJECT,
            0.95,
            'high',
            [
                [
                    'category' => 'scam',
                    'severity' => 'high',
                    'confidence' => 0.96,
                    'evidence' => 'cam kết lợi nhuận bất thường',
                ],
            ],
            'Có dấu hiệu scam.',
            'Bài viết quảng bá hành vi lừa đảo.',
            null,
            false
        ));

        $this->assertSame(Post::STATUS_REJECTED, $attributes['status']);
        $this->assertSame('Bài viết quảng bá hành vi lừa đảo.', $attributes['reject_reason']);
    }

    public function test_it_escalates_ambiguous_or_low_confidence_content(): void
    {
        $attributes = (new ModerationPolicyEngine())->decide(new ModerationResult(
            Post::AI_DECISION_APPROVE,
            0.72,
            'medium',
            [
                [
                    'category' => 'educational_sensitive',
                    'severity' => 'medium',
                    'confidence' => 0.78,
                    'evidence' => 'nội dung bảo mật có thể bị lạm dụng',
                ],
            ],
            'Cần admin kiểm tra.',
            'Nội dung có bối cảnh giáo dục nhưng nhạy cảm.',
            'Security education content needs human review.',
            false
        ));

        $this->assertSame(Post::STATUS_PENDING_HUMAN_REVIEW, $attributes['status']);
        $this->assertSame('Security education content needs human review.', $attributes['ai_escalation_reason']);
    }
}
