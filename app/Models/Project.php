<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;
    public function worksnapUsers(): BelongsToMany
    {
        return $this->belongsToMany(worksnapUser::class, 'project_user', 'project_id', 'user_id');
    }
}
