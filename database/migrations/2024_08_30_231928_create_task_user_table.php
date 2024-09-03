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
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // Automatically delete the relation if the user is deleted
            $table->foreignId('task_id')
                ->constrained('tasks')
                ->onDelete('cascade'); // Automatically delete the relation if the task is deleted
            $table->timestamps();

            // Ensure that a user can only be assigned to a task once
            $table->unique(['user_id', 'task_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tasks');
    }
};
