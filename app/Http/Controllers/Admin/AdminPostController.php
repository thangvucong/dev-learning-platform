<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\Admin\AdminPostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPostController extends Controller
{
    protected AdminPostService $service;

    /**
     * @param  \App\Services\Admin\AdminPostService  $service
     */
    public function __construct(AdminPostService $service)
    {
        $this->service = $service;
    }

    /**
     * Admin posts moderation dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $viewData = $this->service->buildIndexViewData($request->only(['status', 'q', 'sort']));

        return view('components.admin.posts.index', $viewData);
    }

    /**
     * Preview post detail for moderation.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(int $id): View
    {
        $post = $this->service->getForPreview($id);
        if (!$post) {
            abort(404);
        }

        return view('components.admin.posts.show', [
            'post' => $post,
        ]);
    }

    /**
     * Approve a pending post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        $ok = $this->service->approve($id);
        toastr($ok ? 'Đã duyệt bài viết.' : 'Không thể duyệt bài viết.', $ok ? 'success' : 'error');

        return back();
    }

    /**
     * Reject a pending post with reason.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'reject_reason' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối.',
            'reject_reason.min' => 'Lý do từ chối phải có ít nhất :min ký tự.',
            'reject_reason.max' => 'Lý do từ chối tối đa :max ký tự.',
        ]);

        $ok = $this->service->reject($id, (string) $validated['reject_reason']);
        toastr($ok ? 'Đã từ chối bài viết.' : 'Không thể từ chối bài viết.', $ok ? 'success' : 'error');

        return back();
    }

    /**
     * Hide a published post (move back to draft).
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unpublish(int $id): RedirectResponse
    {
        $ok = $this->service->unpublish($id);
        toastr($ok ? 'Đã ẩn bài viết (chuyển về bản nháp).' : 'Không thể ẩn bài viết.', $ok ? 'success' : 'error');

        return back();
    }

    /**
     * Delete a post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $ok = $this->service->delete($id);
        toastr($ok ? 'Đã xóa bài viết.' : 'Không thể xóa bài viết.', $ok ? 'success' : 'error');

        return back();
    }
}

