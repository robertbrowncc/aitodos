<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $people = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '555-0100',
                'date_of_birth' => '1990-01-15',
                'address' => '123 Main St, Anytown, USA'
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '555-0101',
                'date_of_birth' => '1992-03-20',
                'address' => '456 Oak Ave, Somewhere, USA'
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'email' => 'bob.johnson@example.com',
                'phone' => '555-0102',
                'date_of_birth' => '1985-07-10',
                'address' => '789 Pine Rd, Elsewhere, USA'
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Williams',
                'email' => 'alice.williams@example.com',
                'phone' => '555-0103',
                'date_of_birth' => '1988-11-30',
                'address' => '321 Elm St, Nowhere, USA'
            ],
            [
                'first_name' => 'Charlie',
                'last_name' => 'Brown',
                'email' => 'charlie.brown@example.com',
                'phone' => '555-0104',
                'date_of_birth' => '1995-05-25',
                'address' => '654 Maple Dr, Anywhere, USA'
            ]
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
