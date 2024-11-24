<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListItem;
use App\Models\CustomList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListItemController extends Controller
{
    public function index(CustomList $list)
    {
        return $list->items()->orderBy('order')->get();
    }

    public function store(Request $request, CustomList $list)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:255',
            'completed' => 'boolean',
        ]);

        // Get the highest order number and add 1
        $maxOrder = $list->items()->max('order') ?? -1;
        $validated['order'] = $maxOrder + 1;
        $validated['list_id'] = $list->id;

        $item = ListItem::create($validated);
        return $item;
    }

    public function update(Request $request, CustomList $list, ListItem $item)
    {
        $validated = $request->validate([
            'content' => 'sometimes|required|string|max:255',
            'completed' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        if (isset($validated['order'])) {
            $this->reorderItems($list, $item, $validated['order']);
        }

        $item->update($validated);
        return $item;
    }

    public function destroy(CustomList $list, ListItem $item)
    {
        $oldOrder = $item->order;
        $item->delete();

        // Reorder remaining items
        $list->items()
            ->where('order', '>', $oldOrder)
            ->update(['order' => DB::raw('`order` - 1')]);

        return response()->noContent();
    }

    protected function reorderItems(CustomList $list, ListItem $item, int $newOrder)
    {
        $oldOrder = $item->order;
        
        if ($oldOrder === $newOrder) {
            return;
        }

        if ($oldOrder < $newOrder) {
            // Moving down: decrement items in between
            $list->items()
                ->where('order', '>', $oldOrder)
                ->where('order', '<=', $newOrder)
                ->where('id', '!=', $item->id)
                ->update(['order' => DB::raw('`order` - 1')]);
        } else {
            // Moving up: increment items in between
            $list->items()
                ->where('order', '>=', $newOrder)
                ->where('order', '<', $oldOrder)
                ->where('id', '!=', $item->id)
                ->update(['order' => DB::raw('`order` + 1')]);
        }

        $item->order = $newOrder;
        $item->save();
    }
}
