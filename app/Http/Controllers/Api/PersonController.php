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

    public function index()
    {
        return $this->success(Person::latest()->get());
    }

    public function store(StorePersonRequest $request)
    {
        return $this->success(Person::create($request->validated()));
    }

    public function show(Person $person)
    {
        return $this->success($person);
    }

    public function update(UpdatePersonRequest $request, Person $person)
    {
        $person->update($request->validated());
        return $this->success($person);
    }

    public function destroy(Person $person)
    {
        $person->delete();
        return response()->noContent();
    }
}
