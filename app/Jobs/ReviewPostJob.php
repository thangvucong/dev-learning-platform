<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\AI\AIReviewService;
use App\Services\AI\ModerationPolicyEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReviewPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $postId;

    public function __construct(int $postId)
    {
        $this->postId = $postId;
        $this->onQueue('ai-moderation');
    }

    /**
     * @return int
     */
    public function tries(): int
    {
        return (int) config('ai.moderation.max_attempts', 3);
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(AIReviewService $reviewService, ModerationPolicyEngine $policyEngine): void
    {
        $post = Post::query()->with('user:id,name,email')->find($this->postId);
        if (!$post || $post->status !== Post::STATUS_PENDING_AI_REVIEW) {
            return;
        }

        $post->increment('ai_review_attempts');

        try {
            $result = $reviewService->review($post->fresh(['user']));
            $attributes = $policyEngine->decide($result);
        } catch (Throwable $exception) {
            if (config('queue.default') !== 'sync' && $this->attempts() < $this->tries()) {
                throw $exception;
            }

            $this->markFailedForHumanReview($exception);
            return;
        }

        $post->fresh()->update($attributes);
    }

    public function failed(Throwable $exception): void
    {
        $this->markFailedForHumanReview($exception);
    }

    protected function markFailedForHumanReview(Throwable $exception): void
    {
        $post = Post::query()->find($this->postId);
        if (!$post || $post->status !== Post::STATUS_PENDING_AI_REVIEW) {
            return;
        }

        $post->update([
            'status' => Post::STATUS_PENDING_HUMAN_REVIEW,
            'ai_review_status' => Post::AI_STATUS_FAILED,
            'ai_decision' => Post::AI_DECISION_HUMAN_REVIEW,
            'ai_confidence' => null,
            'ai_severity' => 'medium',
            'ai_escalation_reason' => 'AI review failed after retries.',
            'ai_reviewed_at' => now(),
            'ai_model' => (string) config('ai.gemini.model', 'gemini-1.5-flash'),
            'ai_error_code' => class_basename($exception),
            'ai_error_message' => substr($exception->getMessage(), 0, 2000),
        ]);

        Log::warning('AI post moderation failed.', [
            'post_id' => $this->postId,
            'error' => class_basename($exception),
        ]);
    }
}
