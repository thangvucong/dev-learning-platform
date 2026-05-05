<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameClassesAndClassUserTables extends Migration
{
    /**
     * course_classes → classes, course_class_user → class_user, course_class_id → class_id.
     * Cột instructor_id / mode / capacity trên classes được xóa ở migration sau (drop_deprecated...).
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('course_class_user') || !Schema::hasTable('course_classes')) {
            return;
        }

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->dropForeign(['course_class_id']);
            $table->dropUnique(['course_class_id', 'user_id']);
        });

        Schema::rename('course_classes', 'classes');

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->after('id');
        });

        DB::statement('UPDATE course_class_user SET class_id = course_class_id WHERE class_id IS NULL');

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->dropColumn('course_class_id');
        });

        DB::statement('ALTER TABLE course_class_user MODIFY class_id BIGINT UNSIGNED NOT NULL');

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->unique(['class_id', 'user_id']);
        });

        Schema::rename('course_class_user', 'class_user');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('class_user') || !Schema::hasTable('classes')) {
            return;
        }

        Schema::rename('class_user', 'course_class_user');

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropUnique(['class_id', 'user_id']);
        });

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->unsignedBigInteger('course_class_id')->nullable()->after('id');
        });

        DB::statement('UPDATE course_class_user SET course_class_id = class_id WHERE course_class_id IS NULL');

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->dropColumn('class_id');
        });

        Schema::rename('classes', 'course_classes');

        DB::statement('ALTER TABLE course_class_user MODIFY course_class_id BIGINT UNSIGNED NOT NULL');

        Schema::table('course_class_user', function (Blueprint $table) {
            $table->foreign('course_class_id')->references('id')->on('course_classes')->onDelete('cascade');
            $table->unique(['course_class_id', 'user_id']);
        });
    }
}
