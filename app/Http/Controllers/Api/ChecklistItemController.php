<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistItemController extends Controller
{
    public function index(Checklist $checklist)
    {
        return $checklist->items()->orderBy('order')->get();
    }

    public function store(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:255',
            'completed' => 'boolean',
        ]);

        // Get the highest order number and add 1
        $maxOrder = $checklist->items()->max('order') ?? -1;
        $validated['order'] = $maxOrder + 1;
        $validated['checklist_id'] = $checklist->id;

        $item = ChecklistItem::create($validated);
        return $item;
    }

    public function update(Request $request, Checklist $checklist, ChecklistItem $item)
    {
        $validated = $request->validate([
            'content' => 'sometimes|required|string|max:255',
            'completed' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        if (isset($validated['order'])) {
            $this->reorderItems($checklist, $item, $validated['order']);
        }

        $item->update($validated);
        return $item;
    }

    public function destroy(Checklist $checklist, ChecklistItem $item)
    {
        $oldOrder = $item->order;
        $item->delete();

        // Reorder remaining items
        $checklist->items()
            ->where('order', '>', $oldOrder)
            ->update(['order' => DB::raw('`order` - 1')]);

        return response()->noContent();
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
            return response()->json([
                'message' => 'All items must belong to the checklist'
            ], 422);
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
            return $checklist->items()
                ->orderBy('order')
                ->get();

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update item order'
            ], 500);
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
