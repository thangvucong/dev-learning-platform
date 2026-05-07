<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use App\Services\Interfaces\PostServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService implements PostServiceInterface
{
    protected PostRepository $postRepository;

    /**
     * @param  \App\Repositories\PostRepository  $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromComposer(int $userId, array $payload, string $action): Post
    {
        $title = trim((string) Arr::get($payload, 'title', ''));
        $content = (string) Arr::get($payload, 'content', '');

        $slug = $this->generateUniqueSlug($title);
        $description = $this->generateDescription($content, 180);

        $status = $action === 'pending' ? Post::STATUS_PENDING : Post::STATUS_DRAFT;

        $thumbnailPath = null;
        /** @var \Illuminate\Http\UploadedFile|null $thumbnail */
        $thumbnail = Arr::get($payload, 'thumbnail');
        if ($thumbnail instanceof UploadedFile) {
            $thumbnailPath = $thumbnail->store('posts', 'public');
        }

        $imagePath = null;
        /** @var \Illuminate\Http\UploadedFile|null $image */
        $image = Arr::get($payload, 'image');
        if ($image instanceof UploadedFile) {
            $imagePath = $image->store('posts', 'public');
        }

        return $this->postRepository->create([
            'user_id' => $userId,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'description' => $description,
            'thumbnail' => $thumbnailPath,
            'image' => $imagePath,
            'views_count' => 0,
            'status' => $status,
            'reject_reason' => null,
        ])->fresh(['user']);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFromComposer(Post $post, array $payload, string $action): Post
    {
        $title = trim((string) Arr::get($payload, 'title', ''));
        $content = (string) Arr::get($payload, 'content', '');
        $description = $this->generateDescription($content, 180);

        $status = $action === 'pending' ? Post::STATUS_PENDING : Post::STATUS_DRAFT;

        $attributes = [
            'title' => $title,
            'content' => $content,
            'description' => $description,
            'status' => $status,
        ];

        if ($status === Post::STATUS_PENDING) {
            $attributes['reject_reason'] = null;
        }

        /** @var \Illuminate\Http\UploadedFile|null $thumbnail */
        $thumbnail = Arr::get($payload, 'thumbnail');
        if ($thumbnail instanceof UploadedFile) {
            $attributes['thumbnail'] = $thumbnail->store('posts', 'public');
        }

        /** @var \Illuminate\Http\UploadedFile|null $image */
        $image = Arr::get($payload, 'image');
        if ($image instanceof UploadedFile) {
            $attributes['image'] = $image->store('posts', 'public');
        }

        $this->postRepository->update((int) $post->id, $attributes);

        return $post->fresh(['user']);
    }

    /**
     * {@inheritdoc}
     */
    public function uploadEditorImage(UploadedFile $file): string
    {
        $path = $file->store('posts', 'public');

        return Storage::url($path);
    }

    /**
     * Generate unique slug from title (bai-viet, bai-viet-1, ...).
     *
     * @param  string  $title
     * @return string
     */
    protected function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'bai-viet';
        }

        $slug = $base;
        $suffix = 0;

        while ($this->postRepository->slugExists($slug)) {
            $suffix++;
            $slug = $base . '-' . $suffix;
        }

        return $slug;
    }

    /**
     * Generate short description from markdown content.
     *
     * @param  string  $markdown
     * @param  int  $targetLength
     * @return string
     */
    protected function generateDescription(string $markdown, int $targetLength = 180): string
    {
        $text = $markdown;
        $text = preg_replace('/```[\s\S]*?```/m', ' ', $text) ?? $text;
        $text = preg_replace('/`[^`]*`/m', ' ', $text) ?? $text;
        $text = preg_replace('/!\[[^\]]*\]\([^)]+\)/m', ' ', $text) ?? $text;
        $text = preg_replace('/\[[^\]]*\]\([^)]+\)/m', ' ', $text) ?? $text;
        $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;
        $text = preg_replace('/^\s*>\s?/m', '', $text) ?? $text;
        $text = preg_replace('/[*_~#>-]+/m', ' ', $text) ?? $text;
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        return Str::limit($text, $targetLength, '…');
    }
}

