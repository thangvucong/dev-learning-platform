<?php

namespace App\Services;

use App\Models\CourseClass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ClassSessionGeneratorService
{
    /**
     * Generate class sessions by config.
     *
     * @param  \App\Models\CourseClass  $courseClass
     * @param  array<string, mixed>  $config
     * @return \Illuminate\Support\Collection<int, \App\Models\ClassSession>
     */
    public function generate(CourseClass $courseClass, array $config): Collection
    {
        if (($config['generation_mode'] ?? 'auto') === 'custom') {
            $daysOfWeek = $this->normalizeDaysOfWeek((array) ($config['days_of_week'] ?? []));
            if ($daysOfWeek->isEmpty()) {
                throw ValidationException::withMessages([
                    'schedule_config.days_of_week' => 'Chế độ tuỳ chỉnh yêu cầu chọn ít nhất 1 thứ học trong tuần.',
                ]);
            }

            return $this->createCustomSessions($courseClass, $config, $daysOfWeek);
        }

        return $this->createAutoSessions($courseClass, $config);
    }

    /**
     * Create sessions by class duration + week days.
     *
     * @param  \App\Models\CourseClass  $courseClass
     * @param  array<string, mixed>  $config
     * @return \Illuminate\Support\Collection<int, \App\Models\ClassSession>
     */
    protected function createAutoSessions(CourseClass $courseClass, array $config): Collection
    {
        $sessionsCount = (int) ($config['sessions_count'] ?? 0);
        if ($sessionsCount <= 0 || !$courseClass->start_at || !$courseClass->end_at) {
            return collect();
        }

        $startTime = (string) ($config['session_start_time'] ?? $courseClass->start_at->format('H:i'));
        $endTime = (string) ($config['session_end_time'] ?? $courseClass->end_at->format('H:i'));
        $windowStart = $courseClass->start_at->copy();
        $windowEnd = $courseClass->end_at->copy();
        if ($windowEnd->lte($windowStart)) {
            throw ValidationException::withMessages([
                'end_at' => 'Thời gian kết thúc lớp phải sau thời gian bắt đầu.',
            ]);
        }

        $chosen = collect();
        for ($i = 0; $i < $sessionsCount; $i++) {
            $ratio = $sessionsCount === 1 ? 0 : ($i / ($sessionsCount - 1));
            $date = $windowStart->copy()->addSeconds((int) round($ratio * $windowStart->diffInSeconds($windowEnd)));
            $sessionStart = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
            $sessionEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);
            if ($sessionEnd->lte($sessionStart)) {
                $sessionEnd->addDay();
            }
            $chosen->push([
                'start_at' => $sessionStart,
                'end_at' => $sessionEnd,
            ]);
        }

        return $this->persistSessions($courseClass, $chosen);
    }

    /**
     * Create sessions by selected week days (custom mode).
     *
     * @param  \App\Models\CourseClass  $courseClass
     * @param  array<string, mixed>  $config
     * @param  \Illuminate\Support\Collection<int, int>  $daysOfWeek
     * @return \Illuminate\Support\Collection<int, \App\Models\ClassSession>
     */
    protected function createCustomSessions(CourseClass $courseClass, array $config, Collection $daysOfWeek): Collection
    {
        $sessionsCount = (int) ($config['sessions_count'] ?? 0);
        if ($sessionsCount <= 0 || !$courseClass->start_at || !$courseClass->end_at) {
            throw ValidationException::withMessages([
                'schedule_config.sessions_count' => 'Số buổi học phải lớn hơn 0.',
            ]);
        }

        $startTime = (string) ($config['session_start_time'] ?? $courseClass->start_at->format('H:i'));
        $endTime = (string) ($config['session_end_time'] ?? $courseClass->end_at->format('H:i'));
        $windowStart = $courseClass->start_at->copy()->startOfDay();
        $windowEnd = $courseClass->end_at->copy()->endOfDay();

        $rows = collect();
        $cursor = $windowStart->copy();
        while ($cursor->lte($windowEnd) && $rows->count() < $sessionsCount) {
            if ($daysOfWeek->contains((int) $cursor->dayOfWeekIso)) {
                $sessionStart = Carbon::parse($cursor->format('Y-m-d') . ' ' . $startTime);
                $sessionEnd = Carbon::parse($cursor->format('Y-m-d') . ' ' . $endTime);
                if ($sessionEnd->lte($sessionStart)) {
                    $sessionEnd->addDay();
                }

                if ($sessionStart->gte($courseClass->start_at) && $sessionStart->lte($windowEnd)) {
                    $rows->push([
                        'start_at' => $sessionStart,
                        'end_at' => $sessionEnd,
                    ]);
                }
            }
            $cursor->addDay();
        }

        if ($rows->count() < $sessionsCount) {
            throw ValidationException::withMessages([
                'schedule_config.sessions_count' => 'Không đủ ngày học phù hợp trong khoảng thời gian lớp để tạo đủ số buổi.',
            ]);
        }

        return $this->persistSessions($courseClass, $rows);
    }

    /**
     * Persist sessions rows into class_sessions table.
     *
     * @param  \App\Models\CourseClass  $courseClass
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $rows
     * @return \Illuminate\Support\Collection<int, \App\Models\ClassSession>
     */
    protected function persistSessions(CourseClass $courseClass, Collection $rows): Collection
    {
        $courseClass->sessions()->delete();

        return $rows->values()->map(function (array $row, int $index) use ($courseClass) {
            return $courseClass->sessions()->create([
                'session_no' => $index + 1,
                'title' => $row['title'] ?? ('Buổi ' . ($index + 1)),
                'start_at' => $row['start_at'],
                'end_at' => $row['end_at'],
                'status' => $this->resolveSessionStatus($row['start_at'], $row['end_at']),
                'meeting_type' => $courseClass->location ? 'offline' : 'zoom',
                'meeting_info' => $courseClass->location ?: 'Zoom meeting',
                'description' => 'Buổi học được tự động tạo theo lịch lớp.',
            ]);
        });
    }

    protected function normalizeDaysOfWeek(array $daysOfWeek): Collection
    {
        return collect($daysOfWeek)
            ->map(static function ($day) {
                return (int) $day;
            })
            ->filter(static function (int $day) {
                return $day >= 1 && $day <= 7;
            })
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Resolve status from session time.
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
}

