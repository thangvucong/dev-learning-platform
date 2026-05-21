<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Services\Teacher\TeacherClassService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClassController extends Controller
{
    protected TeacherClassService $classService;

    public function __construct(TeacherClassService $classService)
    {
        $this->classService = $classService;
    }

    public function index(Request $request): View
    {
        $filters = [
            'q' => $request->query('q', ''),
            'status' => $request->query('status', ''),
        ];

        return view('pages.teacher.classes.index', $this->classService->buildList($request->user(), $filters));
    }

    public function show(Request $request, CourseClass $courseClass): View
    {
        return view('pages.teacher.classes.show', $this->classService->buildDetail($request->user(), $courseClass));
    }

    public function exportStudents(Request $request, CourseClass $courseClass): StreamedResponse
    {
        $payload = $this->classService->buildStudentExport($request->user(), $courseClass);
        $filename = 'class-' . $courseClass->id . '-students.csv';

        return Response::streamDownload(function () use ($payload) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Status', 'Assigned At', 'Attendance Rate']);

            foreach ($payload as $row) {
                fputcsv($handle, [
                    $row['id'],
                    $row['name'],
                    $row['email'],
                    $row['status'],
                    $row['assigned_at'],
                    $row['attendance_rate'] . '%',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
