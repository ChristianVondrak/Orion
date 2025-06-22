<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class projectUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'payment_type',
        'hourly_rate',
        'flat_rate',
    ];

    protected $casts = [
        'payment_type' => 'string',
        'hourly_rate' => 'float',
        'flat_rate' => 'decimal:2',
    ];

    /**
     * Defines a "belongs to" relationship with the project model.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Defines a "belongs to" relationship with the worksnapUser model.
     *
     * @return BelongsTo
     */
    public function worksnapUser(): BelongsTo
    {
        return $this->belongsTo(worksnapUser::class, 'user_id');
    }

    /**
     * Get the monthly payment amount based on payment type
     *
     * @param int $workingDays Number of working days in the month
     * @param float $hoursPerDay Hours per day (default 8)
     * @return float
     */
    public function getMonthlyPayment(int $workingDays, float $hoursPerDay = 8.0): float
    {
        if ($this->payment_type === 'flat') {
            return (float) $this->flat_rate;
        }

        // Para pago por hora, calculamos basado en días laborables
        return $this->hourly_rate * $hoursPerDay * $workingDays;
    }

    /**
     * Check if the payment type is flat rate
     *
     * @return bool
     */
    public function isFlatRate(): bool
    {
        return $this->payment_type === 'flat';
    }

    /**
     * Check if the payment type is hourly
     *
     * @return bool
     */
    public function isHourly(): bool
    {
        return $this->payment_type === 'hourly';
    }
}
