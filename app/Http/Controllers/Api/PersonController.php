<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index()
    {
        return Person::latest()->get();
    }

    public function store(StorePersonRequest $request)
    {
        return Person::create($request->validated());
    }

    public function show(Person $person)
    {
        return $person;
    }

    public function update(UpdatePersonRequest $request, Person $person)
    {
        $person->update($request->validated());
        return $person;
    }

    public function destroy(Person $person)
    {
        $person->delete();
        return response()->noContent();
    }
}
