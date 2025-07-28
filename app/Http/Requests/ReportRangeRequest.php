<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ReportRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start'  => ['nullable', 'date'],
            'end'    => ['nullable', 'date', 'after_or_equal:start'],
            'export' => ['nullable', 'in:excel,pdf'],
        ];
    }

    /**
     * Devuelve el rango de fechas, con defaults
     */
    public function getRange(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end   = Carbon::now()->endOfMonth();

        if ($this->filled('start') && $this->filled('end')) {
            $start = Carbon::parse($this->start)->startOfDay();
            $end   = Carbon::parse($this->end)->endOfDay();
        }

        return compact('start', 'end');
    }

    public function exportType(): ?string
    {
        return $this->input('export');
    }
}
