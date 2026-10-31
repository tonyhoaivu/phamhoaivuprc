<?php
/**
 * TonyHoaiVu CMS - Frontend Header Template
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

$site_title = get_setting('site_title', 'TonyHoaiVu');
$site_tagline = get_setting('site_tagline', 'Kỹ thuật phần mềm & Kiến trúc hệ thống');
$site_desc = !empty($page_desc) ? $page_desc : get_setting('site_description', 'Blog cá nhân chia sẻ kiến thức công nghệ');
$current_title = !empty($page_title) ? $page_title . " · " . $site_title : $site_title . " — " . $site_tagline;
$header_menus = get_menus('header');
$is_admin_logged = is_logged_in();
?>
<!DOCTYPE html>
<html lang="vi" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($current_title) ?></title>
    <meta name="description" content="<?= e($site_desc) ?>">
    <meta property="og:title" content="<?= e($current_title) ?>">
    <meta property="og:description" content="<?= e($site_desc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e((isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
    <?php if (!empty($og_image)): ?>
    <meta property="og:image" content="<?= e($og_image) ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <script>
        // Check local storage for theme
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-surface text-main antialiased selection:bg-accent selection:text-white transition-colors duration-200">

<?php if ($is_admin_logged): ?>
<!-- Admin Quick Bar for Logged-in Staff -->
<div class="admin-quick-bar">
    <div class="container flex justify-between items-center text-xs">
        <div class="flex items-center gap-4">
            <a href="<?= ADMIN_URL ?>/" class="font-semibold text-accent-light">⚡ CMS Admin Dashboard</a>
            <?php if (!empty($post['id'])): ?>
            <a href="<?= ADMIN_URL ?>/post-edit.php?id=<?= $post['id'] ?>">Chỉnh sửa bài viết này</a>
            <?php endif; ?>
            <a href="<?= ADMIN_URL ?>/post-new.php">+ Viết bài mới</a>
        </div>
        <div>
            <span>Xin chào, <strong><?= e($_SESSION['display_name']) ?></strong></span>
            <a href="<?= ADMIN_URL ?>/logout.php" class="ml-3 text-red-300 hover:text-red-100">Đăng xuất</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main Navigation Header -->
<header class="site-header sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800">
    <div class="container flex items-center justify-between h-16">
        <!-- Brand Logo -->
        <a href="<?= BASE_URL ?>/" class="brand-link flex items-center gap-2.5 group">
            <div class="w-9 h-9 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-bold text-lg shadow-sm group-hover:scale-105 transition-transform">
                T
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-lg tracking-tight text-slate-900 dark:text-white leading-tight">TonyHoaiVu</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Software Engineer &amp; Blog</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300">
            <?php foreach ($header_menus as $menu): ?>
            <a href="<?= (str_starts_with($menu['url'], 'http') || str_starts_with($menu['url'], '/')) ? e($menu['url']) : BASE_URL . '/' . e(ltrim($menu['url'], '/')) ?>" target="<?= e($menu['target']) ?>" class="nav-link hover:text-slate-900 dark:hover:text-white transition-colors">
                <?= e($menu['title']) ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Right Action Controls -->
        <div class="flex items-center gap-3">
            <!-- Search Trigger -->
            <button id="search-btn" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Tìm kiếm bài viết">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>

            <!-- Dark / Light Mode Toggle -->
            <button id="theme-toggle" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Đổi giao diện Sáng / Tối">
                <svg id="theme-icon-sun" class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg id="theme-icon-moon" class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>

            <!-- Mobile Menu Toggle -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="mobile-menu" class="hidden md:hidden border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-6 py-4 space-y-3">
    <?php foreach ($header_menus as $menu): ?>
    <a href="<?= (str_starts_with($menu['url'], 'http') || str_starts_with($menu['url'], '/')) ? e($menu['url']) : BASE_URL . '/' . e(ltrim($menu['url'], '/')) ?>" class="block py-2 text-sm font-medium text-slate-700 dark:text-slate-200 hover:text-accent">
        <?= e($menu['title']) ?>
    </a>
    <?php endforeach; ?>
    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
        <a href="<?= ADMIN_URL ?>/" class="block py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white">
            Trang Quản trị (Admin)
        </a>
    </div>
</div>

<!-- Global Search Modal Popup -->
<div id="search-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm hidden flex items-start justify-center pt-24 px-4">
    <div class="bg-white dark:bg-slate-900 w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <form action="<?= BASE_URL ?>/search.php" method="GET" class="p-4 flex items-center border-b border-slate-100 dark:border-slate-800">
            <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm bài viết..." class="w-full bg-transparent text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none text-base" autofocus>
            <button type="button" id="close-search-btn" class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-white px-2 py-1">ESC</button>
        </form>
        <div class="p-4 text-xs text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-950/50">
            Gợi ý: <span class="text-slate-700 dark:text-slate-300">PHP 8, MySQL, Docker, Clean Architecture, Security</span>
        </div>
    </div>
</div>
