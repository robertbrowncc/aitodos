<?php

namespace App\Http\Requests\Traits;

trait ActivityRules
{
    /**
     * Get the common validation rules for activities.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function activityRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'person_id' => 'required|exists:people,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ];
    }
}
