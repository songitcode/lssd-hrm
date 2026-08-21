<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monthly_attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('month');  // 1–12
            $table->unsignedInteger('year');
            $table->decimal('total_hours', 8, 2);
            $table->decimal('total_wage', 10, 0);

            // ── Chu kỳ lương ──────────────────────────────────────────────
            // monthly  → period_start / period_end = NULL
            // biweekly → period_start = ngày T2 đầu kỳ, period_end = CN cuối kỳ
            $table->enum('period_type', ['monthly', 'biweekly'])->default('monthly');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            $table->timestamps();

            // Unique: mỗi user chỉ có 1 bản ghi / kỳ.
            // - monthly  → unique trên (user_id, period_type, month, year)       với period_start NULL
            // - biweekly → unique trên (user_id, period_type, month, year, period_start)
            // MySQL coi nhiều NULL là KHÔNG vi phạm unique → dùng nullable period_start là đủ.
            $table->unique(
                ['user_id', 'period_type', 'month', 'year', 'period_start'],
                'mas_unique_period'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_attendance_summaries');
    }
};
