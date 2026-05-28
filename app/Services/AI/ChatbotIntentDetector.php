<?php

namespace App\Services\AI;

use App\DTO\AI\ChatbotIntent;

class ChatbotIntentDetector
{
    /**
     * @param  string  $message
     * @param  string  $pageType
     * @return \App\DTO\AI\ChatbotIntent
     */
    public function detect(string $message, string $pageType): ChatbotIntent
    {
        $normalized = $this->normalize($message);

        if ($this->containsAny($normalized, ['khoa hoc noi bat', 'khoa hoc hot', 'khoa hoc pho bien', 'khoa hoc tot nhat', 'khoa hoc dang chu y'])) {
            return new ChatbotIntent(ChatbotIntent::FEATURED_COURSES);
        }

        if ($this->containsAny($normalized, ['bai viet noi bat', 'bai viet hot', 'bai viet pho bien', 'bai viet dang doc'])) {
            return new ChatbotIntent(ChatbotIntent::FEATURED_POSTS);
        }

        if ($this->containsAny($normalized, ['khoa hoc', 'hoc gi', 'nen hoc', 'backend', 'frontend', 'laravel', 'php', 'javascript'])) {
            return new ChatbotIntent(ChatbotIntent::COURSE_SEARCH, $this->extractKeyword($normalized, true));
        }

        if ($this->containsAny($normalized, ['bai viet', 'doc gi', 'blog', 'huong dan'])) {
            return new ChatbotIntent(ChatbotIntent::POST_SEARCH, $this->extractKeyword($normalized, false));
        }

        if (in_array($pageType, ['post_detail', 'course_detail'], true)) {
            return new ChatbotIntent(ChatbotIntent::CURRENT_PAGE);
        }

        return new ChatbotIntent(ChatbotIntent::GENERIC, $this->extractKeyword($normalized, false));
    }

    protected function normalize(string $message): string
    {
        $message = mb_strtolower(trim($message));
        $map = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ];
        $message = strtr($message, $map);
        $message = preg_replace('/[^a-z0-9\s]/', ' ', $message) ?? $message;

        return trim(preg_replace('/\s+/', ' ', $message) ?? $message);
    }

    /**
     * @param  string  $text
     * @param  array<int, string>  $needles
     * @return bool
     */
    protected function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (strpos($text, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    protected function extractKeyword(string $normalized, bool $preferCourseTerms): string
    {
        $stopWords = [
            'toi', 'minh', 'ban', 'co', 'khong', 'nao', 'gi', 'la', 've', 'cho', 'hoi', 'tim', 'goi', 'y',
            'hien', 'tai', 'dang', 'can', 'muon', 'nen', 'phu', 'hop', 'nguoi', 'moi', 'bat', 'dau',
            'khoa', 'hoc', 'bai', 'viet', 'noi', 'bat', 'nhat', 'hot', 'pho', 'bien',
        ];

        $tokens = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $keywords = array_values(array_filter($tokens, function (string $token) use ($stopWords) {
            return strlen($token) >= 2 && !in_array($token, $stopWords, true);
        }));

        if (empty($keywords) && $preferCourseTerms) {
            foreach (['laravel', 'php', 'javascript', 'backend', 'frontend'] as $term) {
                if (strpos($normalized, $term) !== false) {
                    $keywords[] = $term;
                }
            }
        }

        return implode(' ', array_slice(array_unique($keywords), 0, 4));
    }
}
