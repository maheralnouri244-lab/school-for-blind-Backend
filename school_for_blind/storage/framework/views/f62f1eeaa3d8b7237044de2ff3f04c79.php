<!DOCTYPE html>
<html lang="ar" dir="rtl" id="htmlTag">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SESB - لوحة القيادة</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">

    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark'; // Default to dark mode
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-bs-theme', savedTheme); // Tells Bootstrap to use dark mode too
    </script>
</head>
<body>

    <div class="d-flex" style="min-height: 100vh;">
        
        <aside class="sidebar-wrapper flex-shrink-0 d-none d-lg-flex flex-column h-100">
            <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>

        <div class="flex-grow-1 d-flex flex-column overflow-hidden">
            
            <?php echo $__env->make('partials.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="flex-grow-1 p-4 overflow-y-auto">
                <?php echo $__env->yieldContent('content'); ?>
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleTheme() {
            const htmlTag = document.getElementById('htmlTag');
            const currentTheme = htmlTag.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update HTML attributes
            htmlTag.setAttribute('data-theme', newTheme);
            htmlTag.setAttribute('data-bs-theme', newTheme);
            
            // Save to browser memory so it remembers their choice next time
            localStorage.setItem('theme', newTheme);
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\project\laravel\school-for-blind-Backend\school_for_blind\resources\views/layouts/app.blade.php ENDPATH**/ ?>