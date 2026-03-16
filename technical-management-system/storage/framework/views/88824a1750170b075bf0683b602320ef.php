

<?php $__env->startSection('title', 'Backup and Restore'); ?>

<?php $__env->startSection('page-title', 'Backup and Restore'); ?>

<?php $__env->startSection('page-subtitle', 'Create full system backups and restore from ZIP packages'); ?>

<?php $__env->startSection('sidebar-nav'); ?>
    <?php echo $__env->make('admin.sidebar-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">System Backup</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Backup package includes database.sql, storage folder, and .env file.</p>
        </div>
        <a href="<?php echo e(route('admin.settings.index')); ?>" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
            Back to Settings
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800 dark:border-green-700 dark:bg-green-900/30 dark:text-green-200">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-200">
            <ul class="list-disc pl-5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create Backup</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Creates a ZIP: backup/database.sql, backup/storage, backup/.env</p>
            <form action="<?php echo e(route('admin.settings.backup.create')); ?>" method="POST" class="mt-4">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Create Backup Now
                </button>
            </form>
        </div>

        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-700 dark:bg-yellow-900/20">
            <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-200">Restore Backup</h3>
            <p class="mt-1 text-sm text-yellow-800 dark:text-yellow-300">Upload a ZIP backup. This replaces database, storage, and .env.</p>
            <form action="<?php echo e(route('admin.settings.backup.restore')); ?>" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3" onsubmit="return confirm('Restore backup now? This will overwrite current data.');">
                <?php echo csrf_field(); ?>
                <input type="file" name="backup_file" accept=".zip" required class="block w-full rounded-lg border border-yellow-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:border-yellow-700 dark:bg-gray-900 dark:text-white">
                <button type="submit" class="inline-flex items-center rounded-lg bg-yellow-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-yellow-700">
                    Restore Backup
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Available Backups</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($backups->count()); ?> file(s)</span>
        </div>

        <?php if($backups->isEmpty()): ?>
            <p class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                No backup files found in storage/app/backups.
            </p>
        <?php else: ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-col gap-3 rounded-lg border border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white"><?php echo e($backup['file']); ?></p>
                            <p class="text-sm text-gray-600 dark:text-gray-400"><?php echo e($backup['size']); ?> | <?php echo e($backup['created_at']); ?></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="<?php echo e(route('admin.settings.backup.download', $backup['file'])); ?>" class="rounded-lg border border-blue-200 px-3 py-1.5 text-sm font-medium text-blue-700 transition hover:bg-blue-50 dark:border-blue-700 dark:text-blue-300 dark:hover:bg-blue-900/30">
                                Download
                            </a>
                            <form action="<?php echo e(route('admin.settings.backup.delete', $backup['file'])); ?>" method="POST" onsubmit="return confirm('Delete this backup file?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-sm font-medium text-red-700 transition hover:bg-red-50 dark:border-red-700 dark:text-red-300 dark:hover:bg-red-900/30">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\GitHub\technical-management-system\technical-management-system\resources\views/admin/backup-settings.blade.php ENDPATH**/ ?>