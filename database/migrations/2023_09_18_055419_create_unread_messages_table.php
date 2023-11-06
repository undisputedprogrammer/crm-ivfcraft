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
        Schema::create('unread_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->references('id')->on('chats');
            $table->foreignId('lead_id')->references('id')->on('leads');
            $table->integer('count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unread_messages');
    }
};
