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
        Schema::create('rank_salary_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rank_id')->constrained()->cascadeOnDelete();
            $table->decimal('hourly_rate', 10, 0)->default(0);
            $table->decimal('max_hours_per_day', 5, 2)->default(3.0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['rank_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rank_salary_configs', function (Blueprint $table) {
            // Drop foreign keys trước (nếu cần rollback an toàn tuyệt đối)
            $table->dropForeign(['rank_id']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('rank_salary_configs');
    }
};
