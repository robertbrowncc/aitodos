<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomList;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListController extends Controller
{
    public function index()
    {
        return CustomList::with(['person', 'items' => function ($query) {
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

        $list = CustomList::create($validated);
        return $list->load(['person', 'items']);
    }

    public function show(CustomList $list)
    {
        return $list->load(['person', 'items' => function ($query) {
            $query->orderBy('order');
        }]);
    }

    public function update(Request $request, CustomList $list)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'person_id' => 'nullable|exists:people,id',
        ]);

        $list->update($validated);
        return $list->load(['person', 'items']);
    }

    public function destroy(CustomList $list)
    {
        $list->delete();
        return response()->noContent();
    }
}
