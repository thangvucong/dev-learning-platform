<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseDiscount;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseDiscountSeeder extends Seeder
{
    /**
     * Seed course discount data.
     *
     * @return void
     */
    public function run()
    {
        CourseDiscount::query()->delete();

        $courses = Course::query()
            ->select(['id', 'original_price'])
            ->orderBy('id')
            ->get();

        if ($courses->isEmpty()) {
            throw new \RuntimeException(
                'CourseDiscountSeeder: không có khóa học nào. Chạy CourseSeeder trước khi seed course_discounts.'
            );
        }

        $creatorId = User::query()
            ->where('email', 'admin@example.com')
            ->value('id') ?: User::query()->value('id');

        $courses->each(function (Course $course, int $index) use ($creatorId) {
            $type = [
                CourseDiscount::TYPE_PERCENT,
                CourseDiscount::TYPE_FIXED,
                CourseDiscount::TYPE_FINAL_PRICE,
            ][$index % 3];

            CourseDiscount::factory()
                ->state([
                    'course_id' => $course->id,
                    'created_by' => $creatorId,
                    'type' => $type,
                    'amount' => $this->amountForType($type, (int) $course->original_price),
                    'starts_at' => now()->subDays(7 + $index),
                    'ends_at' => now()->addDays(21 + $index),
                    'repeat_type' => CourseDiscount::REPEAT_NONE,
                    'day_of_week' => null,
                    'is_active' => true,
                ])
                ->create();
        });
    }

    protected function amountForType(string $type, int $originalPrice): int
    {
        if ($type === CourseDiscount::TYPE_PERCENT) {
            return 20;
        }

        if ($type === CourseDiscount::TYPE_FIXED) {
            return min(150000, max(0, $originalPrice));
        }

        if ($originalPrice <= 0) {
            return 0;
        }

        return max(0, $originalPrice - 200000);
    }
}
