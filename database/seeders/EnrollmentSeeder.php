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
            ->each(function (Order $order) {
                foreach ($order->items as $item) {
                    Enrollment::query()->updateOrCreate(
                        [
                            'course_id' => $item->course_id,
                            'user_id' => $order->user_id,
                        ],
                        [
                            'status' => 'active',
                            'enrolled_at' => $order->paid_at ?: now(),
                            'completed_at' => null,
                        ]
                    );
                }
            });
    }
}
