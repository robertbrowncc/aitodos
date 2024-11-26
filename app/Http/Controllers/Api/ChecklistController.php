<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\Traits\ApiResponse;

class ChecklistController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $checklists = Checklist::with(['person', 'items' => function ($query) {
            $query->orderBy('order');
        }])->latest()->get();
        
        return $this->successResponse($checklists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'person_id' => 'nullable|exists:people,id',
        ]);

        $checklist = Checklist::create($validated);
        return $this->successResponse(
            $checklist->load(['person', 'items']),
            'Checklist created successfully',
            201
        );
    }

    public function show(Checklist $checklist)
    {
        return $this->successResponse(
            $checklist->load(['person', 'items' => function ($query) {
                $query->orderBy('order');
            }])
        );
    }

    public function update(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'person_id' => 'nullable|exists:people,id',
        ]);

        $checklist->update($validated);
        return $this->successResponse(
            $checklist->fresh()->load(['person', 'items']),
            'Checklist updated successfully'
        );
    }

    public function destroy(Checklist $checklist)
    {
        $checklist->delete();
        return $this->successResponse(
            null,
            'Checklist deleted successfully'
        );
    }
}
