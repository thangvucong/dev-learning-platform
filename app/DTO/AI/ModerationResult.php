<?php

namespace App\DTO\AI;

use App\Models\Post;
use InvalidArgumentException;

class ModerationResult
{
    public string $decision;

    public float $confidence;

    public string $severity;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $flags;

    public string $summary;

    public string $explanation;

    public ?string $escalationReason;

    public bool $safeToPublish;

    /**
     * @param  array<int, array<string, mixed>>  $flags
     */
    public function __construct(
        string $decision,
        float $confidence,
        string $severity,
        array $flags,
        string $summary,
        string $explanation,
        ?string $escalationReason,
        bool $safeToPublish
    ) {
        $this->decision = $decision;
        $this->confidence = max(0, min(1, $confidence));
        $this->severity = $severity;
        $this->flags = $flags;
        $this->summary = $summary;
        $this->explanation = $explanation;
        $this->escalationReason = $escalationReason;
        $this->safeToPublish = $safeToPublish;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return self
     */
    public static function fromArray(array $payload): self
    {
        $decision = (string) ($payload['decision'] ?? '');
        if (!in_array($decision, [
            Post::AI_DECISION_APPROVE,
            Post::AI_DECISION_REJECT,
            Post::AI_DECISION_HUMAN_REVIEW,
        ], true)) {
            throw new InvalidArgumentException('Invalid moderation decision.');
        }

        $severity = (string) ($payload['severity'] ?? '');
        if (!in_array($severity, ['none', 'low', 'medium', 'high', 'critical'], true)) {
            throw new InvalidArgumentException('Invalid moderation severity.');
        }

        $confidence = $payload['confidence'] ?? null;
        if (!is_numeric($confidence)) {
            throw new InvalidArgumentException('Invalid moderation confidence.');
        }

        $flags = $payload['flags'] ?? [];
        if (!is_array($flags)) {
            throw new InvalidArgumentException('Invalid moderation flags.');
        }

        return new self(
            $decision,
            (float) $confidence,
            $severity,
            array_values($flags),
            trim((string) ($payload['summary'] ?? '')),
            trim((string) ($payload['explanation'] ?? '')),
            isset($payload['escalation_reason']) ? trim((string) $payload['escalation_reason']) : null,
            (bool) ($payload['safe_to_publish'] ?? false)
        );
    }
}
