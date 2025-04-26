<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id', 'country', 'phone', 'position',
        'gender', 'marital_status', 'date_of_birth'
    ];

    public function user()
    {
        return $this->belongsTo(worksnapUser::class, 'user_id');
    }
}
