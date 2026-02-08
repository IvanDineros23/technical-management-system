@extends('layouts.dashboard')

@section('title', 'Calendar')

@section('page-title', 'Job Calendar')
@section('page-subtitle', 'View your schedule and assignments')

@section('head')
    <script>
        function calendarPage() {
            return {
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                selectedDate: null,
                jobs: @json($assignments),
                getDaysInMonth() {
                    return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                },
                getFirstDayOfMonth() {
                    return new Date(this.currentYear, this.currentMonth, 1).getDay();
                },
                getCalendarDays() {
                    const daysInMonth = this.getDaysInMonth();
                    const firstDay = this.getFirstDayOfMonth();
                    const days = [];
                    
                    // Add empty cells for days before the first day of the month
                    for (let i = 0; i < firstDay; i++) {
                        days.push({ day: null, date: null });
                    }
                    
                    // Add actual days of the month
                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        days.push({ day: day, date: dateStr });
                    }
                    
                    return days;
                },
                getJobsForDate(dateStr) {
                    return this.jobs.filter(j => j.date === dateStr);
                },
                isToday(dateStr) {
                    const today = new Date();
                    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                    return dateStr === todayStr;
                },
                getMonthName() {
                    const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                                    'July', 'August', 'September', 'October', 'November', 'December'];
                    return months[this.currentMonth];
                },
                previousMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                },
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                },
                goToToday() {
                    this.currentMonth = new Date().getMonth();
                    this.currentYear = new Date().getFullYear();
                }
            }
        }
    </script>
@endsection

@section('sidebar-nav')
    <a href="{{ route('technician.dashboard') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
    </a>
    <a href="{{ route('technician.assignments') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        My Assignments
    </a>
    <a href="{{ route('technician.work-orders') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Work Orders
    </a>
    <a href="{{ route('technician.maintenance') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Maintenance Tasks
    </a>
    <a href="{{ route('technician.equipment') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
        </svg>
        Equipment
    </a>
    <a href="{{ route('technician.inventory') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        Inventory
    </a>
    <a href="{{ route('technician.reports') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Reports
    </a>

    <a href="{{ route('technician.certificates') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
        Certificates
    </a>
    <a href="{{ route('technician.calendar') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white shadow-md dark:bg-blue-700 dark:shadow-blue-900/30">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Calendar
    </a>
    <a href="{{ route('technician.timeline') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 dark:text-gray-300 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Timeline
    </a>
@endsection

@section('content')
    <div x-data="calendarPage()" x-init="console.log('Calendar initialized')">
        <!-- Calendar -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white" x-text="getMonthName() + ' ' + currentYear"></h3>
                <div class="flex gap-2">
                    <button @click="previousMonth()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Previous</button>
                    <button @click="goToToday()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Today</button>
                    <button @click="nextMonth()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Next</button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2 mb-2">
                <!-- Day Headers -->
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">SUN</div>
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">MON</div>
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">TUE</div>
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">WED</div>
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">THU</div>
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">FRI</div>
                <div class="text-center py-3 font-bold text-gray-700 dark:text-gray-300 text-sm border-b-2 border-gray-300 dark:border-gray-600">SAT</div>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 gap-2">
                <template x-for="(dayData, index) in getCalendarDays()" :key="index">
                    <div :class="dayData.day ? (isToday(dayData.date) ? 'min-h-[80px] md:min-h-[100px] p-3 border-2 border-blue-500 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-400 rounded-lg shadow-lg hover:shadow-xl cursor-pointer transition-all' : 'min-h-[80px] md:min-h-[100px] p-3 border-2 border-gray-200 dark:border-gray-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-400 cursor-pointer transition-all') : 'min-h-[80px] md:min-h-[100px] bg-gray-50 dark:bg-gray-900/20 rounded-lg'">
                        <div x-show="dayData.day">
                            <div :class="isToday(dayData.date) ? 'text-base font-bold text-blue-600 dark:text-blue-400 mb-2 flex items-center gap-2' : 'text-base font-bold text-gray-900 dark:text-white mb-2'">
                                <span x-text="dayData.day"></span>
                                <span x-show="isToday(dayData.date)" class="text-xs bg-blue-600 text-white px-2 py-0.5 rounded-full">Today</span>
                            </div>
                            <div x-show="getJobsForDate(dayData.date).length > 0" class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold" x-text="getJobsForDate(dayData.date).length + ' job' + (getJobsForDate(dayData.date).length > 1 ? 's' : '')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Upcoming Jobs -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Upcoming Jobs</h4>
                <div class="space-y-3">
                    <template x-if="jobs.length > 0">
                        <div class="space-y-3">
                            <template x-for="job in jobs" :key="job.date">
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="job.title"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="job.date + ' at ' + job.time"></p>
                                        </div>
                                        <button class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline">View</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="jobs.length === 0">
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No scheduled jobs</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Check back soon for new assignments</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endsection
