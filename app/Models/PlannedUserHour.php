<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PlannedUserHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_start',
        'planned_hours',
    ];

    /**
     * Return the planned hours for a given user and week.
     *
     * If there is no explicit record, assumes 8 hours per working day (Mon–Fri).
     *
     * @param  int    $userId     The worksnap_user id
     * @param  Carbon $weekStart  A Carbon representing the Monday of that week
     * @return float              Planned hours for the week
     */
    public static function getForWeek(int $userId, Carbon $weekStart): float
    {
        $date = $weekStart->toDateString();

        // 1) Intenta cargar registro explícito
        $record = self::where('user_id', $userId)
            ->where('week_start', $date)
            ->first();

        if ($record) {
            return (float) $record->planned_hours;
        }

        // 2) Si no hay registro, calculamos 8 h/día de lunes a viernes
        $period      = CarbonPeriod::create($weekStart, $weekStart->copy()->addDays(6));
        $workingDays = 0;

        foreach ($period as $day) {
            // dayOfWeek 1 = Monday ... 5 = Friday
            if ($day->dayOfWeek >= Carbon::MONDAY && $day->dayOfWeek <= Carbon::FRIDAY) {
                $workingDays++;
            }
        }

        return round($workingDays * 8, 2);
    }
}
