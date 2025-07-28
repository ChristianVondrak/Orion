<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Timming extends Model
{
    use HasFactory;

    /**
     * Defines a "belongs to" relationship with the worksnapUser model.
     *
     * @return BelongsTo
     */
    public function worksnapUser(): BelongsTo
    {
        return $this->BelongsTo(worksnapUser::class);
    }

    /**
     * Defines a "belongs to" relationship with the project model.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->BelongsTo(Project::class);
    }

    /**
     * Get the human-readable formatted time from the timestamp.
     *
     * @return string The formatted date and time.
     */
    public function getHumanTimeFromTimestampAttribute()
    {
        return  Carbon::createFromTimestamp(
            $this->from_timestamp,
            'America/Caracas')
            ->format('d. F Y, g:i A');
    }

    /**
     * Filtrar por proyecto (sin lógica condicional).
     */
    public function scopeByProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Filtrar registros entre dos timestamps dados.
     */
    public function scopeBetweenDates(Builder $query, int $start, int $end): Builder
    {
        return $query->whereBetween('from_timestamp', [$start, $end]);
    }
}
