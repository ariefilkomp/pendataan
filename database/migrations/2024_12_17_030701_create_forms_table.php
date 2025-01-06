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
        Schema::create('forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('table_name')->unique();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description');
            $table->boolean('published')->default(false);
            $table->boolean('multi_entry')->default(true);
            $table->enum('for_role', ['umum', 'opd'])->default('umum');
            $table->integer('id_opds')->nullable();
            $table->string('short_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
