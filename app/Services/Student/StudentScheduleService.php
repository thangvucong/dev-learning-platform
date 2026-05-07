<?php

namespace App\Services\Student;

use App\Models\User;
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
                $query->with(['course.instructor', 'sessions'])->orderBy('start_at');
            },
        ]);

        $classes = $user->assignedClasses instanceof Collection ? $user->assignedClasses : collect();
        if ($classFilter > 0) {
            $classes = $classes->where('id', $classFilter)->values();
        }

        $sessions = $this->buildSessionsFromClasses($classes, $weekStart, $weekEnd);
        if ($sessions->isEmpty()) {
            $sessions = $this->buildMockSessions($weekStart, $weekEnd);
        }

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
                            'class_id' => $classItem->id,
                            'class_name' => $classItem->name,
                            'teacher' => optional(optional($classItem->course)->instructor)->name ?: 'Giảng viên',
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
                    'class_id' => $classItem->id,
                    'class_name' => $classItem->name,
                    'teacher' => optional(optional($classItem->course)->instructor)->name ?: 'Giảng viên',
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
     * Build mock sessions when database has no records.
     *
     * @param  \Illuminate\Support\Carbon  $weekStart
     * @param  \Illuminate\Support\Carbon  $weekEnd
     * @return \Illuminate\Support\Collection
     */
    protected function buildMockSessions(Carbon $weekStart, Carbon $weekEnd): Collection
    {
        $mock = collect([
            ['offset' => 1, 'hour' => 19, 'name' => 'PHP Backend Intensive', 'teacher' => 'Nguyen Van Teacher 1', 'status' => 'upcoming'],
            ['offset' => 2, 'hour' => 20, 'name' => 'Laravel Architecture', 'teacher' => 'Tran Thi Teacher 2', 'status' => 'live'],
            ['offset' => 4, 'hour' => 19, 'name' => 'System Design Practice', 'teacher' => 'Le Van Teacher 3', 'status' => 'completed'],
            ['offset' => 5, 'hour' => 18, 'name' => 'Mock Interview Session', 'teacher' => 'Nguyen Van Teacher 1', 'status' => 'missed'],
        ]);

        return $mock->map(function (array $item, int $index) use ($weekStart) {
            $startAt = (clone $weekStart)->addDays($item['offset'])->setTime($item['hour'], 0);
            $endAt = (clone $startAt)->addHours(2);

            return [
                'id' => 'mock-' . $index,
                'class_id' => 0,
                'class_name' => $item['name'],
                'teacher' => $item['teacher'],
                'course' => 'Mock learning class',
                'description' => 'Dữ liệu mẫu để demo timeline học tập.',
                'day_key' => $startAt->format('Y-m-d'),
                'start_iso' => $startAt->toIso8601String(),
                'end_iso' => $endAt->toIso8601String(),
                'start_local' => $startAt->format('Y-m-d\TH:i:s'),
                'end_local' => $endAt->format('Y-m-d\TH:i:s'),
                'time' => $startAt->format('H:i') . ' - ' . $endAt->format('H:i'),
                'start_at' => $startAt->format('d/m/Y H:i'),
                'meeting_type' => 'zoom',
                'meeting_info' => 'https://zoom.us/j/123456789',
                'status' => $item['status'],
                'relative' => $this->resolveRelativeText($startAt, $endAt),
                'join_url' => '#',
            ];
        });
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
}

