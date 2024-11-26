<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\Traits\ApiResponse;

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
}
