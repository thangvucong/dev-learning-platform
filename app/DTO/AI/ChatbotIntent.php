<?php

namespace App\DTO\AI;

class ChatbotIntent
{
    public const FEATURED_COURSES = 'featured_courses';
    public const FEATURED_POSTS = 'featured_posts';
    public const COURSE_SEARCH = 'course_search';
    public const POST_SEARCH = 'post_search';
    public const CURRENT_PAGE = 'current_page';
    public const GENERIC = 'generic';

    public string $type;

    public string $keyword;

    public function __construct(string $type, string $keyword = '')
    {
        $this->type = $type;
        $this->keyword = trim($keyword);
    }

    public function hasKeyword(): bool
    {
        return $this->keyword !== '';
    }
}
