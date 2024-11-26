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
            // After-school activities (weekdays)
            [
                'name' => 'Soccer Practice',
                'start_time' => '16:30:00',
                'end_time' => '18:00:00',
                'days' => ['Tuesday', 'Thursday']
            ],
            [
                'name' => 'Piano Lessons',
                'start_time' => '17:00:00',
                'end_time' => '18:00:00',
                'days' => ['Wednesday']
            ],
            [
                'name' => 'Art Class',
                'start_time' => '16:00:00',
                'end_time' => '17:30:00',
                'days' => ['Monday']
            ],
            [
                'name' => 'Swimming Lessons',
                'start_time' => '17:00:00',
                'end_time' => '18:00:00',
                'days' => ['Friday']
            ],
            [
                'name' => 'Dance Class',
                'start_time' => '16:30:00',
                'end_time' => '17:30:00',
                'days' => ['Tuesday']
            ],
            // Weekend activities
            [
                'name' => 'Baseball Practice',
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'days' => ['Saturday']
            ],
            [
                'name' => 'Karate Class',
                'start_time' => '11:00:00',
                'end_time' => '12:00:00',
                'days' => ['Saturday']
            ],
            [
                'name' => 'Coding Club',
                'start_time' => '14:00:00',
                'end_time' => '15:30:00',
                'days' => ['Sunday']
            ],
            [
                'name' => 'Family Game Night',
                'start_time' => '19:00:00',
                'end_time' => '20:30:00',
                'days' => ['Friday']
            ],
            [
                'name' => 'Library Reading Club',
                'start_time' => '13:00:00',
                'end_time' => '14:00:00',
                'days' => ['Saturday']
            ]
        ];

        $people = Person::all();

        foreach ($people as $person) {
            // Each person gets 2-3 random activities (reduced from 3-5)
            $numActivities = rand(2, 3);
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
