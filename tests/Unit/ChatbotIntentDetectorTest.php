<?php

namespace Tests\Unit;

use App\DTO\AI\ChatbotIntent;
use App\Services\AI\ChatbotIntentDetector;
use Tests\TestCase;

class ChatbotIntentDetectorTest extends TestCase
{
    public function test_it_detects_featured_course_intent_without_using_full_question_as_keyword(): void
    {
        $intent = (new ChatbotIntentDetector())->detect('Khóa học nổi bật hiện tại là gì?', 'home');

        $this->assertSame(ChatbotIntent::FEATURED_COURSES, $intent->type);
        $this->assertSame('', $intent->keyword);
    }

    public function test_it_extracts_short_course_search_keyword(): void
    {
        $intent = (new ChatbotIntentDetector())->detect('Tôi nên học khóa học Laravel nào cho người mới?', 'home');

        $this->assertSame(ChatbotIntent::COURSE_SEARCH, $intent->type);
        $this->assertSame('laravel', $intent->keyword);
    }
}
