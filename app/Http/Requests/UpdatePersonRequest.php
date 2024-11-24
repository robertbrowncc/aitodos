<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\PersonValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonRequest extends FormRequest
{
    use PersonValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = $this->getCommonRules();
        
        // Add sometimes|required rules for update
        $rules['first_name'] = 'sometimes|required|' . $rules['first_name'];
        $rules['last_name'] = 'sometimes|required|' . $rules['last_name'];
        $rules['email'] = 'sometimes|' . $rules['email'] . '|' . $this->getUniqueEmailRule();

        return $rules;
    }
}
