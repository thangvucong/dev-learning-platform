<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToSepayWebhookEventsTable extends Migration
{
    /**
     * Trạng thái xử lý webhook (processed / failed).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sepay_webhook_events', function (Blueprint $table) {
            if (!Schema::hasColumn('sepay_webhook_events', 'status')) {
                $table->string('status', 32)->default('processed')->after('payload');
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
        Schema::table('sepay_webhook_events', function (Blueprint $table) {
            if (Schema::hasColumn('sepay_webhook_events', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}
