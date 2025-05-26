<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserTerminationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'termination_date' => ['required','date','before_or_equal:today'],
            'reason'           => ['nullable','string','max:255'],
        ];
    }
}
