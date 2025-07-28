<?php
namespace App\Http\Requests;

use Carbon\Carbon;

class RateUpdatesRequest extends ReportRangeRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'year' => ['nullable','integer','min:2000','max:' . now()->year],
        ]);
    }

    public function getYear(): int
    {
        return (int) $this->input('year', now()->year);
    }

    /**
     * Devuelve el rango completo del año si no hay start/end explícitos.
     */
    public function getRange(): array
    {
        if ($this->filled('start') && $this->filled('end')) {
            return parent::getRange();
        }

        $year = $this->getYear();

        return [
            'start' => Carbon::create($year,  1,  1)->startOfDay(),
            'end'   => Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }
}
