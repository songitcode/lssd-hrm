<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Thêm cột mới (nếu chưa có)
        Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
            if (!Schema::hasColumn('monthly_attendance_summaries', 'period_type')) {
                $table->enum('period_type', ['monthly', 'biweekly'])
                    ->default('monthly')
                    ->after('year');
            }

            if (!Schema::hasColumn('monthly_attendance_summaries', 'period_start')) {
                $table->date('period_start')->nullable()->after('period_type');
            }

            if (!Schema::hasColumn('monthly_attendance_summaries', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start');
            }
        });

        // 2. Đảm bảo user_id có index riêng (để không phụ thuộc unique cũ)
        $this->ensureUserIdIndex();

        // 3. Drop unique cũ (nếu tồn tại)
        $this->dropOldUniqueIfExists();

        // 4. Tạo unique mới (nếu chưa có)
        $this->createNewUniqueIfNotExists();
    }

    public function down(): void
    {
        // 1. Drop unique mới
        $this->dropIndexIfExists('monthly_attendance_summaries', 'mas_unique_period');

        // 2. Xóa cột
        Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
            if (Schema::hasColumn('monthly_attendance_summaries', 'period_type')) {
                $table->dropColumn(['period_type', 'period_start', 'period_end']);
            }
        });

        // 3. Restore unique cũ (nếu chưa có)
        if (!$this->indexExists('monthly_attendance_summaries', 'monthly_attendance_summaries_user_id_month_year_unique')) {
            Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
                $table->unique(
                    ['user_id', 'month', 'year'],
                    'monthly_attendance_summaries_user_id_month_year_unique'
                );
            });
        }
    }

    /**
     * =========================
     * HELPER FUNCTIONS
     * =========================
     */

    private function ensureUserIdIndex()
    {
        if (!$this->indexExists('monthly_attendance_summaries', 'mas_user_id_idx')) {
            Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
                $table->index('user_id', 'mas_user_id_idx');
            });
        }
    }

    private function dropOldUniqueIfExists()
    {
        if ($this->indexExists('monthly_attendance_summaries', 'monthly_attendance_summaries_user_id_month_year_unique')) {
            Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
                $table->dropUnique('monthly_attendance_summaries_user_id_month_year_unique');
            });
        }
    }

    private function createNewUniqueIfNotExists()
    {
        if (!$this->indexExists('monthly_attendance_summaries', 'mas_unique_period')) {
            Schema::table('monthly_attendance_summaries', function (Blueprint $table) {
                $table->unique(
                    ['user_id', 'period_type', 'month', 'year', 'period_start'],
                    'mas_unique_period'
                );
            });
        }
    }

    private function dropIndexIfExists($table, $index)
    {
        if ($this->indexExists($table, $index)) {
            Schema::table($table, function (Blueprint $table) use ($index) {
                $table->dropIndex($index);
            });
        }
    }

    private function indexExists($table, $indexName)
    {
        $result = DB::select("
            SELECT COUNT(1) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = ?
            AND index_name = ?
        ", [$table, $indexName]);

        return $result[0]->count > 0;
    }
};