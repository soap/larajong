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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->smallInteger('weekday_start')->default(1)->comment('0=SUN, 1=MON');
            $table->smallInteger('days_visible')->deafault(5)->comment('number of days to display');
            $table->smallInteger('time_on')->comment('store in minutes e.g. 8:00 = 480');
            $table->smallInteger('time_off')->comment('store in minutes e.g. 1020=17:00');
            $table->boolean('is_default')->default(0)->comment('Is this schedule is default one');
            $table->string('timezone')->default('UTC');
            $table->string('time_format')->default('24')->comment('12 or 24');
            $table->boolean('show_summary')->default(0)->comment('Show summary or not for this schedule');
            $table->string('admin_email')->nullable();
            $table->boolean('notify_admin')->default(0);
            $table->foreignId('layout_id');
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
        Schema::dropIfExists('schedules');
    }
};
