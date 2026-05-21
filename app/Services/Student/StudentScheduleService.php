<?php

namespace App\Services\Student;

use App\Models\AssignmentSubmission;
use App\Models\ClassSession;
use App\Models\SessionAssignment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StudentScheduleService
{
    /**
     * Build schedule page payload.
     *
     * @param  \App\Models\User  $user
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(User $user, array $filters): array
    {
        $viewMode = in_array(($filters['view'] ?? 'week'), ['day', 'week', 'month', 'list'], true)
            ? (string) $filters['view']
            : 'week';
        $weekOffset = (int) ($filters['week_offset'] ?? 0);
        $classFilter = (int) ($filters['class_id'] ?? 0);
        $sessionId = (string) ($filters['session_id'] ?? '');

        $weekStart = now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd = (clone $weekStart)->endOfWeek(Carbon::SUNDAY);

        $user->loadMissing([
            'assignedClasses' => function ($query) {
                $query->with(['course', 'instructor', 'sessions'])->orderBy('start_at');
            },
        ]);

        $classes = $user->assignedClasses instanceof Collection ? $user->assignedClasses : collect();
        if ($classFilter > 0) {
            $classes = $classes->where('id', $classFilter)->values();
        }

        $sessions = $this->buildSessionsFromClasses($classes, $weekStart, $weekEnd);

        $selectedSession = $this->resolveSelectedSession($sessions, $sessionId);

        return [
            'header' => [
                'title' => 'Lịch học',
                'week_range' => $weekStart->format('d/m') . ' - ' . $weekEnd->format('d/m/Y'),
                'week_offset' => $weekOffset,
                'view_mode' => $viewMode,
                'week_start' => $weekStart->toDateString(),
            ],
            'filters' => [
                'class_id' => $classFilter,
                'view' => $viewMode,
                'week_offset' => $weekOffset,
                'session_id' => $sessionId,
            ],
            'classes' => $this->mapClassesForFilter($user->assignedClasses ?? collect()),
            'sessions' => $sessions,
            'selected_session' => $selectedSession,
            'sessions_total' => $sessions->count(),
            'upcoming_count' => $sessions->where('status', 'upcoming')->count(),
            'live_count' => $sessions->where('status', 'live')->count(),
            'completed_count' => $sessions->where('status', 'completed')->count(),
            'missed_count' => $sessions->where('status', 'missed')->count(),
        ];
    }

    /**
     * Build weekly sessions from class records.
     *
     * @param  \Illuminate\Support\Collection  $classes
     * @param  \Illuminate\Support\Carbon  $weekStart
     * @param  \Illuminate\Support\Carbon  $weekEnd
     * @return \Illuminate\Support\Collection
     */
    protected function buildSessionsFromClasses(Collection $classes, Carbon $weekStart, Carbon $weekEnd): Collection
    {
        $sessions = $classes->flatMap(function ($classItem) use ($weekStart, $weekEnd) {
            if ($classItem->relationLoaded('sessions') && $classItem->sessions->isNotEmpty()) {
                return $classItem->sessions
                    ->filter(function ($session) use ($weekStart, $weekEnd) {
                        return $session->start_at !== null && $session->start_at->between($weekStart, $weekEnd);
                    })
                    ->map(function ($session) use ($classItem) {
                        $startAt = $session->start_at;
                        $endAt = $session->end_at ?: (clone $startAt)->addHours(2);

                        return [
                            'id' => 'session-' . $session->id,
                            'session_id' => $session->id,
                            'class_id' => $classItem->id,
                            'class_name' => $classItem->name,
                            'teacher' => optional($classItem->instructor)->name ?: 'Giảng viên',
                            'course' => optional($classItem->course)->title ?: 'Khóa học',
                            'description' => $session->description ?: ('Buổi ' . $session->session_no . ' theo lộ trình lớp học.'),
                            'day_key' => $startAt->format('Y-m-d'),
                            'start_iso' => $startAt->toIso8601String(),
                            'end_iso' => $endAt->toIso8601String(),
                            'start_local' => $startAt->format('Y-m-d\TH:i:s'),
                            'end_local' => $endAt->format('Y-m-d\TH:i:s'),
                            'time' => $startAt->format('H:i') . ' - ' . $endAt->format('H:i'),
                            'start_at' => $startAt->format('d/m/Y H:i'),
                            'meeting_type' => $session->meeting_type ?: ($classItem->location ? 'offline' : 'zoom'),
                            'meeting_info' => $session->meeting_info ?: ($classItem->location ?: 'Zoom meeting'),
                            'status' => $this->resolveSessionStatus($startAt, $endAt),
                            'relative' => $this->resolveRelativeText($startAt, $endAt),
                            'join_url' => (string) ($session->join_url ?: '#'),
                        ];
                    });
            }

            if ($classItem->start_at !== null && $classItem->start_at->between($weekStart, $weekEnd)) {
                $startAt = $classItem->start_at;
                $endAt = $classItem->end_at ?: (clone $startAt)->addHours(2);

                return collect([[
                    'id' => 'cls-' . $classItem->id . '-' . $startAt->timestamp,
                    'session_id' => null,
                    'class_id' => $classItem->id,
                    'class_name' => $classItem->name,
                    'teacher' => optional($classItem->instructor)->name ?: 'Giảng viên',
                    'course' => optional($classItem->course)->title ?: 'Khóa học',
                    'description' => 'Buổi học tập trung theo lộ trình lớp học.',
                    'day_key' => $startAt->format('Y-m-d'),
                    'start_iso' => $startAt->toIso8601String(),
                    'end_iso' => $endAt->toIso8601String(),
                    'start_local' => $startAt->format('Y-m-d\TH:i:s'),
                    'end_local' => $endAt->format('Y-m-d\TH:i:s'),
                    'time' => $startAt->format('H:i') . ' - ' . $endAt->format('H:i'),
                    'start_at' => $startAt->format('d/m/Y H:i'),
                    'meeting_type' => $classItem->location ? 'offline' : 'zoom',
                    'meeting_info' => $classItem->location ?: 'Zoom meeting',
                    'status' => $this->resolveSessionStatus($startAt, $endAt),
                    'relative' => $this->resolveRelativeText($startAt, $endAt),
                    'join_url' => '#',
                ]]);
            }

            return collect();
        });

        return $sessions
            ->sortBy(function (array $session) {
                return $session['start_iso'];
            })
            ->values();
    }

    /**
     * Resolve status of one session.
     *
     * @param  \Illuminate\Support\Carbon  $startAt
     * @param  \Illuminate\Support\Carbon  $endAt
     * @return string
     */
    protected function resolveSessionStatus(Carbon $startAt, Carbon $endAt): string
    {
        if ($startAt->isFuture()) {
            return 'upcoming';
        }

        if ($startAt->isPast() && $endAt->isFuture()) {
            return 'live';
        }

        return 'completed';
    }

    /**
     * Resolve relative time string.
     *
     * @param  \Illuminate\Support\Carbon  $startAt
     * @param  \Illuminate\Support\Carbon  $endAt
     * @return string
     */
    protected function resolveRelativeText(Carbon $startAt, Carbon $endAt): string
    {
        if ($startAt->isFuture()) {
            return 'Còn ' . now()->diffInMinutes($startAt) . ' phút';
        }

        if ($endAt->isFuture()) {
            return 'Đang diễn ra';
        }

        return 'Đã kết thúc';
    }

    /**
     * Resolve selected session item.
     *
     * @param  \Illuminate\Support\Collection  $sessions
     * @param  string  $sessionId
     * @return array<string, mixed>|null
     */
    protected function resolveSelectedSession(Collection $sessions, string $sessionId): ?array
    {
        if ($sessionId !== '') {
            $found = $sessions->firstWhere('id', $sessionId);
            if ($found) {
                return $found;
            }
        }

        $live = $sessions->firstWhere('status', 'live');
        if ($live) {
            return $live;
        }

        $upcoming = $sessions->firstWhere('status', 'upcoming');
        if ($upcoming) {
            return $upcoming;
        }

        return $sessions->first();
    }

    /**
     * Map classes for filter dropdown.
     *
     * @param  \Illuminate\Support\Collection  $classes
     * @return \Illuminate\Support\Collection
     */
    protected function mapClassesForFilter(Collection $classes): Collection
    {
        return $classes->map(function ($classItem) {
            return [
                'id' => $classItem->id,
                'name' => $classItem->name,
            ];
        })->values();
    }

    public function buildAssignments(User $student, ClassSession $session): array
    {
        $session->load(['courseClass.course']);
        $this->authorizeStudentSession($student, $session);

        $assignments = SessionAssignment::query()
            ->where('class_session_id', $session->id)
            ->where('status', SessionAssignment::STATUS_PUBLISHED)
            ->with(['submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->latest()
            ->get()
            ->map(function (SessionAssignment $assignment) {
                return $this->mapStudentAssignment($assignment);
            })
            ->values();

        return [
            'session' => [
                'id' => $session->id,
                'title' => $session->title ?: 'Buổi ' . $session->session_no,
                'class_name' => $session->courseClass->name,
                'course' => optional($session->courseClass->course)->title ?: 'Khóa học',
            ],
            'summary' => [
                'total' => $assignments->count(),
                'submitted' => $assignments->filter(function (array $assignment) {
                    return $assignment['submission'] !== null;
                })->count(),
            ],
            'assignments' => $assignments,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function submitAssignment(User $student, SessionAssignment $assignment, array $data, ?UploadedFile $attachment = null): array
    {
        $assignment->load(['session.courseClass.course']);
        $session = $assignment->session;
        $this->authorizeStudentSession($student, $session);
        abort_unless($assignment->status === SessionAssignment::STATUS_PUBLISHED, 422, 'Bài tập chưa được giao.');

        $requiresText = in_array($assignment->submission_type, [SessionAssignment::SUBMISSION_TEXT, SessionAssignment::SUBMISSION_BOTH], true);
        $requiresFile = in_array($assignment->submission_type, [SessionAssignment::SUBMISSION_FILE, SessionAssignment::SUBMISSION_BOTH], true);

        if ($assignment->submission_type === SessionAssignment::SUBMISSION_TEXT) {
            abort_unless(trim((string) ($data['content'] ?? '')) !== '', 422, 'Vui lòng nhập nội dung bài nộp.');
        }

        if ($assignment->submission_type === SessionAssignment::SUBMISSION_FILE) {
            abort_unless($attachment !== null, 422, 'Vui lòng đính kèm file bài nộp.');
        }

        if ($assignment->submission_type === SessionAssignment::SUBMISSION_BOTH) {
            abort_unless(trim((string) ($data['content'] ?? '')) !== '' || $attachment !== null, 422, 'Vui lòng nhập nội dung hoặc đính kèm file bài nộp.');
        }

        abort_unless($requiresText || $requiresFile, 422, 'Kiểu nộp bài không hợp lệ.');

        $status = $assignment->due_at && now()->greaterThan($assignment->due_at)
            ? AssignmentSubmission::STATUS_LATE
            : AssignmentSubmission::STATUS_SUBMITTED;

        $submission = AssignmentSubmission::query()->updateOrCreate(
            [
                'session_assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'content' => $data['content'] ?? null,
                'submitted_at' => now(),
                'status' => $status,
            ]
        );

        if ($attachment) {
            $path = $attachment->store('assignment-submissions/' . $assignment->id . '/' . $student->id);

            $submission->update([
                'attachment_disk' => config('filesystems.default', 'local'),
                'attachment_path' => $path,
                'attachment_name' => $attachment->getClientOriginalName(),
                'attachment_mime' => $attachment->getClientMimeType(),
                'attachment_size' => $attachment->getSize(),
            ]);
        }

        return $this->buildAssignments($student, $session->fresh());
    }

    protected function authorizeStudentSession(User $student, ClassSession $session): void
    {
        $session->loadMissing('courseClass.classEnrollments');
        $courseClass = $session->courseClass;

        $isEnrolled = $courseClass
            && $courseClass->classEnrollments instanceof Collection
            && $courseClass->classEnrollments->contains(function ($enrollment) use ($student) {
                return (int) $enrollment->user_id === (int) $student->id;
            });

        abort_unless($isEnrolled, 403);
    }

    protected function mapStudentAssignment(SessionAssignment $assignment): array
    {
        $submission = $assignment->submissions->first();

        return [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'content' => $assignment->content,
            'submission_type' => $assignment->submission_type,
            'due_at' => optional($assignment->due_at)->format('d/m/Y H:i'),
            'status' => $assignment->status,
            'attachment_name' => $assignment->attachment_name,
            'attachment_size' => $assignment->attachment_size,
            'created_at' => optional($assignment->created_at)->format('d/m/Y H:i'),
            'submission' => $submission ? [
                'id' => $submission->id,
                'content' => $submission->content,
                'attachment_name' => $submission->attachment_name,
                'submitted_at' => optional($submission->submitted_at)->format('d/m/Y H:i'),
                'status' => $submission->status,
                'score' => $submission->score,
                'feedback' => $submission->feedback,
            ] : null,
        ];
    }
}
