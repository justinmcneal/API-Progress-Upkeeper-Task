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
            $table->date('end_date')->nullable(); // Separate end date
            $table->time('end_time')->nullable(); // Separate end time
            $table->json('repeat_days')->nullable();
            $table->string('category'); // Remove default value
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Add foreign key to users table
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};