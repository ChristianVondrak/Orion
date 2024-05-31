<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    public function worksnapUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            worksnapUser::class,
            'project_user',
            'project_id',
            'user_id'
        )->withPivot('hourly_rate');
    }

    public function projectUsers(): HasMany
    {
        return $this->hasMany(projectUser::class);
    }

    public function timmings(): HasMany
    {
        return $this->hasMany(Timming::class);
    }
}
