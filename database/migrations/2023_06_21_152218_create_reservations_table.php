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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id');
            $table->unsignedBigInteger('owner_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->string('repeat_type');
            $table->text('repeat_options')->nullable();
            $table->smallInteger('reservation_type')->default(1)->comment('1=reservation, 2=black out');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
