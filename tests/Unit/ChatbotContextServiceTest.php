<?php

namespace Tests\Unit;

use App\Repositories\ChatbotKnowledgeRepository;
use App\Services\AI\ChatbotContextService;
use App\Services\AI\ChatbotIntentDetector;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ChatbotContextServiceTest extends TestCase
{
    public function test_featured_course_question_uses_business_query_not_like_search(): void
    {
        $repository = Mockery::mock(ChatbotKnowledgeRepository::class);
        $repository->shouldReceive('getFeaturedCourses')
            ->once()
            ->with(4)
            ->andReturn(new Collection());
        $repository->shouldReceive('searchPublishedCourses')
            ->never();
        $repository->shouldReceive('getFeaturedPosts')
            ->never();
        $repository->shouldReceive('searchPublishedPosts')
            ->never();

        $payload = (new ChatbotContextService($repository, new ChatbotIntentDetector()))->build(
            'home',
            null,
            'Khóa học nổi bật hiện tại là gì?'
        );

        $this->assertSame('featured_courses', $payload['context']['intent']['type']);
        $this->assertSame('', $payload['context']['intent']['keyword']);
        $this->assertSame([], $payload['context']['related_courses']);
    }
}
