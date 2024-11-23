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
        return Todo::with('person')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'person_id' => 'nullable|exists:people,id'
        ]);

        return Todo::create($validated);
    }

    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'completed' => 'boolean',
            'person_id' => 'nullable|exists:people,id'
        ]);

        $todo->update($validated);
        return $todo->load('person');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json(['message' => 'Todo deleted']);
    }

    public function getPeople()
    {
        return Person::select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }
}
