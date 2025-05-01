<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ActivityIndexReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start' => ['required','date','before_or_equal:end'],
            'end'   => ['required','date','after_or_equal:start'],
        ];
    }

    public function validatedDates(): array
    {
        return [
            'start' => Carbon::parse($this->input('start'))->startOfDay(),
            'end'   => Carbon::parse($this->input('end'))->endOfDay(),
        ];
    }
}
