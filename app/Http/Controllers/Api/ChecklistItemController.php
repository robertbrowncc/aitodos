<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Http\Resources\ChecklistItemResource;
use App\Http\Requests\StoreChecklistItemRequest;
use App\Http\Requests\UpdateChecklistItemRequest;
use App\Http\Controllers\Api\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistItemController extends Controller
{
    use ApiResponse;

    public function index(Checklist $checklist)
    {
        $items = $checklist->items()->orderBy('order')->get();
        return $this->successResponse(
            ChecklistItemResource::collection($items),
            'Checklist items retrieved successfully'
        );
    }

    public function store(StoreChecklistItemRequest $request, Checklist $checklist)
    {
        $validated = $request->validated();
        
        // Get the highest order number and add 1
        $maxOrder = $checklist->items()->max('order') ?? -1;
        $validated['order'] = $maxOrder + 1;
        $validated['checklist_id'] = $checklist->id;
        $validated['completed'] = $validated['completed'] ?? false;

        $item = ChecklistItem::create($validated);
        
        return $this->successResponse(
            new ChecklistItemResource($item),
            'Checklist item created successfully',
            201
        );
    }

    public function update(UpdateChecklistItemRequest $request, Checklist $checklist, ChecklistItem $item)
    {
        $validated = $request->validated();

        if (isset($validated['order'])) {
            $this->reorderItems($checklist, $item, $validated['order']);
        }

        $item->update($validated);
        
        return $this->successResponse(
            new ChecklistItemResource($item),
            'Checklist item updated successfully'
        );
    }

    public function destroy(Checklist $checklist, ChecklistItem $item)
    {
        if ($item->checklist_id !== $checklist->id) {
            return $this->errorResponse('This item does not belong to the specified checklist', 403);
        }

        $oldOrder = $item->order;
        $item->delete();

        // Reorder remaining items
        $checklist->items()
            ->where('order', '>', $oldOrder)
            ->update(['order' => DB::raw('`order` - 1')]);

        return $this->successResponse(
            null,
            'Checklist item deleted successfully'
        );
    }

    public function reorder(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|distinct|exists:checklist_items,id'
        ]);

        // Verify all items belong to this checklist
        $itemCount = $checklist->items()
            ->whereIn('id', $validated['order'])
            ->count();

        if ($itemCount !== count($validated['order'])) {
            return $this->errorResponse(
                'All items must belong to the checklist',
                422
            );
        }

        try {
            DB::transaction(function () use ($checklist, $validated) {
                foreach ($validated['order'] as $index => $itemId) {
                    $checklist->items()
                        ->where('id', $itemId)
                        ->update(['order' => $index]);
                }
            });

            $items = $checklist->items()->orderBy('order')->get();
            return $this->successResponse(
                ChecklistItemResource::collection($items),
                'Items reordered successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to reorder items',
                500
            );
        }
    }

    private function reorderItems(Checklist $checklist, ChecklistItem $item, int $newOrder)
    {
        $oldOrder = $item->order;
        if ($oldOrder === $newOrder) {
            return;
        }

        if ($newOrder > $oldOrder) {
            // Moving down: decrease order of items in between
            $checklist->items()
                ->whereBetween('order', [$oldOrder + 1, $newOrder])
                ->update(['order' => DB::raw('`order` - 1')]);
        } else {
            // Moving up: increase order of items in between
            $checklist->items()
                ->whereBetween('order', [$newOrder, $oldOrder - 1])
                ->update(['order' => DB::raw('`order` + 1')]);
        }
    }
}
