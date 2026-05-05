<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeUsersPasswordNullable extends Migration
{
    /**
     * Cho phép đăng nhập OTP/Google không có password cục bộ (không dùng doctrine/dbal).
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
    }
}
