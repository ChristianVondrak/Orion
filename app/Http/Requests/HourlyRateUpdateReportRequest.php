<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HourlyRateUpdateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year'       => ['nullable','integer','min:2000','max:' . now()->year],
            'project_id' => ['nullable','exists:projects,id'],
            'export'     => ['nullable','in:excel,pdf'],
        ];
    }

// Obtiene valores filtrados
    public function filters(): array
    {
        return [
            'year'       => $this->input('year', now()->year),
            'project_id' => $this->input('project_id'),
            'export'     => $this->input('export'),
        ];
    }
}
