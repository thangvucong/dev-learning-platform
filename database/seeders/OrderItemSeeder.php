<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CoursePrice;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        Order::query()->each(function (Order $order) {
            $targetItemCount = random_int(1, 3);
            $existingCourseIds = $order->items()->pluck('course_id');

            $courseIds = Course::query()
                ->whereNotIn('id', $existingCourseIds)
                ->inRandomOrder()
                ->limit(max(0, $targetItemCount - $existingCourseIds->count()))
                ->pluck('id');

            foreach ($courseIds as $courseId) {
                $coursePrice = CoursePrice::query()
                    ->where('course_id', $courseId)
                    ->where('is_active', true)
                    ->first();

                OrderItem::factory()
                    ->state(function () use ($order, $courseId, $coursePrice) {
                        return [
                            'order_id' => $order->id,
                            'course_id' => $courseId,
                            'price' => $coursePrice ? $coursePrice->price : random_int(149000, 999000),
                        ];
                    })
                    ->create();
            }

            $order->update([
                'total_amount' => $order->items()->sum('price'),
            ]);
        });
    }
}
