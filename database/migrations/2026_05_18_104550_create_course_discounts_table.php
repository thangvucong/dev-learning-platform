<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'percent',
                'fixed',
                'final_price',
            ]);
            $table->unsignedBigInteger('amount');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->enum('repeat_type', [
                'none',
                'weekly',
            ])->default('none');
            $table->unsignedTinyInteger('day_of_week')
                ->nullable();
            $table->boolean('is_active')
                ->default(true);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        
            $table->timestamps();
            $table->index([
                'starts_at',
                'ends_at',
            ]);
            $table->index([
                'repeat_type',
                'day_of_week',
            ]);
            $table->index(['course_id', 'is_active', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_discounts');
    }
}
