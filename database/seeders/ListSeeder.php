<?php

namespace Database\Seeders;

use App\Models\CustomList;
use App\Models\ListItem;
use App\Models\Person;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    public function run(): void
    {
        // Create some example lists for The Dude
        $dude = Person::where('name', 'Jeffrey "The Dude" Lebowski')->first();
        
        if ($dude) {
            // Bowling team list
            $bowlingList = CustomList::create([
                'name' => 'Bowling Team Roster',
                'description' => 'League players and substitutes',
                'person_id' => $dude->id,
            ]);

            $bowlingItems = [
                ['content' => 'The Dude - Team Captain', 'order' => 1],
                ['content' => 'Walter Sobchak', 'order' => 2],
                ['content' => 'Theodore Donald "Donny" Kerabatsos', 'order' => 3],
                ['content' => 'Jesus Quintana (rival)', 'order' => 4],
            ];

            foreach ($bowlingItems as $item) {
                ListItem::create([
                    'list_id' => $bowlingList->id,
                    'content' => $item['content'],
                    'order' => $item['order'],
                    'completed' => false,
                ]);
            }

            // Rug shopping list
            $rugList = CustomList::create([
                'name' => 'Rug Shopping List',
                'description' => 'It really tied the room together',
                'person_id' => $dude->id,
            ]);

            $rugItems = [
                ['content' => 'Persian rug - 8x10', 'order' => 1],
                ['content' => 'Check local dealers', 'order' => 2],
                ['content' => 'Compare prices', 'order' => 3],
                ['content' => 'Get compensation from Big Lebowski', 'order' => 4],
            ];

            foreach ($rugItems as $item) {
                ListItem::create([
                    'list_id' => $rugList->id,
                    'content' => $item['content'],
                    'order' => $item['order'],
                    'completed' => false,
                ]);
            }
        }

        // Create some random lists for testing
        CustomList::factory()
            ->count(3)
            ->afterCreating(function (CustomList $list) {
                ListItem::factory()
                    ->count(5)
                    ->create(['list_id' => $list->id]);
            })
            ->create();
    }
}
