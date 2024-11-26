<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChecklistItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['sometimes', 'string', 'max:255'],
            'completed' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer'],
            'checklist_id' => ['sometimes', 'exists:checklists,id'],
        ];
    }
}
