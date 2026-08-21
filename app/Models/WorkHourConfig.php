<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorkHourConfig extends Model
{
    protected $fillable = [
        'max_hours_per_day',
        'updated_by',
        'cycle_type',              // 'monthly' | 'biweekly'
        'biweekly_reference_date', // date — ngày thứ Hai mốc đầu tiên
    ];

    protected $casts = [
        'biweekly_reference_date' => 'date',
    ];

    // ─── Singleton helpers ────────────────────────────────────────────────────

    public static function latestConfig(): self
    {
        return self::latest()->first() ?? self::create([
            'max_hours_per_day' => 3.00,
            'cycle_type' => 'monthly',
        ]);
    }

    public static function currentMaxHour(): float
    {
        return static::latest()->first()?->max_hours_per_day ?? 3.0;
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Chu kỳ tính lương ───────────────────────────────────────────────────

    /**
     * Kỳ HIỆN TẠI.
     * Trả về array:
     *   type, label, label_prev, start (Carbon), end (Carbon),
     *   month, year, period_start (string|null), period_end (string|null)
     */
    public function getCurrentPeriod(): array
    {
        return $this->cycle_type === 'biweekly'
            ? $this->buildBiweekly(Carbon::today())
            : $this->buildMonthly(Carbon::now());
    }

    /** Kỳ TRƯỚC kỳ hiện tại. */
    public function getPreviousPeriod(): array
    {
        if ($this->cycle_type === 'biweekly') {
            $cur = $this->buildBiweekly(Carbon::today());
            $prevDay = Carbon::parse($cur['period_start'])->subDay();
            return $this->buildBiweekly($prevDay);
        }

        return $this->buildMonthly(Carbon::now()->subMonth());
    }

    /**
     * 6 kỳ gần nhất (hiện tại + 5 kỳ trước).
     * monthly  → 6 tháng lùi
     * biweekly → 6 chu kỳ 14 ngày lùi
     */
    public function getLast6Periods(): array
    {
        $periods = [];

        for ($i = 5; $i >= 0; $i--) {
            if ($this->cycle_type === 'biweekly') {
                $targetDay = Carbon::today()->subDays($i * 14);
                $periods[] = $this->buildBiweekly($targetDay);
            } else {
                $periods[] = $this->buildMonthly(Carbon::now()->subMonths($i));
            }
        }

        return $periods;
    }

    // ─── Public alias (dùng trong controller khi cần build từ ngày bất kỳ) ──

    public function buildBiweeklyFromDay(Carbon $day): array
    {
        return $this->buildBiweekly($day);
    }

    // ─── Builders ─────────────────────────────────────────────────────────────

    private function buildMonthly(Carbon $ref): array
    {
        $start = $ref->copy()->startOfMonth()->startOfDay();
        $end = $ref->copy()->endOfMonth()->endOfDay();

        $prevRef = $start->copy()->subMonth();
        $prevLbl = 'Tháng ' . $prevRef->month . '/' . $prevRef->year;

        return [
            'type' => 'monthly',
            'label' => 'Tháng ' . $start->month . '/' . $start->year,
            'label_prev' => $prevLbl,
            'start' => $start,
            'end' => $end,
            'month' => $start->month,
            'year' => $start->year,
            'period_start' => null,
            'period_end' => null,
        ];
    }

    private function buildBiweekly(Carbon $targetDay): array
    {
        $ref = $this->biweekly_reference_date
            ? $this->biweekly_reference_date->copy()
            : Carbon::today()->startOfWeek(Carbon::MONDAY);

        $diff = $ref->diffInDays($targetDay, false);
        $idx = (int) floor($diff / 14);
        $start = $ref->copy()->addDays($idx * 14)->startOfDay();
        $end = $start->copy()->addDays(13)->endOfDay();

        $prevStart = $start->copy()->subDays(14);
        $prevEnd = $prevStart->copy()->addDays(13);

        return [
            'type' => 'biweekly',
            'label' => $start->format('d/m') . ' – ' . $end->format('d/m/Y'),
            'label_prev' => $prevStart->format('d/m') . ' – ' . $prevEnd->format('d/m/Y'),
            'start' => $start,
            'end' => $end,
            'month' => $start->month,
            'year' => $start->year,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
        ];
    }
}
