<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    const MONTH_HOURS_GOAL_VZLA = 160;
    const DAY_HOURS = 8;
    /**
     * Defines a many-to-many relationship with worksnapUser.
     *
     * @return BelongsToMany
     */
    public function worksnapUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            worksnapUser::class,
            'project_users',
            'project_id',
            'user_id'
        )->withPivot('hourly_rate');
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
     * Retrieve the associated Timming models.
     *
     * This function establishes a one-to-many relationship between the current model
     * and the Timming model.
     *
     * @return HasMany
     */
    public function timmings(): HasMany
    {
        return $this->hasMany(Timming::class);
    }

    /**
     * Calculate time in hours by counting minutes.
     *
     * @return float
     */
    public function getTimingsCountInHoursAttribute()
    {
        return floor($this->timmings_count* 10 / 60);
    }
}
