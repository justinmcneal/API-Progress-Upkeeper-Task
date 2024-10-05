<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->json('repeat_days')->nullable();
            $table->boolean('send_notification')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};