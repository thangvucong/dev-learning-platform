<?php

namespace Database\Factories;

use App\Models\ClassSession;
use App\Models\CourseClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassSessionFactory extends Factory
{
    protected $model = ClassSession::class;

    public function definition()
    {
        $startAt = now()->addDays($this->faker->numberBetween(1, 60))->setTime(19, 0);

        return [
            'class_id' => CourseClass::factory(),
            'session_no' => $this->faker->numberBetween(1, 30),
            'title' => 'Buổi ' . $this->faker->numberBetween(1, 30),
            'start_at' => $startAt,
            'end_at' => (clone $startAt)->addHours(2),
            'status' => ClassSession::STATUS_UPCOMING,
            'meeting_type' => ClassSession::MEETING_ZOOM,
            'meeting_info' => 'Zoom meeting',
            'join_url' => 'https://zoom.example.com/j/' . $this->faker->numerify('##########'),
            'description' => $this->faker->optional()->sentence(12),
        ];
    }

    public function forClass(CourseClass $courseClass, int $sessionNo)
    {
        $startAt = $courseClass->start_at
            ? $courseClass->start_at->copy()->addWeeks($sessionNo - 1)
            : now()->addWeeks($sessionNo);
        $endAt = $startAt->copy()->addHours(2);

        return $this->state(function () use ($courseClass, $sessionNo, $startAt, $endAt) {
            return [
                'class_id' => $courseClass->id,
                'session_no' => $sessionNo,
                'title' => 'Buổi ' . $sessionNo,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => $this->statusForSchedule($startAt, $endAt),
                'meeting_type' => $courseClass->mode === CourseClass::MODE_OFFLINE
                    ? ClassSession::MEETING_OFFLINE
                    : ClassSession::MEETING_ZOOM,
                'meeting_info' => $courseClass->location ?: 'Zoom meeting',
                'join_url' => $courseClass->mode === CourseClass::MODE_OFFLINE
                    ? null
                    : 'https://zoom.example.com/j/' . str_pad((string) $courseClass->id, 10, '0', STR_PAD_LEFT),
                'description' => 'Buổi học thuộc lớp ' . $courseClass->name . '.',
            ];
        });
    }

    protected function statusForSchedule($startAt, $endAt): string
    {
        if ($startAt->isFuture()) {
            return ClassSession::STATUS_UPCOMING;
        }

        if ($endAt->isFuture()) {
            return ClassSession::STATUS_LIVE;
        }

        return ClassSession::STATUS_COMPLETED;
    }
}
