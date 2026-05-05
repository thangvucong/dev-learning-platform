<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCapacityToClassesTable extends Migration
{
    /**
     * Thêm sức chứa lớp (số học viên tối đa).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'capacity')) {
                $table->unsignedInteger('capacity')->default(30)->after('status');
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
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'capacity')) {
                $table->dropColumn('capacity');
            }
        });
    }
}
