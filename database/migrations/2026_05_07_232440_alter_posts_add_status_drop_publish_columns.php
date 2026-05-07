<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPostsAddStatusDropPublishColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->after('views_count');

            $table->dropColumn(['is_published', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('views_count');
            $table->timestamp('published_at')->nullable()->after('is_published');

            $table->dropColumn(['status']);
        });
    }
}
