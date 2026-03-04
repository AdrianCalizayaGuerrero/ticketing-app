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
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('ticked_id');
            $table->string('previous_status');
            $table->string('new_status');
            $table->text('comment')->nullable();
            $table->uuid('changed_by'); // agent id
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('ticked_id')
                ->references('id')
                ->on('tickeds')
                ->cascadeOnDelete();

            $table->foreign('changed_by')
                ->references('id')
                ->on('agents');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
};
