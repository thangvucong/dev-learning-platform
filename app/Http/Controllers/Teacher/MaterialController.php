<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreLearningMaterialRequest;
use App\Models\CourseClass;
use App\Models\LearningMaterial;
use App\Services\LearningMaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MaterialController extends Controller
{
    protected LearningMaterialService $materialService;

    public function __construct(LearningMaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    public function index(Request $request): View
    {
        return view('pages.teacher.materials.index', $this->materialService->buildTeacherIndex($request->user(), $this->filters($request)));
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->materialService->buildTeacherIndex($request->user(), $this->filters($request)));
    }

    public function store(StoreLearningMaterialRequest $request): RedirectResponse
    {
        try {
            $this->materialService->createTeacherMaterial(
                $request->user(),
                $request->validated(),
                $request->file('file')
            );
        } catch (Throwable $exception) {
            report($exception);

            toastr('Không thể tải tài liệu lên Google Drive. Vui lòng kiểm tra cấu hình Drive hoặc thử lại.', 'error');

            return back()->withInput();
        }

        toastr('Đã tải tài liệu lên thành công.', 'success');

        return back();
    }

    public function destroy(Request $request, LearningMaterial $learningMaterial): RedirectResponse
    {
        $this->materialService->archiveTeacherMaterial($request->user(), $learningMaterial);

        toastr('Đã ẩn tài liệu.', 'success');

        return back();
    }

    public function download(Request $request, LearningMaterial $learningMaterial): StreamedResponse
    {
        $content = $this->materialService->teacherDownload($request->user(), $learningMaterial);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $learningMaterial->original_name, [
            'Content-Type' => $learningMaterial->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function classSessions(Request $request, CourseClass $courseClass): JsonResponse
    {
        return response()->json([
            'sessions' => $this->materialService->teacherSessionOptions($request->user(), $courseClass),
        ]);
    }

    protected function filters(Request $request): array
    {
        return [
            'class_id' => (int) $request->query('class_id', 0),
            'class_session_id' => (int) $request->query('class_session_id', 0),
        ];
    }
}
