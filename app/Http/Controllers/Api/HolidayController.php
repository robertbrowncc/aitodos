<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\Traits\ApiResponse;

class HolidayController extends Controller
{
    use ApiResponse;

    private array $belgianHolidays2024 = [
        ['name' => 'New Year\'s Day', 'date' => '2024-01-01'],
        ['name' => 'Easter Monday', 'date' => '2024-04-01'],
        ['name' => 'Labour Day', 'date' => '2024-05-01'],
        ['name' => 'Ascension Day', 'date' => '2024-05-09'],
        ['name' => 'Whit Monday', 'date' => '2024-05-20'],
        ['name' => 'Belgian National Day', 'date' => '2024-07-21'],
        ['name' => 'Assumption of Mary', 'date' => '2024-08-15'],
        ['name' => 'All Saints\' Day', 'date' => '2024-11-01'],
        ['name' => 'Armistice Day', 'date' => '2024-11-11'],
        ['name' => 'Christmas Day', 'date' => '2024-12-25']
    ];

    public function upcomingHolidays(): JsonResponse
    {
        $today = Carbon::today();
        
        $upcomingHolidays = collect($this->belgianHolidays2024)
            ->map(function ($holiday) use ($today) {
                $holidayDate = Carbon::parse($holiday['date']);
                
                // If the holiday has passed this year, look at next year's date
                if ($holidayDate->isPast()) {
                    $holidayDate->addYear();
                    $holiday['date'] = $holidayDate->format('Y-m-d');
                }
                
                return [
                    'name' => $holiday['name'],
                    'date' => $holiday['date'],
                    'days_until' => $today->diffInDays($holidayDate, false)
                ];
            })
            ->filter(function ($holiday) {
                return $holiday['days_until'] >= 0;
            })
            ->sortBy('days_until')
            ->take(4)
            ->values();

        return $this->successResponse(
            $upcomingHolidays,
            'Upcoming Belgian holidays retrieved successfully'
        );
    }
}
