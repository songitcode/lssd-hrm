<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name_ingame');

            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rank_id')->constrained()->cascadeOnDelete();

            $table->string('avatar')->nullable();
            $table->string('discord_id')->nullable();
            $table->string('discord_username')->nullable();
            $table->string('discord_avatar')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();

            // $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('position_id');
            // $table->unsignedBigInteger('rank_id');
            // $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('position_id')->references('id')->on('positions');
            // $table->foreign('rank_id')->references('id')->on('ranks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
