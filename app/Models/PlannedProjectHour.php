<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class PlannedProjectHour extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['project_id','week_start','planned_hours'];

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class);
    }

    public static function getForWeek(int $projectId, Carbon $weekStart): float
    {
        $date   = $weekStart->toDateString();
        $record = static::where('project_id',$projectId)
            ->where('week_start',$date)
            ->first()
            ?: static::where('project_id',$projectId)
                ->where('week_start','<',$date)
                ->orderByDesc('week_start')
                ->first();

        return $record ? (float)$record->planned_hours : 0.0;
    }
}

