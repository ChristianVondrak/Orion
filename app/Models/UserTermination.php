<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTermination extends Model
{
    protected $table = 'user_terminations';

    protected $fillable = [
        'user_id',
        'termination_date',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(worksnapUser::class, 'user_id');
    }
}

