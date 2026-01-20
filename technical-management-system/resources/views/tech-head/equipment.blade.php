@extends('layouts.dashboard')

@section('title', 'Equipment')

@section('page-title', 'Equipment')
@section('page-subtitle', 'Inventory status and utilization')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    <div x-data="{ showAdd:false, showEdit:false, showStatus:false, showLocation:false, selectedId:null, selectedEquipment:null }" class="space-y-6">
        
        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $equipmentStats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-[20px] shadow-md border border-emerald-200 dark:border-emerald-800 p-5">
                <p class="text-sm text-emerald-900 dark:text-emerald-200">Available</p>
                <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100 mt-1">{{ $equipmentStats['available'] ?? 0 }}</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-[20px] shadow-md border border-blue-200 dark:border-blue-800 p-5">
                <p class="text-sm text-blue-900 dark:text-blue-200">In Use</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $equipmentStats['in_use'] ?? 0 }}</p>
            </div>
            <div class="bg-amber-50 dark:bg-amber-900/30 rounded-[20px] shadow-md border border-amber-200 dark:border-amber-800 p-5">
                <p class="text-sm text-amber-900 dark:text-amber-200">Maintenance</p>
                <p class="text-3xl font-bold text-amber-900 dark:text-amber-100 mt-1">{{ $equipmentStats['maintenance'] ?? 0 }}</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button @click="showAdd=true" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add Equipment</button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Equipment Inventory</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Complete list of all equipment</p>
                </div>
            </div>
            
            @if($equipment->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr class="text-left">
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Name</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Code</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Category</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Location</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400">Updated</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($equipment as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="py-3">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->name ?? 'N/A' }}</p>
                                    @if($item->calibration_required)
                                        <p class="text-xs text-blue-600 dark:text-blue-400">\ud83d\udd0d Cal. Required</p>
                                    @endif
                                </td>
                                <td class="py-3"><p class="text-sm text-gray-700 dark:text-gray-300">{{ $item->equipment_code ?? 'N/A' }}</p></td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ ($item->status ?? '') === 'available' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' : '' }}
                                        {{ ($item->status ?? '') === 'in_use' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                        {{ ($item->status ?? '') === 'maintenance' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                        {{ ($item->status ?? '') === 'retired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200' : '' }}">
                                        {{ ucfirst($item->status ?? 'unknown') }}
                                    </span>
                                </td>
                                <td class="py-3"><p class="text-sm text-gray-700 dark:text-gray-300">{{ $item->category ?? 'N/A' }}</p></td>
                                <td class="py-3"><p class="text-sm text-gray-700 dark:text-gray-300">{{ $item->location ?? '-' }}</p></td>
                                <td class="py-3"><p class="text-sm text-gray-700 dark:text-gray-300">{{ optional($item->updated_at)->format('M d, Y') }}</p></td>
                                <td class="py-3">
                                    <div class="flex gap-2 justify-end">
                                        <button @click="selectedId={{ $item->id }}; showEdit=true;" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">Edit</button>
                                        <button @click="selectedId={{ $item->id }}; showStatus=true;" class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">Status</button>
                                        <form method="POST" action="{{ route('tech-head.equipment.destroy', $item->id) }}" onsubmit="return confirm('Delete equipment?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-medium">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $equipment->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No equipment found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding your first equipment.</p>
                <button @click="showAdd=true" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Add Equipment</button>
            </div>
            @endif
        </div>

        <!-- Add Equipment Modal -->
        <div 
            x-show="showAdd" 
            x-cloak
            @keydown.escape.window="showAdd=false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAdd=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700 max-h-[85vh] overflow-y-auto"
                >
                <form method="POST" action="{{ route('tech-head.equipment.store') }}" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add New Equipment</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Equipment Code *</label>
                            <input name="equipment_code" placeholder="EQ-001" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                            <input name="name" placeholder="Equipment name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <input name="category" placeholder="e.g., Calibration, Testing" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                                <option value="available">Available</option>
                                <option value="in_use">In Use</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="retired">Retired</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manufacturer</label>
                            <input name="manufacturer" placeholder="Manufacturer name" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Model</label>
                            <input name="model" placeholder="Model number" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input name="location" placeholder="Storage location" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="calibration_required" value="1" id="calibration_required" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <label for="calibration_required" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Requires Regular Calibration</label>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="showAdd=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Create Equipment</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Edit Equipment Modal -->
        <div 
            x-show="showEdit" 
            x-cloak
            @keydown.escape.window="showEdit=false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEdit=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" :action="selectedId ? '{{ url('/tech-head/equipment') }}/' + selectedId : '#'" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Equipment</h3>
                    <input name="name" placeholder="Name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="retired">Retired</option>
                    </select>
                    <input name="location" placeholder="Location" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showEdit=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Update</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Status Modal -->
        <div 
            x-show="showStatus" 
            x-cloak
            @keydown.escape.window="showStatus=false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showStatus=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" :action="selectedId ? '{{ url('/tech-head/equipment') }}/' + selectedId + '/status' : '#'" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Update Status</h3>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white">
                        <option value="available">Available</option>
                        <option value="in_use">In Use</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="retired">Retired</option>
                    </select>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showStatus=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium">Update</button>
                    </div>
                </form>
                </div>
            </div>
        </div>

        <!-- Location Modal -->
        <div 
            x-show="showLocation" 
            x-cloak
            @keydown.escape.window="showLocation=false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showLocation=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                <form method="POST" :action="selectedId ? '{{ url('/tech-head/equipment') }}/' + selectedId + '/location' : '#'" class="p-6 space-y-4">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Update Location</h3>
                    <input name="location" placeholder="Location" required class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white" />
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showLocation=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Save</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
