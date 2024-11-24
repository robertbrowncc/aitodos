<?php

namespace Database\Factories;

use App\Models\CustomList;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomListFactory extends Factory
{
    protected $model = CustomList::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'person_id' => Person::factory(),
        ];
    }
}
