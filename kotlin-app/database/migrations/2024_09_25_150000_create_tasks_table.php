<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
     // public function up(): void
    // {
       // Schema::create('tasks', function (Blueprint $table) {
         //   $table->id();
         //   $table->timestamps();
        // });
    // }

    public function up()
{
   Schema::create('tasks', function (Blueprint $table) {
    $table->id();
    $table->string('task_name');
    $table->text('task_description')->nullable();
    $table->timestamp('start_datetime');
    $table->timestamp('end_datetime')->nullable();
    $table->json('repeat_days')->nullable(); // Make sure this column is JSON
    $table->string('attachment')->nullable();
    $table->boolean('send_notification')->default(false);
    $table->timestamps();
});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
