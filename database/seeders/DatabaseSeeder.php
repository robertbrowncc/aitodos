<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\PersonSeeder;
use Database\Seeders\TodoSeeder;
use Database\Seeders\ActivitySeeder;
use Database\Seeders\ListSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            PersonSeeder::class,
            TodoSeeder::class,
            ActivitySeeder::class,
            ListSeeder::class,
        ]);
    }
}
