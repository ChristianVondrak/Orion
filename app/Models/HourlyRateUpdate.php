<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HourlyRateUpdate extends Model
{
    protected $fillable = [
        'user_id',
        'previous_rate',
        'new_rate',
    ];

    /**
     * Relación con el usuario.
     */
    public function user()
    {
        return $this->belongsTo(worksnapUser::class, 'user_id');
    }
}

