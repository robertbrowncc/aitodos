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
                'email' => 'thedude@lebowski.com',
                'date_of_birth' => '1961-12-04',
                'phone' => '(310) 555-0123',
                'address' => 'Venice, Los Angeles, CA'
            ],
            [
                'name' => 'Walter Sobchak',
                'email' => 'walter@sobchak-security.com',
                'date_of_birth' => '1959-03-17',
                'phone' => '(310) 555-0124',
                'address' => 'Los Angeles, CA'
            ],
            [
                'name' => 'Donny Kerabatsos',
                'email' => 'donny@bowling.com',
                'date_of_birth' => '1964-09-23',
                'phone' => '(310) 555-0125',
                'address' => 'North Hollywood, Los Angeles, CA'
            ],
            [
                'name' => 'Maude Lebowski',
                'email' => 'maude@knox-reilly.com',
                'date_of_birth' => '1969-06-15',
                'phone' => '(310) 555-0126',
                'address' => 'Beverly Hills, CA'
            ],
            [
                'name' => 'Jesus Quintana',
                'email' => 'jesus@bowling.com',
                'date_of_birth' => '1967-11-30',
                'phone' => '(310) 555-0127',
                'address' => 'Hollywood, Los Angeles, CA'
            ],
            [
                'name' => 'Bunny Lebowski',
                'email' => 'bunny@lebowski.com',
                'date_of_birth' => '1972-04-01',
                'phone' => '(310) 555-0128',
                'address' => 'Beverly Hills, CA'
            ],
            [
                'name' => 'Jackie Treehorn',
                'email' => 'jackie@treehorn-productions.com',
                'date_of_birth' => '1955-08-12',
                'phone' => '(310) 555-0129',
                'address' => 'Malibu, CA'
            ],
            [
                'name' => 'Brandt',
                'email' => 'brandt@lebowski-estate.com',
                'date_of_birth' => '1963-01-25',
                'phone' => '(310) 555-0130',
                'address' => 'Pasadena, CA'
            ]
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
