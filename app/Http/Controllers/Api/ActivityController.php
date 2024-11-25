<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        return Activity::with('person')->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function store(StoreActivityRequest $request)
    {
        $activity = Activity::create($request->validated());
        return $activity->load('person');
    }

    public function show(Activity $activity)
    {
        return $activity->load('person');
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $activity->update($request->validated());
        return $activity->fresh()->load('person');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->noContent();
    }
}
