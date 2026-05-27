<?php

namespace Tests\Unit;

use App\Services\AI\ChatbotService;
use App\Services\AI\ChatbotContextService;
use App\Services\AI\GeminiClient;
use Mockery;
use Tests\TestCase;

class ChatbotServiceTest extends TestCase
{
    public function test_it_returns_gemini_answer_with_sources(): void
    {
        $context = Mockery::mock(ChatbotContextService::class);
        $context->shouldReceive('build')
            ->once()
            ->with('course_detail', 'laravel-co-ban', 'Khóa này phù hợp với ai?')
            ->andReturn([
                'context' => [
                    'page_type' => 'course_detail',
                    'current_page' => ['title' => 'Laravel cơ bản'],
                    'related_courses' => [],
                    'related_posts' => [],
                ],
                'sources' => [
                    ['type' => 'current_page', 'title' => 'Laravel cơ bản', 'url' => '/courses/laravel-co-ban'],
                ],
            ]);

        $gemini = Mockery::mock(GeminiClient::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andReturn('Khóa này phù hợp với người mới học Laravel.');

        $result = (new ChatbotService($context, $gemini))->reply(
            'Khóa này phù hợp với ai?',
            'course_detail',
            'laravel-co-ban'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Khóa này phù hợp với người mới học Laravel.', $result['message']);
        $this->assertSame('/courses/laravel-co-ban', $result['sources'][0]['url']);
    }

    public function test_it_returns_safe_fallback_when_gemini_fails(): void
    {
        $context = Mockery::mock(ChatbotContextService::class);
        $context->shouldReceive('build')
            ->once()
            ->andReturn([
                'context' => ['page_type' => 'home'],
                'sources' => [],
            ]);

        $gemini = Mockery::mock(GeminiClient::class);
        $gemini->shouldReceive('generateText')
            ->once()
            ->andThrow(new \RuntimeException('Gemini request failed with HTTP status 403.'));

        $result = (new ChatbotService($context, $gemini))->reply(
            'Có khóa học nào mới không?',
            'home',
            null
        );

        $this->assertFalse($result['success']);
        $this->assertSame('Chatbot tạm thời chưa phản hồi được. Vui lòng thử lại sau.', $result['message']);
        $this->assertSame([], $result['sources']);
    }

    public function test_it_skips_gemini_for_featured_course_listing(): void
    {
        $context = Mockery::mock(ChatbotContextService::class);
        $context->shouldReceive('build')
            ->once()
            ->andReturn([
                'context' => [
                    'intent' => ['type' => 'featured_courses', 'keyword' => ''],
                    'related_courses' => [
                        [
                            'title' => 'Laravel cơ bản',
                            'sale_price' => 1200000,
                            'rating_avg' => 4.8,
                        ],
                    ],
                ],
                'sources' => [
                    ['type' => 'course', 'title' => 'Laravel cơ bản', 'url' => '/courses/laravel-co-ban'],
                ],
            ]);

        $gemini = Mockery::mock(GeminiClient::class);
        $gemini->shouldReceive('generateText')->never();

        $result = (new ChatbotService($context, $gemini))->reply(
            'Khóa học nổi bật hiện tại là gì?',
            'home',
            null
        );

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Các khóa học nổi bật hiện tại', $result['message']);
        $this->assertStringContainsString('Laravel cơ bản', $result['message']);
    }
}
