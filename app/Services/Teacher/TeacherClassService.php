<?php

namespace App\Services\Teacher;

use App\Models\ClassSession;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Support\Collection;

class TeacherClassService
{
    /**
     * Build class list payload.
     *
     * @param  \App\Models\User  $teacher
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildList(User $teacher, array $filters): array
    {
        $classes = CourseClass::query()
            ->where('instructor_id', $teacher->id)
            ->with(['course', 'sessions.attendances'])
            ->withCount(['classEnrollments as students_count', 'sessions as sessions_count'])
            ->orderBy('start_at')
            ->get();

        $keyword = trim((string) ($filters['q'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        $mapped = $classes->map(function (CourseClass $courseClass) {
            return $this->mapClassCard($courseClass);
        });

        if ($keyword !== '') {
            $mapped = $mapped->filter(function (array $classItem) use ($keyword) {
                $haystack = mb_strtolower(implode(' ', [
                    $classItem['name'],
                    $classItem['code'],
                    $classItem['course_name'],
                ]));

                return mb_stripos($haystack, mb_strtolower($keyword)) !== false;
            })->values();
        }

        if ($status !== '' && in_array($status, ['upcoming', 'ongoing', 'completed', 'cancelled'], true)) {
            $mapped = $mapped->where('status', $status)->values();
        }

        return [
            'filters' => [
                'q' => $keyword,
                'status' => $status,
            ],
            'classes' => $mapped,
            'stats' => [
                'total' => $mapped->count(),
                'ongoing' => $mapped->where('status', 'ongoing')->count(),
                'upcoming' => $mapped->where('status', 'upcoming')->count(),
                'completed' => $mapped->where('status', 'completed')->count(),
            ],
        ];
    }

    /**
     * Build one class detail payload.
     *
     * @param  \App\Models\User  $teacher
     * @param  \App\Models\CourseClass  $courseClass
     * @return array<string, mixed>
     */
    public function buildDetail(User $teacher, CourseClass $courseClass): array
    {
        $this->authorizeTeacher($teacher, $courseClass);

        $courseClass->load([
            'course',
            'instructor',
            'classEnrollments.user',
            'sessions.attendances.student',
        ])->loadCount(['classEnrollments as students_count', 'sessions as sessions_count']);

        $sessions = $courseClass->sessions instanceof Collection ? $courseClass->sessions->sortBy('start_at')->values() : collect();
        $students = $courseClass->classEnrollments instanceof Collection ? $courseClass->classEnrollments : collect();
        $nextSession = $this->resolveNextSession($sessions);
        $nearestAttendanceSession = $this->resolveNearestAttendanceSession($sessions);
        $attendanceSummary = $this->classAttendanceSummary($courseClass);
        $progress = $this->calculateClassProgress($sessions, $courseClass->status);

        return [
            'class' => [
                'id' => $courseClass->id,
                'name' => $courseClass->name,
                'code' => $courseClass->code,
                'course_name' => optional($courseClass->course)->title ?: 'Khóa học',
                'description' => optional($courseClass->course)->description ?: 'Lớp học đang được phụ trách.',
                'status' => $courseClass->status,
                'mode' => $courseClass->mode,
                'capacity' => (int) $courseClass->capacity,
                'location' => $courseClass->location ?: 'Online',
                'start_at' => optional($courseClass->start_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
                'end_at' => optional($courseClass->end_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
                'students_count' => (int) $courseClass->students_count,
                'sessions_count' => (int) $courseClass->sessions_count,
                'progress' => $progress,
                'attendance_rate' => $attendanceSummary['rate'],
                'next_session' => optional(optional($nextSession)->start_at)->format('d/m/Y H:i') ?: 'Chưa có lịch',
                'nearest_attendance_session_id' => optional($nearestAttendanceSession)->id,
            ],
            'overview' => [
                'completed_sessions' => $sessions->filter(fn ($session) => $this->isCompletedSession($session))->count(),
                'remaining_sessions' => $sessions->filter(fn ($session) => !$this->isCompletedSession($session))->count(),
                'present_count' => $attendanceSummary['present'],
                'late_count' => $attendanceSummary['late'],
                'absent_count' => $attendanceSummary['absent'],
            ],
            'students' => $students->map(function ($enrollment) use ($courseClass) {
                return $this->mapStudent($enrollment, $courseClass);
            })->values(),
            'sessions' => $sessions->map(function (ClassSession $session) use ($courseClass) {
                return $this->mapSession($session, $courseClass);
            })->values(),
            'attendance_sessions' => $sessions->map(function (ClassSession $session) use ($courseClass) {
                return $this->mapAttendanceSession($session, $courseClass);
            })->values(),
        ];
    }

    /**
     * Build CSV export rows.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function buildStudentExport(User $teacher, CourseClass $courseClass): Collection
    {
        $detail = $this->buildDetail($teacher, $courseClass);

        return collect($detail['students']);
    }

    protected function authorizeTeacher(User $teacher, CourseClass $courseClass): void
    {
        abort_unless((int) $courseClass->instructor_id === (int) $teacher->id, 403);
    }

    protected function mapClassCard(CourseClass $courseClass): array
    {
        $sessions = $courseClass->sessions instanceof Collection ? $courseClass->sessions : collect();
        $nextSession = $this->resolveNextSession($sessions);
        $attendance = $this->classAttendanceSummary($courseClass);

        return [
            'id' => $courseClass->id,
            'name' => $courseClass->name,
            'code' => $courseClass->code,
            'course_name' => optional($courseClass->course)->title ?: 'Khóa học',
            'status' => $courseClass->status,
            'students_count' => (int) $courseClass->students_count,
            'sessions_count' => (int) $courseClass->sessions_count,
            'progress' => $this->calculateClassProgress($sessions, $courseClass->status),
            'attendance_rate' => $attendance['rate'],
            'next_session' => optional(optional($nextSession)->start_at)->format('d/m H:i') ?: 'Chưa có lịch',
        ];
    }

    protected function mapStudent($enrollment, CourseClass $courseClass): array
    {
        $student = $enrollment->user;
        $records = $this->attendanceRecords($courseClass)
            ->filter(function ($attendance) use ($student) {
                return (int) $attendance->student_id === (int) optional($student)->id;
            })
            ->values();
        $attended = $records->filter(function ($attendance) {
            return in_array((string) $attendance->status, ['present', 'late'], true);
        })->count();

        return [
            'id' => optional($student)->id,
            'name' => optional($student)->name ?: 'Học viên',
            'email' => optional($student)->email ?: '',
            'avatar' => optional($student)->avatar_url,
            'status' => (string) $enrollment->status,
            'assigned_at' => optional($enrollment->assigned_at)->format('d/m/Y') ?: '',
            'attendance_rate' => $records->isNotEmpty() ? (int) round(($attended / $records->count()) * 100) : 0,
            'present' => $records->where('status', 'present')->count(),
            'late' => $records->where('status', 'late')->count(),
            'absent' => $records->where('status', 'absent')->count(),
        ];
    }

    protected function mapSession(ClassSession $session, CourseClass $courseClass): array
    {
        return [
            'id' => $session->id,
            'title' => $session->title ?: 'Buổi ' . $session->session_no,
            'session_no' => (int) $session->session_no,
            'start_at' => optional($session->start_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
            'end_at' => optional($session->end_at)->format('H:i') ?: '',
            'status' => $this->resolveSessionStatus($session),
            'meeting_type' => $session->meeting_type ?: $courseClass->mode,
            'meeting_info' => $session->meeting_info ?: $courseClass->location ?: 'Online',
            'join_url' => (string) ($session->join_url ?: ''),
        ];
    }

    protected function mapAttendanceSession(ClassSession $session, CourseClass $courseClass): array
    {
        $records = $session->attendances instanceof Collection ? $session->attendances : collect();
        $studentCount = $courseClass->classEnrollments instanceof Collection ? $courseClass->classEnrollments->count() : 0;
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $excused = $records->where('status', 'excused')->count();

        return [
            'id' => $session->id,
            'title' => $session->title ?: 'Buổi ' . $session->session_no,
            'start_at' => optional($session->start_at)->format('d/m/Y H:i') ?: 'Đang cập nhật',
            'status' => $this->resolveSessionStatus($session),
            'student_count' => $studentCount,
            'recorded_count' => $records->count(),
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'excused' => $excused,
            'rate' => $records->isNotEmpty() ? (int) round((($present + $late) / $records->count()) * 100) : 0,
        ];
    }

    protected function classAttendanceSummary(CourseClass $courseClass): array
    {
        $records = $this->attendanceRecords($courseClass);
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $attended = $present + $late;

        return [
            'rate' => $records->isNotEmpty() ? (int) round(($attended / $records->count()) * 100) : 0,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
        ];
    }

    protected function attendanceRecords(CourseClass $courseClass): Collection
    {
        $sessions = $courseClass->sessions instanceof Collection ? $courseClass->sessions : collect();

        return $sessions
            ->flatMap(function ($session) {
                return $session->attendances instanceof Collection ? $session->attendances : collect();
            })
            ->values();
    }

    protected function calculateClassProgress(Collection $sessions, string $status): int
    {
        if ($sessions->isEmpty()) {
            return $status === CourseClass::STATUS_COMPLETED ? 100 : 0;
        }

        $completed = $sessions->filter(fn ($session) => $this->isCompletedSession($session))->count();

        return (int) round(($completed / max(1, $sessions->count())) * 100);
    }

    protected function isCompletedSession(ClassSession $session): bool
    {
        if ($session->status === ClassSession::STATUS_COMPLETED) {
            return true;
        }

        return $session->end_at !== null && $session->end_at->isPast();
    }

    protected function resolveSessionStatus(ClassSession $session): string
    {
        if ($session->status === ClassSession::STATUS_CANCELLED) {
            return 'cancelled';
        }

        if ($session->start_at && $session->start_at->isFuture()) {
            return 'upcoming';
        }

        if ($session->start_at && $session->end_at && $session->start_at->lessThanOrEqualTo(now()) && $session->end_at->greaterThanOrEqualTo(now())) {
            return 'live';
        }

        return 'completed';
    }

    protected function resolveNextSession(Collection $sessions)
    {
        return $sessions
            ->filter(fn ($session) => $session->start_at !== null && $session->start_at->isFuture())
            ->sortBy('start_at')
            ->first();
    }

    protected function resolveNearestAttendanceSession(Collection $sessions)
    {
        return $sessions
            ->filter(fn ($session) => $session->end_at !== null && $session->end_at->isPast())
            ->sortByDesc('end_at')
            ->first();
    }
}
