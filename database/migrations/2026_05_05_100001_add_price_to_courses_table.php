<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToCoursesTable extends Migration
{
    /**
     * Thêm giá hiển thị trên course (song song course_prices — không xóa dữ liệu cũ).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 12, 2)->nullable()->after('intro_video_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
}
