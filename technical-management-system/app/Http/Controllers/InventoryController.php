<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function adminIndex(Request $request)
    {
        return $this->renderIndex($request, 'admin.inventory');
    }

    public function techHeadIndex(Request $request)
    {
        return $this->renderIndex($request, 'tech-head.inventory');
    }

    public function technicianIndex(Request $request)
    {
        return $this->renderIndex($request, 'technician.inventory');
    }

    public function store(Request $request)
    {
        $data = $this->validateItem($request, null);
        $data['status'] = $this->computeStatus($data['quantity'], $data['min_level']);

        InventoryItem::create($data);

        return redirect()->back()->with('status', 'Inventory item added');
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $data = $this->validateItem($request, $inventoryItem->id);
        $data['status'] = $this->computeStatus($data['quantity'], $data['min_level']);

        $inventoryItem->update($data);

        return redirect()->back()->with('status', 'Inventory item updated');
    }

    private function renderIndex(Request $request, string $view)
    {
        $query = InventoryItem::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $items = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'totalItems' => InventoryItem::count(),
            'lowStock' => InventoryItem::where('status', 'low')->count(),
            'outOfStock' => InventoryItem::where('status', 'out')->count(),
        ];

        $categories = InventoryItem::whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view($view, compact('items', 'stats', 'categories'));
    }

    private function validateItem(Request $request, ?int $ignoreId): array
    {
        $skuRule = 'unique:inventory_items,sku';
        if ($ignoreId) {
            $skuRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'name' => 'required|string',
            'sku' => ['required', 'string', $skuRule],
            'category' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'unit' => 'nullable|string',
            'min_level' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
    }

    private function computeStatus(int $quantity, int $minLevel): string
    {
        if ($quantity <= 0) {
            return 'out';
        }

        if ($quantity <= $minLevel) {
            return 'low';
        }

        return 'normal';
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return redirect()->back()->with('status', 'Inventory item deleted successfully');
    }

    public function requestItem(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:500',
        ]);

        InventoryRequest::create([
            'inventory_item_id' => $validated['inventory_item_id'],
            'requested_by' => auth()->id(),
            'quantity' => $validated['quantity'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('status', 'Request submitted successfully! Waiting for approval.');
    }
}
