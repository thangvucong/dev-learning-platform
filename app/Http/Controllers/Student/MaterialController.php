<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\LearningMaterial;
use App\Services\LearningMaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends Controller
{
    protected LearningMaterialService $materialService;

    public function __construct(LearningMaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

    public function index(Request $request): View
    {
        return view('pages.student.materials.index', $this->materialService->buildStudentIndex($request->user(), $this->filters($request)));
    }

    public function data(Request $request): JsonResponse
    {
        return response()->json($this->materialService->buildStudentIndex($request->user(), $this->filters($request)));
    }

    public function download(Request $request, LearningMaterial $learningMaterial): StreamedResponse
    {
        $content = $this->materialService->studentDownload($request->user(), $learningMaterial);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $learningMaterial->original_name, [
            'Content-Type' => $learningMaterial->mime_type ?: 'application/octet-stream',
        ]);
    }

    public function classSessions(Request $request, CourseClass $courseClass): JsonResponse
    {
        return response()->json([
            'sessions' => $this->materialService->studentSessionOptions($request->user(), $courseClass),
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
