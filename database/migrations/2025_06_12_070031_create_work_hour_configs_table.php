<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_hour_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('max_hours_per_day', 5, 2)->default(3.00);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // ── Chu kỳ tính lương ──────────────────────────────────────────
            // monthly  : tổng hợp từ ngày 1 → cuối tháng theo lịch
            // biweekly : tổng hợp 14 ngày liên tiếp (T2 → CN tuần 2)
            $table->enum('cycle_type', ['monthly', 'biweekly'])->default('monthly');

            // Ngày thứ Hai dùng làm mốc tính chu kỳ 14 ngày (chỉ dùng khi biweekly)
            $table->date('biweekly_reference_date')->nullable();

            $table->timestamps();
        });

        // Bản ghi mặc định
        DB::table('work_hour_configs')->insert([
            'max_hours_per_day' => 3.00,
            'cycle_type' => 'monthly',
            'biweekly_reference_date' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('work_hour_configs');
    }
};
