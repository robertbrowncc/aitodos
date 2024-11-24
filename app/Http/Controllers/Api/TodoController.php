<?php

namespace App\Http\Controllers\Api;

use App\Models\Todo;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    public function index()
    {
        return response()->json(
            Todo::select('id', 'name', 'url', 'completed', 'person_id', 'created_at', 'updated_at')
                ->with(['person' => function($query) {
                    $query->select('id', 'name');
                }])
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'person_id' => 'nullable|exists:people,id',
            'completed' => 'boolean'
        ]);

        // Set default value for completed if not provided
        if (!isset($validated['completed'])) {
            $validated['completed'] = false;
        }

        $todo = Todo::create($validated);
        return $todo->load('person');
    }

    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'completed' => 'boolean',
            'person_id' => 'nullable|exists:people,id',
            'name' => 'sometimes|required|string|max:255',
            'url' => 'nullable|url|max:255'
        ]);

        $todo->update($validated);
        return $todo->load('person');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->noContent();
    }

    public function getPeople()
    {
        return Person::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }
}
