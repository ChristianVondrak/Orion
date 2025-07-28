<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AnnualHoursRequest extends ReportRangeRequest
{
    public function rules(): array
    {
        // hereda start/end/export de ReportRangeRequest
        return array_merge(parent::rules(), [
            'year'       => ['nullable', 'integer', 'min:2000', 'max:' . now()->year],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);
    }

    /**
     * Año seleccionado o default al actual
     */
    public function getYear(): int
    {
        return (int) $this->input('year', now()->year);
    }

    /**
     * ID de proyecto o null
     */
    public function getProjectId(): ?int
    {
        return $this->input('project_id');
    }
}
