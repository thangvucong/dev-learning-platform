<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class RenameEnrollmentsTableToCourseUser extends Migration
{
    /**
     * Align tên bảng many-to-many với schema doc (course_user).
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('enrollments') && !Schema::hasTable('course_user')) {
            Schema::rename('enrollments', 'course_user');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('course_user') && !Schema::hasTable('enrollments')) {
            Schema::rename('course_user', 'enrollments');
        }
    }
}
