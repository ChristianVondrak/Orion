<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class worksnapUser extends Model
{
    use HasFactory, Notifiable;

    /**
     * Defines a many-to-many relationship with Project.
     *
     * @return BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class,
            'project_users',
            'user_id',
            'project_id')
            ->withPivot('hourly_rate','flat_rate','payment_type');
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

    public function detail()
    {
        return $this->hasOne(UserDetail::class, 'user_id');
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

    public function plannedHours()
    {
        return $this->hasMany(PlannedUserHour::class, 'user_id');
    }
    /**
     * Calculate worked time of a user in hours per project.
     *
     * @return float
     */
    public function getTimingsCountInHoursAttribute()
    {
        $minutes = $this->timmings->count();
        return round(CarbonInterval::minutes($minutes*10)->total('hours'),2);
    }

    protected static function newFactory()
    {
        return WorksnapUserFactory::new();
    }

    /**
     * Calculate total profits per User.
     *
     * @param float $hours
     * @param projectUser $projectUser
     * @return float
     */
    public function totalProfits($hours, projectUser $projectUser): float
    {
        if ($projectUser->isFlatRate()) {
            return (float) $projectUser->flat_rate;
        }
        
        return round($hours * $projectUser->hourly_rate, 2);
    }
}
