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

        if (!$activeCurrency) {
            throw new \RuntimeException(
                'CoursePriceSeeder: không tìm thấy currency (VND hoặc is_active). Chạy CurrencySeeder trước.'
            );
        }

        $courses = Course::query()->orderBy('id')->get();

        foreach ($courses->values() as $index => $course) {
            $isFree = $index < 2;
            $price = $isFree ? 0 : random_int(149000, 2999000);
            $compare = $isFree ? 0 : $price + random_int(50000, 300000);

            CoursePrice::factory()
                ->active()
                ->create([
                    'course_id' => $course->id,
                    'currency_id' => $activeCurrency->id,
                    'price' => $price,
                    'compare_price' => $compare,
                ]);

            CoursePrice::factory()
                ->inactive()
                ->create([
                    'course_id' => $course->id,
                    'currency_id' => $activeCurrency->id,
                ]);
        }
    }
}
