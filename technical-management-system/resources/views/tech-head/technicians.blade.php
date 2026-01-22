@extends('layouts.dashboard')

@section('title', 'Technicians')

@section('page-title', 'Technicians')
@section('page-subtitle', 'Team roster and performance overview')

@section('sidebar-nav')
    @include('tech-head.partials.sidebar')
@endsection

@section('content')
    @php
        $totalTechnicians = $technicians->count();
        $activeAssignments = $technicianStats->sum('active');
        $completedAssignments = $technicianStats->sum('completed');
    @endphp
    <div x-data="{ 
        showAdd: false, 
        showAvailability: false, 
        showSkills: false, 
        showDisable: false,
        showDetails: false,
        selectedId: null,
        selectedName: null,
        selectedTechnician: null,
        init() {
            this.$watch('showAdd', value => this.handleModalState(value));
            this.$watch('showAvailability', value => this.handleModalState(value));
            this.$watch('showSkills', value => this.handleModalState(value));
            this.$watch('showDisable', value => this.handleModalState(value));
            this.$watch('showDetails', value => this.handleModalState(value));
        },
        handleModalState(isOpen) {
            if (isOpen) {
                document.body.style.overflow = 'hidden';
                this.setupEscapeKey();
            } else {
                document.body.style.overflow = 'auto';
            }
        },
        setupEscapeKey() {
            const handler = (e) => {
                if (e.key === 'Escape') {
                    this.closeAllModals();
                    document.removeEventListener('keydown', handler);
                }
            };
            document.addEventListener('keydown', handler);
        },
        closeAllModals() {
            this.showAdd = false;
            this.showAvailability = false;
            this.showSkills = false;
            this.showDisable = false;
            this.showDetails = false;
        },
        openDetails(technician) {
            this.selectedTechnician = technician;
            this.showDetails = true;
        }
    }" class="space-y-6">
        <!-- Quick stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Technicians</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalTechnicians }}</p>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-[20px] shadow-md border border-blue-200 dark:border-blue-800 p-5">
                <p class="text-sm text-blue-900 dark:text-blue-200">Active Assignments</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100 mt-1">{{ $activeAssignments }}</p>
            </div>
            <div class="bg-emerald-50 dark:bg-emerald-900/30 rounded-[20px] shadow-md border border-emerald-200 dark:border-emerald-800 p-5">
                <p class="text-sm text-emerald-900 dark:text-emerald-200">Completed Assignments</p>
                <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100 mt-1">{{ $completedAssignments }}</p>
            </div>
        </div>

        <!-- Search Bar and Add Button -->
        <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
            <!-- Search Bar -->
            <form method="GET" action="{{ route('tech-head.technicians') }}" class="flex-1 max-w-2xl">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') ?? '' }}"
                        placeholder="Search by name, email, role, or skills..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
            
            <!-- Add Button -->
            <button 
                @click="showAdd=true" 
                class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg text-sm font-semibold hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 flex items-center gap-2 whitespace-nowrap"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Technician</span>
            </button>
        </div>
        
        @if(request('search'))
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span>Search results for: <strong class="text-gray-900 dark:text-white">"{{ request('search') }}"</strong></span>
                <a href="{{ route('tech-head.technicians') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Clear</a>
            </div>
        @endif

        <!-- Technicians table -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Team Members</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Name</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Email</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Role</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Active</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Completed</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Total</th>
                            <th class="pb-3 text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($technicians as $tech)
                            @php
                                $stats = $technicianStats[$tech->id] ?? null;
                                $active = $stats->active ?? 0;
                                $completed = $stats->completed ?? 0;
                                $total = $stats->total_assignments ?? ($active + $completed);
                            @endphp
                            <tr 
                                @click="openDetails({{ json_encode([
                                    'id' => $tech->id,
                                    'name' => $tech->name,
                                    'email' => $tech->email,
                                    'role' => $tech->role->name ?? 'Technician',
                                    'active' => $active,
                                    'completed' => $completed,
                                    'total' => $total,
                                    'created_at' => $tech->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A')
                                ]) }})"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                            >
                                <td class="py-3 text-center">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $tech->name }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $tech->email }}</p>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tech->role->name ?? 'Technician' }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">{{ $active }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">{{ $completed }}</span>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200">{{ $total }}</span>
                                </td>
                                <td class="py-3 text-center" @click.stop>
                                    <div class="flex gap-3 justify-center">
                                        <button @click="selectedId={{ $tech->id }}; showSkills=true;" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">Skills</button>
                                        <button @click="selectedId={{ $tech->id }}; showAvailability=true;" class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">Availability</button>
                                        <button @click="selectedId={{ $tech->id }}; selectedName='{{ $tech->name }}'; showDisable=true;" class="text-rose-600 dark:text-rose-400 hover:underline text-xs font-medium">Disable</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Technician Modal -->
        <div 
            x-show="showAdd" 
            x-cloak
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
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Technician</h3>
                            <button @click="showAdd=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form method="POST" action="{{ route('tech-head.technicians.store') }}" class="space-y-4">
                            @csrf
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Full Name</label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        required 
                                        placeholder="Enter full name"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Email Address</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        required 
                                        placeholder="email@example.com"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Password</label>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        required 
                                        placeholder="Enter password"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Confirm Password</label>
                                    <input 
                                        type="password" 
                                        name="password_confirmation" 
                                        required 
                                        placeholder="Confirm password"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Phone Number</label>
                                    <input 
                                        type="tel" 
                                        name="phone" 
                                        placeholder="+63 XXX XXX XXXX"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Department</label>
                                    <select 
                                        name="department" 
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    >
                                        <option value="">Select department</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Plumbing">Plumbing</option>
                                        <option value="HVAC">HVAC</option>
                                        <option value="IT">IT Support</option>
                                        <option value="General Maintenance">General Maintenance</option>
                                    </select>
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Skills (comma-separated)</label>
                                    <input 
                                        type="text" 
                                        name="skills" 
                                        placeholder="e.g., Electrical, Plumbing, HVAC, Carpentry"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Separate multiple skills with commas</p>
                                </div>
                                
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Address</label>
                                    <textarea 
                                        name="address" 
                                        rows="2"
                                        placeholder="Enter complete address"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    ></textarea>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showAdd=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Add Technician</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Availability Modal -->
        <div 
            x-show="showAvailability" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAvailability=false"></div>
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
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Set Availability</h3>
                            <button @click="showAvailability=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form method="POST" :action="selectedId ? '{{ url('/tech-head/technicians') }}/' + selectedId + '/availability' : '#'" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Availability Status</label>
                                <select 
                                    name="availability" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                >
                                    <option value="available">Available</option>
                                    <option value="on_leave">On Leave</option>
                                                <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showAvailability=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors">Update Availability</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skills Modal -->
        <div 
            x-show="showSkills" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showSkills=false"></div>
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
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Update Skills</h3>
                            <button @click="showSkills=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <form method="POST" :action="selectedId ? '{{ url('/tech-head/technicians') }}/' + selectedId + '/skills' : '#'" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1 font-semibold">Skills (comma-separated)</label>
                                <input 
                                    type="text" 
                                    name="skills" 
                                    placeholder="e.g., Electrical, Plumbing, HVAC, Carpentry"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Separate multiple skills with commas</p>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" @click="showSkills=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Save Skills</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disable Confirmation Modal -->
        <div 
            x-show="showDisable" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDisable=false"></div>
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
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-rose-100 dark:bg-rose-900/30 rounded-full">
                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        
                        <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Disable Technician?</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Are you sure you want to disable <strong class="text-gray-900 dark:text-white" x-text="selectedName"></strong>? This action will prevent them from being assigned to new work orders.
                            </p>
                        </div>
                        
                        <form method="POST" :action="selectedId ? '{{ url('/tech-head/technicians') }}/' + selectedId + '/disable' : '#'" class="space-y-4">
                            @csrf
                            
                            <div class="flex justify-center gap-3 pt-4">
                                <button type="button" @click="showDisable=false" class="px-6 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">Cancel</button>
                                <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium hover:bg-rose-700 transition-colors">Yes, Disable</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technician Details Modal -->
        <div 
            x-show="showDetails" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDetails=false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-[20px] shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Technician Details</h3>
                            <button @click="showDetails=false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedTechnician?.name"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedTechnician?.email"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Role</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedTechnician?.role"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created At</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300" x-text="selectedTechnician?.created_at"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Active Assignments</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200" x-text="selectedTechnician?.active || 0"></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Completed Assignments</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200" x-text="selectedTechnician?.completed || 0"></span>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Assignments</p>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200" x-text="selectedTechnician?.total || 0"></span>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button @click="showDetails=false; selectedId=selectedTechnician.id; showAvailability=true;" class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700">Set Availability</button>
                            <button @click="showDetails=false; selectedId=selectedTechnician.id; showSkills=true;" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Update Skills</button>
                            <button @click="showDetails=false" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg text-sm font-medium">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
