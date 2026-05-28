<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Repositories\PostRepository;
use App\Services\Interfaces\PostServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    protected PostServiceInterface $postService;

    protected PostRepository $postRepository;

    /**
     * @param  \App\Services\Interfaces\PostServiceInterface  $postService
     * @param  \App\Repositories\PostRepository  $postRepository
     */
    public function __construct(PostServiceInterface $postService, PostRepository $postRepository)
    {
        $this->postService = $postService;
        $this->postRepository = $postRepository;
    }

    /**
     * Display post index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('pages.posts.index', [
            'posts' => $this->postRepository->getPublishedPosts(10),
        ]);
    }

    /**
     * Display post create page.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Display post edit page (owner only).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        if ($post->status === Post::STATUS_PUBLISHED) {
            return redirect()->route('posts.show', $post->slug);
        }

        return view('posts.edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update a post (draft/pending).
     *
     * @param  \App\Http\Requests\UpdatePostRequest  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        if ($post->status === Post::STATUS_PUBLISHED) {
            toastr('Không thể chỉnh sửa bài đã xuất bản.', 'error');
            return redirect()->route('posts.show', $post->slug);
        }

        $validated = $request->validated();
        $action = (string) $validated['action'];

        $post = $this->postService->updateFromComposer(
            $post,
            [
                'title' => $validated['title'],
                'content' => $validated['content'],
                'thumbnail' => $request->file('thumbnail'),
                'image' => $request->file('image'),
            ],
            $action
        );

        toastr($action === 'pending' ? 'Đã gửi bài viết để xét duyệt.' : 'Đã lưu bản nháp.', 'success');

        return redirect()->route('my-posts.index', ['status' => $post->status]);
    }

    /**
     * Store a new post (draft or publish).
     *
     * @param  \App\Http\Requests\StorePostRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $action = (string) $validated['action'];

        $post = $this->postService->createFromComposer(
            (int) $request->user()->id,
            [
                'title' => $validated['title'],
                'content' => $validated['content'],
                'thumbnail' => $request->file('thumbnail'),
                'image' => $request->file('image'),
            ],
            $action
        );

        toastr($action === 'pending' ? 'Đã gửi bài viết để xét duyệt.' : 'Đã lưu bản nháp.', 'success');

        return redirect()->route('posts.show', $post->slug);
    }

    /**
     * Upload an image from editor toolbar and return public URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadEditorImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $url = $this->postService->uploadEditorImage($validated['image']);

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Display post detail page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show(Request $request, string $slug): View
    {
        $post = $this->postRepository->findVisibleBySlug($slug, $request->user()?->id);

        if (!$post) {
            abort(404);
        }

        return view('posts.show', [
            'post' => $post,
        ]);
    }
}
