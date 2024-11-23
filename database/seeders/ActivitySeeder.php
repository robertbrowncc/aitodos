<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\Activity;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            // Work activities
            [
                'name' => 'Team Stand-up',
                'start_time' => '09:30:00',
                'end_time' => '10:00:00',
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
            ],
            [
                'name' => 'Lunch Break',
                'start_time' => '12:00:00',
                'end_time' => '13:00:00',
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
            ],
            [
                'name' => 'Project Planning',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'days' => ['Monday']
            ],
            // Exercise activities
            [
                'name' => 'Morning Gym',
                'start_time' => '07:00:00',
                'end_time' => '08:00:00',
                'days' => ['Monday', 'Wednesday', 'Friday']
            ],
            [
                'name' => 'Evening Yoga',
                'start_time' => '18:00:00',
                'end_time' => '19:00:00',
                'days' => ['Tuesday', 'Thursday']
            ],
            // Social activities
            [
                'name' => 'Book Club',
                'start_time' => '19:00:00',
                'end_time' => '20:30:00',
                'days' => ['Wednesday']
            ],
            [
                'name' => 'Family Dinner',
                'start_time' => '18:30:00',
                'end_time' => '20:00:00',
                'days' => ['Sunday']
            ],
            // Weekend activities
            [
                'name' => 'Grocery Shopping',
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'days' => ['Saturday']
            ],
            [
                'name' => 'Swimming Class',
                'start_time' => '16:00:00',
                'end_time' => '17:00:00',
                'days' => ['Saturday']
            ],
            [
                'name' => 'Movie Night',
                'start_time' => '20:00:00',
                'end_time' => '22:30:00',
                'days' => ['Friday']
            ]
        ];

        $people = Person::all();

        foreach ($people as $person) {
            // Each person gets 3-5 random activities
            $numActivities = rand(3, 5);
            $selectedActivities = collect($activities)->random($numActivities);

            foreach ($selectedActivities as $activity) {
                foreach ($activity['days'] as $day) {
                    Activity::create([
                        'name' => $activity['name'],
                        'person_id' => $person->id,
                        'start_time' => $activity['start_time'],
                        'end_time' => $activity['end_time'],
                        'day_of_week' => $day
                    ]);
                }
            }
        }
    }
}
