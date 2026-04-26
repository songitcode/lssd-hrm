<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PayrollSetting extends Model
{
    protected $fillable = [
        'cycle_type',
        'biweekly_reference_date',
    ];

    protected $casts = [
        'biweekly_reference_date' => 'date',
    ];

    // ─── Singleton helper ────────────────────────────────────────────────────

    /**
     * Trả về bản ghi cài đặt duy nhất, tạo mới nếu chưa có.
     */
    public static function current(): self
    {
        return self::firstOrCreate([], ['cycle_type' => 'monthly']);
    }

    // ─── Period helpers ──────────────────────────────────────────────────────

    /**
     * Trả về thông tin kỳ tính lương HIỆN TẠI dạng array:
     *
     * [
     *   'type'         => 'monthly' | 'biweekly',
     *   'label'        => string  (hiển thị giao diện),
     *   'start'        => Carbon,
     *   'end'          => Carbon,
     *   'month'        => int,
     *   'year'         => int,
     *   'period_start' => string|null  (Y-m-d),
     *   'period_end'   => string|null  (Y-m-d),
     * ]
     */
    public function getCurrentPeriod(): array
    {
        if ($this->cycle_type === 'monthly') {
            return $this->buildMonthlyPeriod(Carbon::now());
        }

        return $this->buildBiweeklyPeriod(Carbon::today());
    }

    /**
     * Trả về thông tin kỳ TRƯỚC (previous period).
     */
    public function getPreviousPeriod(): array
    {
        if ($this->cycle_type === 'monthly') {
            return $this->buildMonthlyPeriod(Carbon::now()->subMonth());
        }

        // Lùi về trước 14 ngày so với ngày đầu kỳ hiện tại
        $current = $this->buildBiweeklyPeriod(Carbon::today());
        $prevDay = Carbon::parse($current['period_start'])->subDay();
        return $this->buildBiweeklyPeriod($prevDay);
    }

    // ─── Builders (public để Controller có thể gọi) ──────────────────────────

    private function buildMonthlyPeriod(Carbon $ref): array
    {
        $start = $ref->copy()->startOfMonth()->startOfDay();
        $end   = $ref->copy()->endOfMonth()->endOfDay();

        return [
            'type'         => 'monthly',
            'label'        => 'Tháng ' . $start->month . '/' . $start->year,
            'start'        => $start,
            'end'          => $end,
            'month'        => $start->month,
            'year'         => $start->year,
            'period_start' => null,
            'period_end'   => null,
        ];
    }

    public function buildBiweeklyPeriodPublic(Carbon $targetDay): array
    {
        return $this->buildBiweeklyPeriod($targetDay);
    }

    private function buildBiweeklyPeriod(Carbon $targetDay): array
    {
        // Ngày mốc (luôn là thứ Hai). Nếu chưa set, dùng thứ Hai tuần hiện tại.
        $ref = $this->biweekly_reference_date
            ? $this->biweekly_reference_date->copy()
            : Carbon::today()->startOfWeek(Carbon::MONDAY);

        // Số ngày từ mốc đến targetDay
        $diff = $ref->diffInDays($targetDay, false);

        if ($diff < 0) {
            // targetDay nằm trước mốc — lùi về kỳ âm
            $periodIndex = (int) floor($diff / 14);
        } else {
            $periodIndex = (int) floor($diff / 14);
        }

        $start = $ref->copy()->addDays($periodIndex * 14)->startOfDay();
        $end   = $start->copy()->addDays(13)->endOfDay();

        return [
            'type'         => 'biweekly',
            'label'        => $start->format('d/m') . ' — ' . $end->format('d/m/Y'),
            'start'        => $start,
            'end'          => $end,
            'month'        => $start->month,
            'year'         => $start->year,
            'period_start' => $start->toDateString(),
            'period_end'   => $end->toDateString(),
        ];
    }
}
