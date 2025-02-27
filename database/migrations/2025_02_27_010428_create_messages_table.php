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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->uuid('form_id')->nullable();
            $table->text('message');
            $table->string('message_id')->nullable();
            $table->string('remote_jid')->nullable();
            $table->timestamp('sending_time')->nullable();
            $table->timestamp('sent_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
