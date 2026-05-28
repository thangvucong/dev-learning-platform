<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        OrderItem::query()->delete();

        $courseIds = Course::query()
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($courseIds === []) {
            throw new \RuntimeException(
                'OrderItemSeeder: không có khóa học nào. Chạy CourseSeeder trước khi seed order_items.'
            );
        }

        Order::query()->each(function (Order $order) {
            $targetItemCount = random_int(1, 3);

            $courses = Course::query()
                ->inRandomOrder()
                ->limit($targetItemCount)
                ->get();

            foreach ($courses as $course) {
                OrderItem::factory()
                    ->forCourse($course)
                    ->state(function () use ($order) {
                        return [
                            'order_id' => $order->id,
                        ];
                    })
                    ->create();
            }

            $order->update([
                'subtotal_amount' => $order->items()->sum('original_price'),
                'discount_amount' => $order->items()->sum('discount_amount'),
                'total_amount' => $order->items()->sum('final_price'),
            ]);
        });
    }
}
