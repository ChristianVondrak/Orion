<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timming extends Model
{
    use HasFactory;

    public function worksnapUsers(): BelongsTo
    {
        return $this->BelongsTo(worksnapUser::class);
    }

    public function Projects(): BelongsTo
    {
        return $this->BelongsTo(Project::class);
    }
}
