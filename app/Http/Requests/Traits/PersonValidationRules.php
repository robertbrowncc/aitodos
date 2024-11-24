<?php

namespace App\Http\Requests\Traits;

trait PersonValidationRules
{
    /**
     * Get the common validation rules for person requests.
     *
     * @return array<string, string>
     */
    protected function getCommonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
        ];
    }

    /**
     * Get the unique email validation rule.
     *
     * @return string
     */
    protected function getUniqueEmailRule(): string
    {
        $uniqueRule = 'unique:people,email';
        
        // If this is an update request, exclude the current person
        if ($this->person) {
            $uniqueRule .= ',' . $this->person->id;
        }

        return $uniqueRule;
    }
}
