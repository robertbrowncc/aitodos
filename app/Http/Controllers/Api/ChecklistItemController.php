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
        $oldOrder = $item->order;
        $item->delete();

        // Reorder remaining items
        $checklist->items()
            ->where('order', '>', $oldOrder)
            ->update(['order' => DB::raw('`order` - 1')]);

        return $this->successResponse(
            null,
            'Checklist item deleted successfully',
            204
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

            // Return the updated items
            $items = $checklist->items()->orderBy('order')->get();
            return $this->successResponse(
                ChecklistItemResource::collection($items),
                'Items reordered successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update item order',
                500
            );
        }
    }

    protected function reorderItems(Checklist $checklist, ChecklistItem $item, int $newOrder)
    {
        $oldOrder = $item->order;
        
        if ($oldOrder === $newOrder) {
            return;
        }

        if ($oldOrder < $newOrder) {
            // Moving down: decrement items in between
            $checklist->items()
                ->where('order', '>', $oldOrder)
                ->where('order', '<=', $newOrder)
                ->where('id', '!=', $item->id)
                ->update(['order' => DB::raw('`order` - 1')]);
        } else {
            // Moving up: increment items in between
            $checklist->items()
                ->where('order', '>=', $newOrder)
                ->where('order', '<', $oldOrder)
                ->where('id', '!=', $item->id)
                ->update(['order' => DB::raw('`order` + 1')]);
        }

        $item->order = $newOrder;
        $item->save();
    }
}
