<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function Project(): BelongsTo
    {
        return $this->BelongsTo(Project::class);
    }
}
