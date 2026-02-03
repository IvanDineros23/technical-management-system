<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryRequest;
use App\Helpers\AuditLogHelper;
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

        $item = InventoryItem::create($data);

        AuditLogHelper::log(
            action: 'CREATE',
            modelType: 'Inventory',
            modelId: $item->id,
            description: "Added inventory item: {$data['name']} (SKU: {$data['sku']})",
            newValues: $data
        );

        return redirect()->back()->with('status', 'Inventory item added');
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $oldValues = $inventoryItem->toArray();
        $data = $this->validateItem($request, $inventoryItem->id);
        $data['status'] = $this->computeStatus($data['quantity'], $data['min_level']);

        $inventoryItem->update($data);

        AuditLogHelper::log(
            action: 'UPDATE',
            modelType: 'Inventory',
            modelId: $inventoryItem->id,
            description: "Updated inventory item: {$data['name']} (SKU: {$data['sku']})",
            oldValues: $oldValues,
            newValues: $data
        );

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
        $requests = null;
        if (in_array($view, ['admin.inventory', 'tech-head.inventory'], true)) {
            $requests = InventoryRequest::with(['inventoryItem', 'user'])
                ->latest('created_at')
                ->paginate(10, ['*'], 'requests_page')
                ->withQueryString();
        }

        return view($view, compact('items', 'stats', 'categories', 'requests'));
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
        $itemData = $inventoryItem->toArray();
        $itemId = $inventoryItem->id;
        $itemName = $inventoryItem->name;
        
        $inventoryItem->delete();

        AuditLogHelper::log(
            action: 'DELETE',
            modelType: 'Inventory',
            modelId: $itemId,
            description: "Deleted inventory item: {$itemName}",
            oldValues: $itemData
        );

        return redirect()->back()->with('status', 'Inventory item deleted successfully');
    }

    public function requestItem(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:500',
        ]);

        $item = InventoryItem::find($validated['inventory_item_id']);

        $inventoryRequest = InventoryRequest::create([
            'inventory_item_id' => $validated['inventory_item_id'],
            'requested_by' => auth()->id(),
            'quantity' => $validated['quantity'],
            'purpose' => $validated['purpose'],
            'status' => 'pending',
        ]);

        AuditLogHelper::log(
            action: 'CREATE',
            modelType: 'InventoryRequest',
            modelId: $inventoryRequest->id,
            description: "Requested {$validated['quantity']} {$item->name} for: {$validated['purpose']}"
        );

        return redirect()->back()->with('status', 'Request submitted successfully! Waiting for approval.');
    }

    public function viewRequests(Request $request, string $view)
    {
        $query = InventoryRequest::with(['inventoryItem', 'user'])
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('inventoryItem', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(20);

        return view($view, compact('requests'));
    }

    public function updateRequestStatus(Request $request, InventoryRequest $inventoryRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,fulfilled',
        ]);

        $oldStatus = $inventoryRequest->status;
        $inventoryRequest->update(['status' => $validated['status']]);

        AuditLogHelper::log(
            action: 'UPDATE',
            modelType: 'InventoryRequest',
            modelId: $inventoryRequest->id,
            description: "Changed request status from {$oldStatus} to {$validated['status']}"
        );

        return redirect()->back()->with('status', 'Request status updated successfully!');
    }
}
