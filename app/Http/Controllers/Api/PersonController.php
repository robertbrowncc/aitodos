<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\Traits\ApiResponse;
use Carbon\Carbon;

class PersonController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $people = Person::latest()->get();
        return $this->resourceCollectionResponse(
            PersonResource::collection($people),
            'People retrieved successfully'
        );
    }

    public function store(StorePersonRequest $request): JsonResponse
    {
        $person = Person::create($request->validated());
        return $this->resourceResponse(
            new PersonResource($person),
            'Person created successfully',
            201
        );
    }

    public function show(Person $person): JsonResponse
    {
        return $this->resourceResponse(
            new PersonResource($person),
            'Person retrieved successfully'
        );
    }

    public function update(UpdatePersonRequest $request, Person $person): JsonResponse
    {
        $person->update($request->validated());
        return $this->resourceResponse(
            new PersonResource($person),
            'Person updated successfully'
        );
    }

    public function destroy(Person $person)
    {
        $person->delete();
        return response()->noContent();
    }

    public function upcomingBirthdays(): JsonResponse
    {
        $today = Carbon::today();
        
        // Get all people with birthdays
        $people = Person::whereNotNull('date_of_birth')
            ->get()
            ->map(function ($person) use ($today) {
                // Get this year's birthday
                $birthday = Carbon::parse($person->date_of_birth)->setYear($today->year);
                
                // If birthday has passed this year, look at next year's birthday
                if ($birthday->isPast() && !$birthday->isToday()) {
                    $birthday->addYear();
                }
                
                $daysUntil = $today->diffInDays($birthday, false);
                
                return [
                    'person' => $person,
                    'days_until' => $daysUntil,
                    'is_today' => $birthday->isToday()
                ];
            })
            ->filter(function ($item) {
                // Include birthdays that are today (days_until = 0) or in the future
                return $item['days_until'] >= 0;
            })
            ->sortBy([
                // Sort by is_today first (true comes first)
                ['is_today', 'desc'],
                // Then by days until
                ['days_until', 'asc']
            ])
            ->take(4);

        return $this->resourceResponse(
            PersonResource::collection($people->pluck('person')),
            'Upcoming birthdays retrieved successfully'
        );
    }
}
