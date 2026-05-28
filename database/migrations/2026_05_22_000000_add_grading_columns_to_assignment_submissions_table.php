<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradingColumnsToAssignmentSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->timestamp('graded_at')->nullable()->after('feedback');
            $table->foreignId('graded_by')->nullable()->after('graded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->dropForeign(['graded_by']);
            $table->dropColumn(['graded_at', 'graded_by']);
        });
    }
}
