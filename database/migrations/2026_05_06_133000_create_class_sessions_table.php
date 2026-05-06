<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedInteger('session_no');
            $table->string('title')->nullable();
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->string('status')->default('upcoming');
            $table->string('meeting_type')->nullable();
            $table->string('meeting_info')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['class_id', 'session_no']);
            $table->index(['class_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_sessions');
    }
}

