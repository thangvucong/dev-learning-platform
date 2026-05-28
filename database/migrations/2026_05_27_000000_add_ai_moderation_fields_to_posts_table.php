<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAiModerationFieldsToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('ai_review_status', 30)->nullable()->after('reject_reason')->index();
            $table->string('ai_decision', 30)->nullable()->after('ai_review_status');
            $table->decimal('ai_confidence', 5, 4)->nullable()->after('ai_decision');
            $table->string('ai_severity', 20)->nullable()->after('ai_confidence');
            $table->json('ai_flags')->nullable()->after('ai_severity');
            $table->text('ai_summary')->nullable()->after('ai_flags');
            $table->text('ai_explanation')->nullable()->after('ai_summary');
            $table->text('ai_escalation_reason')->nullable()->after('ai_explanation');
            $table->unsignedSmallInteger('ai_review_attempts')->default(0)->after('ai_escalation_reason');
            $table->timestamp('ai_reviewed_at')->nullable()->after('ai_review_attempts');
            $table->string('ai_model', 80)->nullable()->after('ai_reviewed_at');
            $table->string('ai_error_code', 80)->nullable()->after('ai_model');
            $table->text('ai_error_message')->nullable()->after('ai_error_code');
            $table->foreignId('reviewed_by')->nullable()->after('ai_error_message')->constrained('users')->nullOnDelete();
            $table->timestamp('human_reviewed_at')->nullable()->after('reviewed_by');
        });

        DB::table('posts')
            ->where('status', 'pending')
            ->update(['status' => 'pending_human_review']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('posts')
            ->where('status', 'pending_human_review')
            ->update(['status' => 'pending']);

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'ai_review_status',
                'ai_decision',
                'ai_confidence',
                'ai_severity',
                'ai_flags',
                'ai_summary',
                'ai_explanation',
                'ai_escalation_reason',
                'ai_review_attempts',
                'ai_reviewed_at',
                'ai_model',
                'ai_error_code',
                'ai_error_message',
                'reviewed_by',
                'human_reviewed_at',
            ]);
        });
    }
}
