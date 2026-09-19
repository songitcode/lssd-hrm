<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('url');
            $table->string('route_name')->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 20)->nullable()->index();
            $table->string('browser', 80)->nullable()->index();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};