<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class worksnapUser extends Model
{
    use HasFactory;

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')->withPivot('hourly_rate');
    }
    /**
     * Get the related Timming models.
     *
     * This function defines a one-to-many relationship between the current model
     * and the ProjectUser model.
     *
     * @return HasMany
     */
    public function timmings(): HasMany
    {
        return $this->hasMany(Timming::class,'user_id', 'id');
    }

    /**
     * Get the related ProjectUser models.
     *
     * This function defines a one-to-many relationship between the current model
     * and the ProjectUser model.
     *
     * @return HasMany
     */
    public function projectUsers(): HasMany
    {
        return $this->hasMany(projectUser::class);
    }

    /**
     * Calculate worked time of a user in hours per project.
     *
     * @return CarbonInterval
     */
    public function getTimingsCountInHoursAttribute()
    {
        CarbonInterval::setCascadeFactors([
            'minute' => [60, 'seconds'],
            'hour' => [60, 'minutes']
        ]);

        $minutes = $this->timmings->count();
        $interval = CarbonInterval::minutes($minutes*10)->cascade();
        return ($interval);
    }

    public function total_profits($hours,$rate)
    {
        $hours= $hours->totalHours;
        return (round($hours*$rate,2));
    }
}
