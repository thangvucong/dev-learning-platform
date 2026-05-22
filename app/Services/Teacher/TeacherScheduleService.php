<?php

namespace App\Services\Teacher;

use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\AssignmentSubmission;
use App\Models\SessionAttendance;
use App\Models\SessionAssignment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;

class TeacherScheduleService
{
    /**
     * Build schedule page payload for an instructor.
     *
     * @param  \App\Models\User  $teacher
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function build(User $teacher, array $filters): array
    {
        $viewMode = in_array(($filters['view'] ?? 'week'), ['day', 'week', 'month', 'list'], true)
            ? (string) $filters['view']
            : 'week';
        $weekOffset = (int) ($filters['week_offset'] ?? 0);
        $classFilter = (int) ($filters['class_id'] ?? 0);
        $sessionId = (string) ($filters['session_id'] ?? '');

        $weekStart = now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd = (clone $weekStart)->endOfWeek(Carbon::SUNDAY);

        $allClasses = CourseClass::query()
            ->where('instructor_id', $teacher->id)
            ->with(['course'])
            ->orderBy('start_at')
            ->get();

        $classes = CourseClass::query()
            ->where('instructor_id', $teacher->id)
            ->with([
                'course',
                'sessions.attendances',
                'classEnrollments',
            ])
            ->orderBy('start_at')
            ->get();

        if ($classFilter > 0) {
            $classes = $classes->where('id', $classFilter)->values();
        }

        $sessions = $this->buildSessionsFromClasses($classes, $weekStart, $weekEnd);
        $selectedSession = $this->resolveSelectedSession($sessions, $sessionId);

        return [
            'header' => [
                'title' => 'Lịch giảng dạy',
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
            'classes' => $this->mapClassesForFilter($allClasses),
            'sessions' => $sessions,
            'selected_session' => $selectedSession,
            'sessions_total' => $sessions->count(),
            'upcoming_count' => $sessions->where('status', 'upcoming')->count(),
            'live_count' => $sessions->where('status', 'live')->count(),
            'completed_count' => $sessions->where('status', 'completed')->count(),
            'missed_count' => 0,
            'pending_attendance_count' => $sessions->filter(function (array $session) {
                return $session['status'] === 'completed'
                    && (int) $session['attendance_count'] === 0
                    && (int) $session['student_count'] > 0;
            })->count(),
        ];
    }

    protected function buildSessionsFromClasses(Collection $classes, Carbon $weekStart, Carbon $weekEnd): Collection
    {
        $sessions = $classes->flatMap(function (CourseClass $classItem) use ($weekStart, $weekEnd) {
            if ($classItem->relationLoaded('sessions') && $classItem->sessions->isNotEmpty()) {
                return $classItem->sessions
                    ->filter(function ($session) use ($weekStart, $weekEnd) {
                        return $session->start_at !== null && $session->start_at->between($weekStart, $weekEnd);
                    })
                    ->map(function ($session) use ($classItem) {
                        $startAt = $session->start_at;
                        $endAt = $session->end_at ?: (clone $startAt)->addHours(2);
                        $studentCount = $classItem->classEnrollments instanceof Collection
                            ? $classItem->classEnrollments->count()
                            : 0;
                        $attendanceCount = $session->attendances instanceof Collection
                            ? $session->attendances->count()
                            : 0;

                        return [
                            'id' => 'session-' . $session->id,
                            'session_id' => $session->id,
                            'class_id' => $classItem->id,
                            'class_name' => $classItem->name,
                            'teacher' => 'Điểm danh ' . $attendanceCount . '/' . $studentCount,
                            'course' => optional($classItem->course)->title ?: 'Khóa học',
                            'description' => $session->description ?: ('Buổi ' . $session->session_no . ' theo lịch giảng dạy.'),
                            'day_key' => $startAt->format('Y-m-d'),
                            'start_iso' => $startAt->toIso8601String(),
                            'end_iso' => $endAt->toIso8601String(),
                            'start_local' => $startAt->format('Y-m-d\TH:i:s'),
                            'end_local' => $endAt->format('Y-m-d\TH:i:s'),
                            'time' => $startAt->format('H:i') . ' - ' . $endAt->format('H:i'),
                            'start_at' => $startAt->format('d/m/Y H:i'),
                            'meeting_type' => $session->meeting_type ?: ($classItem->location ? 'offline' : 'zoom'),
                            'meeting_info' => $session->meeting_info ?: ($classItem->location ?: 'Online'),
                            'status' => $this->resolveSessionStatus($startAt, $endAt),
                            'relative' => $this->resolveRelativeText($startAt, $endAt),
                            'join_url' => (string) ($session->join_url ?: '#'),
                            'student_count' => $studentCount,
                            'attendance_count' => $attendanceCount,
                            'attendance_label' => $studentCount > 0 ? $attendanceCount . '/' . $studentCount : '0',
                        ];
                    });
            }

            if ($classItem->start_at !== null && $classItem->start_at->between($weekStart, $weekEnd)) {
                $startAt = $classItem->start_at;
                $endAt = $classItem->end_at ?: (clone $startAt)->addHours(2);
                $studentCount = $classItem->classEnrollments instanceof Collection
                    ? $classItem->classEnrollments->count()
                    : 0;

                return collect([[
                    'id' => 'cls-' . $classItem->id . '-' . $startAt->timestamp,
                    'session_id' => null,
                    'class_id' => $classItem->id,
                    'class_name' => $classItem->name,
                    'teacher' => 'Điểm danh 0/' . $studentCount,
                    'course' => optional($classItem->course)->title ?: 'Khóa học',
                    'description' => 'Buổi học tập trung theo lịch lớp.',
                    'day_key' => $startAt->format('Y-m-d'),
                    'start_iso' => $startAt->toIso8601String(),
                    'end_iso' => $endAt->toIso8601String(),
                    'start_local' => $startAt->format('Y-m-d\TH:i:s'),
                    'end_local' => $endAt->format('Y-m-d\TH:i:s'),
                    'time' => $startAt->format('H:i') . ' - ' . $endAt->format('H:i'),
                    'start_at' => $startAt->format('d/m/Y H:i'),
                    'meeting_type' => $classItem->location ? 'offline' : 'zoom',
                    'meeting_info' => $classItem->location ?: 'Online',
                    'status' => $this->resolveSessionStatus($startAt, $endAt),
                    'relative' => $this->resolveRelativeText($startAt, $endAt),
                    'join_url' => '#',
                    'student_count' => $studentCount,
                    'attendance_count' => 0,
                    'attendance_label' => $studentCount > 0 ? '0/' . $studentCount : '0',
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

    protected function resolveSelectedSession(Collection $sessions, string $sessionId): ?array
    {
        if ($sessionId !== '') {
            $found = $sessions->firstWhere('id', $sessionId);
            if ($found) {
                return $found;
            }
        }

        return $sessions->firstWhere('status', 'live')
            ?: $sessions->firstWhere('status', 'upcoming')
            ?: $sessions->first();
    }

    protected function mapClassesForFilter(Collection $classes): Collection
    {
        return $classes->map(function (CourseClass $classItem) {
            return [
                'id' => $classItem->id,
                'name' => $classItem->name,
            ];
        })->values();
    }

    public function buildAttendance(User $teacher, ClassSession $session): array
    {
        $session->load([
            'courseClass.course',
            'courseClass.classEnrollments.user',
            'attendances.student',
        ]);

        $courseClass = $this->authorizeSessionTeacher($teacher, $session);
        $enrollments = $courseClass->classEnrollments instanceof Collection
            ? $courseClass->classEnrollments
            : collect();
        $records = $session->attendances instanceof Collection
            ? $session->attendances->keyBy('student_id')
            : collect();

        $students = $enrollments
            ->filter(function ($enrollment) {
                return $enrollment->user !== null;
            })
            ->map(function ($enrollment) use ($records) {
                $student = $enrollment->user;
                $attendance = $records->get($student->id);

                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'status' => optional($attendance)->status ?: 'unmarked',
                    'checked_in_at' => optional(optional($attendance)->checked_in_at)->format('H:i d/m/Y'),
                ];
            })
            ->sortBy('name')
            ->values();

        return $this->attendancePayload($session, $courseClass, $students);
    }

    public function ensureClassSessionForAttendance(User $teacher, CourseClass $courseClass): array
    {
        abort_unless((int) $courseClass->instructor_id === (int) $teacher->id, 403);
        abort_unless($courseClass->start_at !== null, 422, 'Lớp chưa có thời gian học.');

        $session = ClassSession::query()
            ->where('class_id', $courseClass->id)
            ->where('start_at', $courseClass->start_at)
            ->first();

        if (!$session) {
            $nextSessionNo = ((int) ClassSession::query()
                ->where('class_id', $courseClass->id)
                ->max('session_no')) + 1;

            $session = ClassSession::query()->create([
                'class_id' => $courseClass->id,
                'session_no' => max(1, $nextSessionNo),
                'title' => 'Buổi ' . max(1, $nextSessionNo),
                'start_at' => $courseClass->start_at,
                'end_at' => $courseClass->end_at ?: (clone $courseClass->start_at)->addHours(2),
                'status' => ClassSession::STATUS_UPCOMING,
                'meeting_type' => $courseClass->location ? ClassSession::MEETING_OFFLINE : ClassSession::MEETING_ZOOM,
                'meeting_info' => $courseClass->location ?: 'Online',
                'description' => 'Buổi học được tạo từ lịch lớp để điểm danh.',
            ]);
        }

        return [
            'session_id' => $session->id,
            'attendance' => $this->buildAttendance($teacher, $session),
        ];
    }

    public function updateStudentAttendance(User $teacher, ClassSession $session, int $studentId, string $status): array
    {
        $session->load(['courseClass.classEnrollments.user', 'courseClass.course', 'attendances.student']);
        $courseClass = $this->authorizeSessionTeacher($teacher, $session);
        $this->authorizeStudentInClass($courseClass, $studentId);

        SessionAttendance::query()->updateOrCreate(
            [
                'class_session_id' => $session->id,
                'student_id' => $studentId,
            ],
            [
                'status' => $status,
                'checked_in_at' => in_array($status, [SessionAttendance::STATUS_PRESENT, SessionAttendance::STATUS_LATE], true) ? now() : null,
                'marked_by' => $teacher->id,
                'marked_by_role' => 'instructor',
            ]
        );

        return $this->buildAttendance($teacher, $session->fresh());
    }

    public function bulkUpdateAttendance(User $teacher, ClassSession $session, string $status): array
    {
        $session->load(['courseClass.classEnrollments', 'courseClass.course']);
        $courseClass = $this->authorizeSessionTeacher($teacher, $session);
        $studentIds = $courseClass->classEnrollments instanceof Collection
            ? $courseClass->classEnrollments->pluck('user_id')->filter()->values()
            : collect();

        foreach ($studentIds as $studentId) {
            SessionAttendance::query()->updateOrCreate(
                [
                    'class_session_id' => $session->id,
                    'student_id' => $studentId,
                ],
                [
                    'status' => $status,
                    'checked_in_at' => in_array($status, [SessionAttendance::STATUS_PRESENT, SessionAttendance::STATUS_LATE], true) ? now() : null,
                    'marked_by' => $teacher->id,
                    'marked_by_role' => 'instructor',
                ]
            );
        }

        return $this->buildAttendance($teacher, $session->fresh());
    }

    public function buildAssignments(User $teacher, ClassSession $session): array
    {
        $session->load(['courseClass.course']);
        $courseClass = $this->authorizeSessionTeacher($teacher, $session);

        $assignments = SessionAssignment::query()
            ->where('class_session_id', $session->id)
            ->withCount('submissions')
            ->withCount([
                'submissions as ungraded_submissions_count' => function ($query) {
                    $query->where('status', '!=', AssignmentSubmission::STATUS_RETURNED);
                },
            ])
            ->latest()
            ->get()
            ->map(function (SessionAssignment $assignment) {
                return $this->mapAssignment($assignment);
            })
            ->values();

        return [
            'session' => [
                'id' => $session->id,
                'title' => $session->title ?: 'Buổi ' . $session->session_no,
                'class_name' => $courseClass->name,
                'course' => optional($courseClass->course)->title ?: 'Khóa học',
            ],
            'summary' => [
                'total' => $assignments->count(),
                'published' => $assignments->where('status', SessionAssignment::STATUS_PUBLISHED)->count(),
                'draft' => $assignments->where('status', SessionAssignment::STATUS_DRAFT)->count(),
            ],
            'assignments' => $assignments,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAssignment(User $teacher, ClassSession $session, array $data, ?UploadedFile $attachment = null): array
    {
        $session->load(['courseClass.course']);
        $this->authorizeSessionTeacher($teacher, $session);

        $assignment = SessionAssignment::query()->create([
            'class_session_id' => $session->id,
            'teacher_id' => $teacher->id,
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'submission_type' => $data['submission_type'] ?? SessionAssignment::SUBMISSION_BOTH,
            'due_at' => $data['due_at'] ?? null,
            'status' => $data['status'] ?? SessionAssignment::STATUS_PUBLISHED,
        ]);

        if ($attachment) {
            $path = $attachment->store('session-assignments/' . $assignment->id);

            $assignment->update([
                'attachment_disk' => config('filesystems.default', 'local'),
                'attachment_path' => $path,
                'attachment_name' => $attachment->getClientOriginalName(),
                'attachment_mime' => $attachment->getClientMimeType(),
                'attachment_size' => $attachment->getSize(),
            ]);
        }

        return $this->buildAssignments($teacher, $session->fresh());
    }

    public function buildAssignmentSubmissions(User $teacher, SessionAssignment $assignment): array
    {
        $assignment->load([
            'session.courseClass.course',
            'session.courseClass.classEnrollments.user',
            'submissions.student',
            'submissions.grader',
        ]);

        $session = $assignment->session;
        $courseClass = $this->authorizeSessionTeacher($teacher, $session);
        $enrollments = $courseClass->classEnrollments instanceof Collection
            ? $courseClass->classEnrollments
            : collect();
        $records = $assignment->submissions instanceof Collection
            ? $assignment->submissions->keyBy('student_id')
            : collect();

        $submissions = $enrollments
            ->filter(function ($enrollment) {
                return $enrollment->user !== null;
            })
            ->map(function ($enrollment) use ($records, $assignment) {
                $student = $enrollment->user;
                $submission = $records->get($student->id);

                return $this->mapAssignmentSubmissionRow($student, $submission, $assignment);
            })
            ->sortBy('student_name')
            ->values();

        return [
            'assignment' => [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'class_name' => $courseClass->name,
                'course' => optional($courseClass->course)->title ?: 'Khóa học',
            ],
            'summary' => [
                'total' => $submissions->count(),
                'submitted' => $submissions->where('status', '!=', 'not_submitted')->count(),
                'not_submitted' => $submissions->where('status', 'not_submitted')->count(),
                'graded' => $submissions->where('status', AssignmentSubmission::STATUS_RETURNED)->count(),
                'ungraded' => $submissions->filter(function (array $row) {
                    return $row['submission_id'] !== null
                        && $row['status'] !== AssignmentSubmission::STATUS_RETURNED;
                })->count(),
                'late' => $submissions->where('is_late', true)->count(),
            ],
            'submissions' => $submissions,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function gradeSubmission(User $teacher, AssignmentSubmission $submission, array $data): array
    {
        $submission->load(['assignment.session.courseClass.classEnrollments', 'assignment.session.courseClass.course']);
        $assignment = $submission->assignment;
        $session = $assignment->session;
        $courseClass = $this->authorizeSessionTeacher($teacher, $session);
        $this->authorizeStudentInClass($courseClass, (int) $submission->student_id);

        $submission->update([
            'score' => $data['score'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'status' => AssignmentSubmission::STATUS_RETURNED,
            'graded_at' => now(),
            'graded_by' => $teacher->id,
        ]);

        return $this->buildAssignmentSubmissions($teacher, $assignment->fresh());
    }

    protected function authorizeSessionTeacher(User $teacher, ClassSession $session): CourseClass
    {
        $courseClass = $session->courseClass;

        abort_unless($courseClass && (int) $courseClass->instructor_id === (int) $teacher->id, 403);

        return $courseClass;
    }

    protected function authorizeStudentInClass(CourseClass $courseClass, int $studentId): void
    {
        $studentExists = $courseClass->classEnrollments instanceof Collection
            && $courseClass->classEnrollments->contains(function ($enrollment) use ($studentId) {
                return (int) $enrollment->user_id === (int) $studentId;
            });

        abort_unless($studentExists, 422, 'Học viên không thuộc lớp này.');
    }

    protected function mapAssignment(SessionAssignment $assignment): array
    {
        return [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'content' => $assignment->content,
            'submission_type' => $assignment->submission_type,
            'due_at' => optional($assignment->due_at)->format('d/m/Y H:i'),
            'status' => $assignment->status,
            'attachment_name' => $assignment->attachment_name,
            'attachment_size' => $assignment->attachment_size,
            'submissions_count' => (int) ($assignment->submissions_count ?? 0),
            'ungraded_submissions_count' => (int) ($assignment->ungraded_submissions_count ?? 0),
            'created_at' => optional($assignment->created_at)->format('d/m/Y H:i'),
        ];
    }

    protected function mapAssignmentSubmissionRow(User $student, ?AssignmentSubmission $submission, SessionAssignment $assignment): array
    {
        $isLate = $submission
            && $assignment->due_at
            && $submission->submitted_at
            && $submission->submitted_at->greaterThan($assignment->due_at);

        return [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'student_email' => $student->email,
            'submission_id' => optional($submission)->id,
            'status' => optional($submission)->status ?: 'not_submitted',
            'is_late' => (bool) ($isLate || optional($submission)->status === AssignmentSubmission::STATUS_LATE),
            'content' => optional($submission)->content,
            'attachment_name' => optional($submission)->attachment_name,
            'submitted_at' => optional(optional($submission)->submitted_at)->format('d/m/Y H:i'),
            'score' => optional($submission)->score,
            'feedback' => optional($submission)->feedback,
            'graded_at' => optional(optional($submission)->graded_at)->format('d/m/Y H:i'),
            'grader_name' => optional(optional($submission)->grader)->name,
        ];
    }

    protected function attendancePayload(ClassSession $session, CourseClass $courseClass, Collection $students): array
    {
        $marked = $students->where('status', '!=', 'unmarked')->count();
        $present = $students->whereIn('status', [SessionAttendance::STATUS_PRESENT, SessionAttendance::STATUS_LATE])->count();
        $total = $students->count();

        return [
            'session' => [
                'id' => $session->id,
                'title' => $session->title ?: 'Buổi ' . $session->session_no,
                'class_name' => $courseClass->name,
                'course' => optional($courseClass->course)->title ?: 'Khóa học',
                'start_at' => optional($session->start_at)->format('d/m/Y H:i') ?: '',
            ],
            'summary' => [
                'total' => $total,
                'marked' => $marked,
                'unmarked' => max(0, $total - $marked),
                'present' => $students->where('status', SessionAttendance::STATUS_PRESENT)->count(),
                'late' => $students->where('status', SessionAttendance::STATUS_LATE)->count(),
                'absent' => $students->where('status', SessionAttendance::STATUS_ABSENT)->count(),
                'excused' => $students->where('status', SessionAttendance::STATUS_EXCUSED)->count(),
                'rate' => $total > 0 ? (int) round(($present / $total) * 100) : 0,
            ],
            'students' => $students->values(),
        ];
    }
}
