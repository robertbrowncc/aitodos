<?php

namespace Database\Factories;

use App\Models\ListItem;
use App\Models\CustomList;
use Illuminate\Database\Eloquent\Factories\Factory;

class ListItemFactory extends Factory
{
    protected $model = ListItem::class;

    public function definition(): array
    {
        return [
            'content' => fake()->sentence(),
            'order' => fake()->numberBetween(0, 100),
            'completed' => fake()->boolean(20),
            'list_id' => CustomList::factory(),
        ];
    }
}
