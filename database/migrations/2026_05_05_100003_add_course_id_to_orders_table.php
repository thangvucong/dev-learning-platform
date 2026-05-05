<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCourseIdToOrdersTable extends Migration
{
    /**
     * Thêm course_id trực tiếp trên orders (backfill từ order_items — không xóa order_items).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('user_id')->constrained('courses')->nullOnDelete();
            }
        });

        if (Schema::hasTable('order_items')) {
            DB::statement('
                UPDATE orders o
                INNER JOIN (
                    SELECT order_id, MIN(id) AS min_item_id
                    FROM order_items
                    GROUP BY order_id
                ) first_item ON first_item.order_id = o.id
                INNER JOIN order_items oi ON oi.id = first_item.min_item_id
                SET o.course_id = oi.course_id
                WHERE o.course_id IS NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'course_id')) {
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
        });
    }
}
