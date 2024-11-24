<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $people = [
            [
                'name' => 'Jeffrey "The Dude" Lebowski',
                'email' => 'thedude@lebowski.com'
            ],
            [
                'name' => 'Walter Sobchak',
                'email' => 'walter@sobchak-security.com'
            ],
            [
                'name' => 'Donny Kerabatsos',
                'email' => 'donny@bowling.com'
            ],
            [
                'name' => 'Maude Lebowski',
                'email' => 'maude@knox-reilly.com'
            ],
            [
                'name' => 'Jesus Quintana',
                'email' => 'jesus@bowling.com'
            ],
            [
                'name' => 'Bunny Lebowski',
                'email' => 'bunny@lebowski.com'
            ],
            [
                'name' => 'Jackie Treehorn',
                'email' => 'jackie@treehorn-productions.com'
            ],
            [
                'name' => 'Brandt',
                'email' => 'brandt@lebowski-estate.com'
            ]
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
