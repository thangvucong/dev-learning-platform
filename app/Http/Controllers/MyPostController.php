<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\MyPostServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyPostController extends Controller
{
    protected MyPostServiceInterface $myPostService;

    /**
     * @param  \App\Services\Interfaces\MyPostServiceInterface  $myPostService
     */
    public function __construct(MyPostServiceInterface $myPostService)
    {
        $this->myPostService = $myPostService;
    }

    /**
     * Creator dashboard: manage user's posts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $viewData = $this->myPostService->buildMyPostsDashboard(
            (int) $request->user()->id,
            $request->only(['status', 'q', 'sort'])
        );

        return view('posts.my-index', $viewData);
    }

    /**
     * Delete a post owned by user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $postId): RedirectResponse
    {
        $ok = $this->myPostService->deleteMyPost((int) $request->user()->id, $postId);
        toastr($ok ? 'Đã xóa bài viết.' : 'Không thể xóa bài viết.', $ok ? 'success' : 'error');

        return back();
    }

    /**
     * Resubmit a draft/rejected post for review.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $postId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resubmit(Request $request, int $postId): RedirectResponse
    {
        $ok = $this->myPostService->resubmitForReview((int) $request->user()->id, $postId);
        toastr($ok ? 'Đã gửi bài viết để xét duyệt.' : 'Không thể gửi duyệt lại.', $ok ? 'success' : 'error');

        return back();
    }
}

