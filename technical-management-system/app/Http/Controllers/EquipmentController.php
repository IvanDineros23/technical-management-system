<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Helpers\AuditLogHelper;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
        }

        // Type filter
        if ($request->filled('type') && $request->get('type') != '') {
            $query->where('category', $request->get('type'));
        }

        // Status filter
        if ($request->filled('status') && $request->get('status') != '') {
            $query->where('status', $request->get('status'));
        }

        $equipment = $query->paginate(20);

        // Calculate stats
        $totalEquipment = Equipment::count();
        $availableCount = Equipment::where('status', 'available')->count();
        $maintenanceCount = Equipment::where('status', 'maintenance')->count();

        return view('admin.equipment', compact('equipment', 'totalEquipment', 'availableCount', 'maintenanceCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'equipment_code' => 'required|string|unique:equipment,equipment_code',
            'name' => 'required|string',
            'category' => 'nullable|string',
            'manufacturer' => 'nullable|string',
            'model' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'asset_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'location' => 'nullable|string',
            'responsible_person' => 'nullable|integer',
            'status' => 'nullable|in:available,in_use,maintenance,retired',
            'calibration_required' => 'nullable|boolean',
            'last_maintenance' => 'nullable|date',
            'next_maintenance' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $data['calibration_required'] = (bool) ($data['calibration_required'] ?? false);

        $equipment = Equipment::create($data);

        AuditLogHelper::log(
            action: 'CREATE',
            modelType: 'Equipment',
            modelId: $equipment->id,
            description: "Registered equipment: {$data['name']} (Code: {$data['equipment_code']})",
            newValues: $data
        );

        return redirect()->route('admin.equipment.index')->with('status', 'Equipment registered');
    }

    public function destroy(Equipment $equipment)
    {
        $equipmentData = $equipment->toArray();
        $equipmentId = $equipment->id;
        $equipmentName = $equipment->name;
        
        $equipment->delete();

        AuditLogHelper::log(
            action: 'DELETE',
            modelType: 'Equipment',
            modelId: $equipmentId,
            description: "Deleted equipment: {$equipmentName}",
            oldValues: $equipmentData
        );

        return redirect()->route('admin.equipment.index')->with('status', 'Equipment deleted');
    }

    public function calibrate(Request $request, Equipment $equipment)
    {
        $oldValues = [
            'last_maintenance' => $equipment->last_maintenance,
            'next_maintenance' => $equipment->next_maintenance,
            'status' => $equipment->status,
        ];

        $data = $request->validate([
            'last_maintenance' => 'required|date',
            'next_maintenance' => 'nullable|date',
            'status' => 'nullable|in:available,in_use,maintenance,retired',
            'notes' => 'nullable|string',
        ]);

        $equipment->update([
            'last_maintenance' => $data['last_maintenance'],
            'next_maintenance' => $data['next_maintenance'] ?? $equipment->next_maintenance,
            'status' => $data['status'] ?? $equipment->status,
            'notes' => $data['notes'] ?? $equipment->notes,
        ]);

        AuditLogHelper::log(
            action: 'CALIBRATE',
            modelType: 'Equipment',
            modelId: $equipment->id,
            description: "Updated calibration for: {$equipment->name}",
            oldValues: $oldValues,
            newValues: $data
        );

        return redirect()->route('admin.equipment.index')->with('status', 'Calibration updated');
    }
}
