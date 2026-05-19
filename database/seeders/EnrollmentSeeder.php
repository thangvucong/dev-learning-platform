<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Seed enrollments from paid order items.
     *
     * @return void
     */
    public function run()
    {
        Enrollment::query()->delete();

        Order::query()
            ->with('items')
            ->where('status', Order::STATUS_PAID)
            ->each(function (Order $order) {
                foreach ($order->items as $item) {
                    if (!$order->user_id || !$item->course_id) {
                        continue;
                    }

                    $attributes = Enrollment::factory()
                        ->active()
                        ->state([
                            'course_id' => $item->course_id,
                            'user_id' => $order->user_id,
                            'enrolled_at' => $order->paid_at ?: now(),
                            'completed_at' => null,
                        ])
                        ->make()
                        ->getAttributes();

                    Enrollment::query()->updateOrCreate(
                        [
                            'course_id' => $item->course_id,
                            'user_id' => $order->user_id,
                        ],
                        $attributes
                    );
                }
            });
    }
}
