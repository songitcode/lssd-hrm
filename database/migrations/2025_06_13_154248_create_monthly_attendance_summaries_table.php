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
        Schema::create('monthly_attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('month'); // 1-12
            $table->unsignedInteger('year');
            $table->decimal('total_hours', 8, 2);
            $table->decimal('total_wage', 10, 0);
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year']); // Đảm bảo 1 bản ghi mỗi tháng
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_attendance_summaries');
    }
};
