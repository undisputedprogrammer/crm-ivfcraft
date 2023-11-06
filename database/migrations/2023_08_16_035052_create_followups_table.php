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
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->references('id')->on('leads');
            $table->integer('followup_count')->nullable();
            $table->timestamp('scheduled_date');
            $table->timestamp('actual_date')->nullable();
            $table->timestamp('next_followup_date')->nullable();
            // $table->enum('status',['pending','completed']);

            $table->boolean('converted')->default(false);
            $table->boolean('consulted')->default(false);
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};
