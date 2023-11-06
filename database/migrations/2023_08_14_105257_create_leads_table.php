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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained('hospitals', 'id');
            $table->foreignId('center_id')->constrained('centers', 'id');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->json('qnas')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->boolean('is_genuine')->default(false);
            $table->text('history')->nullable();
            $table->text('q_visit')->nullable();
            $table->text('q_decide')->nullable();
            $table->enum('customer_segment',['hot','warm','cold'])->nullable();
            $table->enum('status', ['Created', 'Follow-up Started', 'Appointment Fixed', 'Consulted', 'Completed', 'Closed'])->default('Created')->nullable();
            $table->enum('treatment_status', ['Continuing','Discontinued','Not decided'])->nullable();
            $table->boolean('followup_created')->default(false);
            $table->timestamp('followup_created_at')->nullable();
            $table->foreignId('assigned_to')->references('id')->on('users');
            $table->foreignId('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
