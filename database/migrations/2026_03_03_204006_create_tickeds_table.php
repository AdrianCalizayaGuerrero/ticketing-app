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
        Schema::create('tickeds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_code')->unique();
            $table->string('subject');
            $table->text('description');
            $table->string('status');

            $table->uuid('reporter_id'); // employee id
            $table->uuid('assigned_agent_id')->nullable(); // agent id

            $table->foreign('reporter_id')->references('id')->on('employees');
            $table->foreign('assigned_agent_id')->references('id')->on('agents')->nullOnDelete();

            $table->foreignId('category_id')->constrained();
            $table->foreignId('priority_id')->constrained();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickeds');
    }
};
