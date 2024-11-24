<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\Person;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $people = Person::all();
        
        $todos = [
            [
                'name' => 'Review project proposal',
                'url' => 'https://docs.example.com/proposal',
                'completed' => false
            ],
            [
                'name' => 'Schedule team meeting',
                'url' => null,
                'completed' => true
            ],
            [
                'name' => 'Update documentation',
                'url' => 'https://wiki.example.com/docs',
                'completed' => false
            ],
            [
                'name' => 'Fix critical bug in production',
                'url' => 'https://github.com/example/repo/issues/123',
                'completed' => false
            ],
            [
                'name' => 'Prepare quarterly report',
                'url' => null,
                'completed' => false
            ],
            [
                'name' => 'Review pull requests',
                'url' => 'https://github.com/example/repo/pulls',
                'completed' => false
            ],
            [
                'name' => 'Deploy new features',
                'url' => 'https://deploy.example.com',
                'completed' => false
            ],
            [
                'name' => 'Update dependencies',
                'url' => 'https://npmjs.com',
                'completed' => true
            ],
            [
                'name' => 'Write unit tests',
                'url' => null,
                'completed' => false
            ],
            [
                'name' => 'Conduct code review',
                'url' => 'https://github.com/example/repo/reviews',
                'completed' => false
            ]
        ];

        foreach ($todos as $todo) {
            // Randomly assign each todo to a person (or leave unassigned)
            $person = $people->random(); // Get a random person
            $todo['person_id'] = rand(0, 1) ? $person->id : null; // 50% chance of being assigned

            Todo::create($todo);
        }
    }
}
