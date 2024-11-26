<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'person_id' => 'nullable|exists:people,id',
            'completed' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'completed' => $this->completed ?? false,
        ]);
    }
}
