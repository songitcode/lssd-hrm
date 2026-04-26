<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('cycle_type', ['monthly', 'biweekly'])->default('monthly')
                  ->comment('Chu kỳ tính lương: monthly = theo tháng, biweekly = theo 2 tuần');
            $table->date('biweekly_reference_date')->nullable()
                  ->comment('Ngày thứ Hai đầu tiên làm mốc tính chu kỳ 2 tuần');
            $table->timestamps();
        });

        // Chèn bản ghi mặc định
        DB::table('payroll_settings')->insert([
            'cycle_type'               => 'monthly',
            'biweekly_reference_date'  => null,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};