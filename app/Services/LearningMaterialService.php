<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\LearningMaterial;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LearningMaterialService
{
    protected GoogleDriveDocumentStorage $storage;

    public function __construct(GoogleDriveDocumentStorage $storage)
    {
        $this->storage = $storage;
    }

    public function buildTeacherIndex(User $teacher, array $filters): array
    {
        $classes = $this->teacherClasses($teacher);
        $classId = (int) ($filters['class_id'] ?? 0);
        $sessionId = (int) ($filters['class_session_id'] ?? 0);

        $materials = LearningMaterial::query()
            ->where('status', LearningMaterial::STATUS_ACTIVE)
            ->whereHas('courseClass', function ($query) use ($teacher) {
                $query->where('instructor_id', $teacher->id);
            })
            ->with(['courseClass.course', 'session', 'uploader'])
            ->when($classId > 0, function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->when($sessionId > 0, function ($query) use ($sessionId) {
                $query->where('class_session_id', $sessionId);
            })
            ->latest('published_at')
            ->latest()
            ->get()
            ->map(fn (LearningMaterial $material) => $this->mapMaterial($material, 'teacher'))
            ->values();

        return [
            'filters' => [
                'class_id' => $classId,
                'class_session_id' => $sessionId,
            ],
            'classes' => $classes->map(fn (CourseClass $classItem) => $this->mapClassOption($classItem))->values(),
            'sessions' => $this->sessionsForSelectedClass($classes, $classId),
            'materials' => $materials,
        ];
    }

    public function buildStudentIndex(User $student, array $filters): array
    {
        $classes = $this->studentClasses($student);
        $classIds = $classes->pluck('id')->map(fn ($id) => (int) $id)->all();
        $classId = (int) ($filters['class_id'] ?? 0);
        $sessionId = (int) ($filters['class_session_id'] ?? 0);

        abort_if($classId > 0 && !in_array($classId, $classIds, true), 403);

        $materials = LearningMaterial::query()
            ->where('status', LearningMaterial::STATUS_ACTIVE)
            ->whereIn('class_id', $classIds)
            ->with(['courseClass.course', 'session', 'uploader'])
            ->when($classId > 0, function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->when($sessionId > 0, function ($query) use ($sessionId) {
                $query->where('class_session_id', $sessionId);
            })
            ->latest('published_at')
            ->latest()
            ->get()
            ->map(fn (LearningMaterial $material) => $this->mapMaterial($material, 'student'))
            ->values();

        return [
            'filters' => [
                'class_id' => $classId,
                'class_session_id' => $sessionId,
            ],
            'classes' => $classes->map(fn (CourseClass $classItem) => $this->mapClassOption($classItem))->values(),
            'sessions' => $this->sessionsForSelectedClass($classes, $classId),
            'materials' => $materials,
        ];
    }

    public function createTeacherMaterial(User $teacher, array $data, UploadedFile $file): LearningMaterial
    {
        $courseClass = CourseClass::query()->with('course')->findOrFail((int) $data['class_id']);
        $this->authorizeTeacherClass($teacher, $courseClass);

        $session = null;
        if (!empty($data['class_session_id'])) {
            $session = ClassSession::query()->where('class_id', $courseClass->id)->findOrFail((int) $data['class_session_id']);
        }

        $uploaded = $this->storage->upload($file, $courseClass, $session);

        try {
            return DB::transaction(function () use ($teacher, $data, $file, $courseClass, $session, $uploaded) {
                return LearningMaterial::query()->create([
                    'class_id' => $courseClass->id,
                    'class_session_id' => optional($session)->id,
                    'uploaded_by' => $teacher->id,
                    'title' => trim((string) ($data['title'] ?? '')) ?: $file->getClientOriginalName(),
                    'description' => $data['description'] ?? null,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => (int) $file->getSize(),
                    'drive_file_id' => $uploaded['drive_file_id'],
                    'drive_folder_id' => $uploaded['drive_folder_id'],
                    'status' => LearningMaterial::STATUS_ACTIVE,
                    'published_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            try {
                $this->storage->delete($uploaded['drive_file_id']);
            } catch (\Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }
    }

    public function archiveTeacherMaterial(User $teacher, LearningMaterial $material): void
    {
        $material->load('courseClass');
        $this->authorizeTeacherClass($teacher, $material->courseClass);

        $material->update([
            'status' => LearningMaterial::STATUS_ARCHIVED,
        ]);
    }

    public function teacherDownload(User $teacher, LearningMaterial $material): string
    {
        $material->load('courseClass');
        $this->authorizeTeacherClass($teacher, $material->courseClass);

        abort_unless($material->status === LearningMaterial::STATUS_ACTIVE, 404);

        return $this->storage->download($material->drive_file_id);
    }

    public function studentDownload(User $student, LearningMaterial $material): string
    {
        $material->load('courseClass');
        $this->authorizeStudentClass($student, $material->courseClass);

        abort_unless($material->status === LearningMaterial::STATUS_ACTIVE, 404);

        return $this->storage->download($material->drive_file_id);
    }

    public function teacherSessionOptions(User $teacher, CourseClass $courseClass): Collection
    {
        $this->authorizeTeacherClass($teacher, $courseClass);

        return $courseClass->sessions()->get()->map(fn (ClassSession $session) => $this->mapSessionOption($session))->values();
    }

    public function studentSessionOptions(User $student, CourseClass $courseClass): Collection
    {
        $this->authorizeStudentClass($student, $courseClass);

        return $courseClass->sessions()->get()->map(fn (ClassSession $session) => $this->mapSessionOption($session))->values();
    }

    protected function teacherClasses(User $teacher): Collection
    {
        return CourseClass::query()
            ->where('instructor_id', $teacher->id)
            ->with(['course', 'sessions'])
            ->orderBy('start_at')
            ->get();
    }

    protected function studentClasses(User $student): Collection
    {
        return $student->assignedClasses()
            ->with(['course', 'sessions'])
            ->orderBy('start_at')
            ->get();
    }

    protected function sessionsForSelectedClass(Collection $classes, int $classId): Collection
    {
        if ($classId <= 0) {
            return collect();
        }

        $classItem = $classes->firstWhere('id', $classId);
        if (!$classItem || !$classItem->relationLoaded('sessions')) {
            return collect();
        }

        return $classItem->sessions->map(fn (ClassSession $session) => $this->mapSessionOption($session))->values();
    }

    protected function authorizeTeacherClass(User $teacher, ?CourseClass $courseClass): void
    {
        abort_unless($courseClass && (int) $courseClass->instructor_id === (int) $teacher->id, 403);
    }

    protected function authorizeStudentClass(User $student, ?CourseClass $courseClass): void
    {
        if (!$courseClass) {
            abort(403);
        }

        $exists = $student->assignedClasses()
            ->where('classes.id', $courseClass->id)
            ->exists();

        abort_unless($exists, 403);
    }

    protected function mapClassOption(CourseClass $classItem): array
    {
        return [
            'id' => $classItem->id,
            'name' => $classItem->name,
            'code' => $classItem->code,
            'course_name' => optional($classItem->course)->title ?: 'Khóa học',
        ];
    }

    protected function mapSessionOption(ClassSession $session): array
    {
        return [
            'id' => $session->id,
            'label' => ($session->title ?: 'Buổi ' . $session->session_no) . ' · ' . (optional($session->start_at)->format('d/m/Y H:i') ?: 'Chưa có giờ'),
        ];
    }

    protected function mapMaterial(LearningMaterial $material, string $actor): array
    {
        $downloadRoute = $actor === 'teacher' ? 'teacher.materials.download' : 'user.materials.download';

        return [
            'id' => $material->id,
            'title' => $material->title,
            'description' => $material->description,
            'class_name' => optional($material->courseClass)->name ?: 'Lớp học',
            'course_name' => optional(optional($material->courseClass)->course)->title ?: 'Khóa học',
            'session_label' => $material->session ? ($material->session->title ?: 'Buổi ' . $material->session->session_no) : 'Tài liệu lớp',
            'original_name' => $material->original_name,
            'mime_type' => $material->mime_type,
            'size' => $material->size,
            'size_label' => $this->formatBytes((int) $material->size),
            'published_at' => optional($material->published_at)->format('d/m/Y H:i'),
            'uploader_name' => optional($material->uploader)->name ?: 'Giảng viên',
            'download_url' => route($downloadRoute, $material),
            'delete_url' => $actor === 'teacher' ? route('teacher.materials.destroy', $material) : null,
            'icon' => $this->fileIcon($material->original_name, $material->mime_type),
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    protected function fileIcon(string $name, ?string $mimeType): string
    {
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (in_array($extension, ['pdf'], true)) {
            return 'fa-regular fa-file-pdf';
        }

        if (in_array($extension, ['doc', 'docx'], true)) {
            return 'fa-regular fa-file-word';
        }

        if (in_array($extension, ['xls', 'xlsx'], true)) {
            return 'fa-regular fa-file-excel';
        }

        if (in_array($extension, ['ppt', 'pptx'], true)) {
            return 'fa-regular fa-file-powerpoint';
        }

        if (in_array($extension, ['png', 'jpg', 'jpeg'], true) || strpos((string) $mimeType, 'image/') === 0) {
            return 'fa-regular fa-file-image';
        }

        if ($extension === 'zip') {
            return 'fa-regular fa-file-zipper';
        }

        return 'fa-regular fa-file-lines';
    }
}
