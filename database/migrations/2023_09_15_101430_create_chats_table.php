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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->enum('type', ['text','media'])->nullable();
            $table->string('direction');
            $table->foreignId('lead_id')->references('id')->on('leads')->nullable();
            $table->enum('status',['submitted','sent','delivered','read','received','viewed']);
            $table->text('wamid');
            $table->string('expiration_time')->nullable();
            // $table->foreignId('template_id')->constrained('messages','id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
