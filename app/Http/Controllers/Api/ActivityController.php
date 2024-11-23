<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return Activity::with('person')->orderBy('day_of_week')->orderBy('start_time')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'person_id' => 'required|exists:people,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        return Activity::create($validated);
    }

    public function show(Activity $activity)
    {
        return $activity->load('person');
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'person_id' => 'sometimes|required|exists:people,id',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'day_of_week' => 'sometimes|required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        $activity->update($validated);
        return $activity->load('person');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->noContent();
    }
}
