<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('label_end')->nullable();
            $table->smallInteger('availability_code')->default(1)->comment('1=reservable, 2=unreservable');
            $table->time('start_time');
            $table->time('end_time');
            $table->smallInteger('day_of_week')->nullable();
            $table->foreignId('schedule_layout_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_blocks');
    }
};
