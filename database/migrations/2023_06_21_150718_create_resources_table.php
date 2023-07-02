<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id');
            $table->integer('ordering');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->string('contact_info')->nullable();
            $table->boolean('allow_multiple_days')->default(0);
            $table->smallInteger('max_participants')->default(0);
            $table->smallInteger('min_reservation_duration')->default(0);
            $table->smallInteger('max_reservation_duration')->default(0);
            $table->smallInteger('min_notice_duration')->default(0);
            $table->smallInteger('max_notice_duration')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
