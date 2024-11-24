<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\PersonValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePersonRequest extends FormRequest
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
        
        // Add required rules for store
        $rules['name'] = 'required|' . $rules['name'];
        $rules['email'] = $rules['email'] . '|' . $this->getUniqueEmailRule();

        return $rules;
    }
}
