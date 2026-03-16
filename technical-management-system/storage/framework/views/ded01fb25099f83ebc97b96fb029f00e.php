

<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('page-title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-subtitle', 'System administration, user management, and compliance monitoring'); ?>

<?php $__env->startSection('sidebar-nav'); ?>
    <?php echo $__env->make('admin.sidebar-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Current Date & Time Display -->
    <div class="text-right mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <span id="current-datetime"></span>
        </p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Users -->
        <a href="<?php echo e(route('admin.users.index')); ?>" class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M7 20H2v-2a3 3 0 015.856-1.487M12 14a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['total_users'] ?? 0); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
                </div>
            </div>
        </a>

        <!-- Active Users -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['active_users'] ?? 0); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Active Users</p>
                </div>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo e($stats['inactive_users'] ?? 0); ?></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Inactive Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">🏥 System Status</h3>
        </div>

        <div class="space-y-3">
            <?php $__currentLoopData = $systemStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($service['name']); ?></p>
                    <p class="text-xs text-gray-600 dark:text-gray-400"><?php echo e($service['message']); ?></p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($service['status'] === 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200'); ?>">
                    <?php echo e(ucfirst($service['status'])); ?>

                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Recent User Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">👥 Recent User Activity</h3>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">View all →</a>
        </div>

        <?php if($recentUserActivity->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-center">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr class="text-center text-xs">
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">User</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Email</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Role</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Status</th>
                            <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Last Login</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php $__currentLoopData = $recentUserActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="py-3 font-semibold text-gray-900 dark:text-white text-center"><?php echo e($user->name); ?></td>
                            <td class="py-3 text-gray-700 dark:text-gray-300 text-sm"><?php echo e($user->email); ?></td>
                            <td class="py-3 text-gray-700 dark:text-gray-300 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                                    <?php echo e($user->role); ?>

                                </span>
                            </td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200'); ?>">
                                    <?php echo e($user->is_active ? 'Active' : 'Inactive'); ?>

                                </span>
                            </td>
                            <td class="py-3 text-gray-600 dark:text-gray-400 text-xs"><?php echo e($user->last_login); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No user activity</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Audit Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-[20px] shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">📋 Audit Logs</h3>
            <a href="<?php echo e(route('admin.audit-logs.index')); ?>" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-medium">View all →</a>
        </div>

        <?php if($auditActivity->count() > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $auditActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0 last:pb-0">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full 
                            <?php if($audit->action === 'CREATE'): ?> bg-green-600 dark:bg-green-400
                            <?php elseif($audit->action === 'UPDATE'): ?> bg-blue-600 dark:bg-blue-400
                            <?php elseif($audit->action === 'DELETE'): ?> bg-red-600 dark:bg-red-400
                            <?php else: ?> bg-gray-600 dark:bg-gray-400
                            <?php endif; ?> mt-1.5 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white font-semibold">
                                <?php
                                    $desc = $audit->description;
                                    // Replace generic patterns with user-specific descriptions
                                    if (str_contains(strtolower($desc), 'user logged in')) {
                                        $desc = $audit->user_name . ' has logged in';
                                    } elseif (str_contains(strtolower($desc), 'user logged out')) {
                                        $desc = $audit->user_name . ' has logged out';
                                    }
                                ?>
                                <?php echo e($desc); ?>

                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-500"><?php echo e($audit->created_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A'); ?></span>
                                <span class="text-xs text-gray-500 dark:text-gray-500"><?php echo e($audit->created_at?->diffForHumans() ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">No audit activity</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Admin Actions -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/30 rounded-[20px] border border-blue-200 dark:border-blue-800 p-6">
        <h3 class="text-sm font-bold text-blue-900 dark:text-blue-200 mb-3">⚡ Quick Admin Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <a href="<?php echo e(route('admin.users.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                👥 Manage Users →
            </a>
            <a href="<?php echo e(route('admin.roles.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                🔐 Manage Roles →
            </a>
            <a href="<?php echo e(route('admin.settings.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                ⚙️ Settings →
            </a>
            <a href="<?php echo e(route('admin.audit-logs.index')); ?>" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                📊 Audit Logs →
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Display current date and time in Asia/Manila timezone
    function updateDateTime() {
        const now = new Date();
        const formatter = new Intl.DateTimeFormat('en-US', {
            timeZone: 'Asia/Manila',
            year: 'numeric',
            month: 'long',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });
        
        const datetimeStr = formatter.format(now);
        document.getElementById('current-datetime').textContent = datetimeStr;
    }
    
    // Update on page load and every second
    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\GitHub\technical-management-system\technical-management-system\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>