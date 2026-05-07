<?php

namespace App\Services\Interfaces;

use App\Models\Post;
use Illuminate\Http\UploadedFile;

interface PostServiceInterface
{
    /**
     * Create a post from create form payload.
     *
     * @param  int  $userId
     * @param  array<string, mixed>  $payload
     * @param  string  $action  draft|publish
     * @return \App\Models\Post
     */
    public function createFromComposer(int $userId, array $payload, string $action): Post;

    /**
     * Update an existing post from composer payload.
     *
     * @param  \App\Models\Post  $post
     * @param  array<string, mixed>  $payload
     * @param  string  $action  draft|pending
     * @return \App\Models\Post
     */
    public function updateFromComposer(Post $post, array $payload, string $action): Post;

    /**
     * Upload an editor image and return public URL.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    public function uploadEditorImage(UploadedFile $file): string;
}

