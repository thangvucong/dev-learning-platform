<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CoursePriceSeeder extends Seeder
{
    public function run()
    {
        CoursePrice::query()->delete();

        $activeCurrency = Currency::query()->where('code', 'VND')->first();

        if (!$activeCurrency) {
            $activeCurrency = Currency::query()->where('is_active', true)->first();
        }

        Course::query()->each(function (Course $course) use ($activeCurrency) {
            CoursePrice::factory()
                ->active()
                ->state(function () use ($course, $activeCurrency) {
                    return [
                        'course_id' => $course->id,
                        'currency_id' => $activeCurrency->id,
                    ];
                })
                ->create();

            CoursePrice::factory()
                ->inactive()
                ->state(function () use ($course, $activeCurrency) {
                    return [
                        'course_id' => $course->id,
                        'currency_id' => $activeCurrency->id,
                    ];
                })
                ->create();
        });
    }
}
