<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChecklistController extends Controller
{
    public function index()
    {
        return Checklist::with(['person', 'items' => function ($query) {
            $query->orderBy('order');
        }])->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'person_id' => 'nullable|exists:people,id',
        ]);

        $checklist = Checklist::create($validated);
        return $checklist->load(['person', 'items']);
    }

    public function show(Checklist $checklist)
    {
        return $checklist->load(['person', 'items' => function ($query) {
            $query->orderBy('order');
        }]);
    }

    public function update(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'person_id' => 'nullable|exists:people,id',
        ]);

        $checklist->update($validated);
        return $checklist->load(['person', 'items']);
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();
        return response()->noContent();
    }
}
