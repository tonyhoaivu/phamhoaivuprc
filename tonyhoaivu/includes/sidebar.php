<?php
/**
 * TonyHoaiVu CMS - Frontend Sidebar Component
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

$db = get_db();

// Fetch Admin Profile for author box
$author_stmt = $db->query("SELECT display_name, bio, avatar FROM users WHERE role = 'administrator' ORDER BY id ASC LIMIT 1");
$author = $author_stmt->fetch();

// Fetch featured/popular posts
$featured_stmt = $db->query("SELECT id, title, slug, featured_image, created_at, views_count FROM posts WHERE status = 'publish' ORDER BY views_count DESC, id DESC LIMIT 4");
$popular_posts = $featured_stmt->fetchAll();

// Fetch categories with post count
$cats_stmt = $db->query("SELECT c.id, c.name, c.slug, COUNT(p.id) as total FROM categories c LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'publish' GROUP BY c.id ORDER BY total DESC, c.name ASC");
$all_cats = $cats_stmt->fetchAll();
?>
<aside class="site-sidebar space-y-8">
    <!-- Author Bio Card -->
    <?php if ($author): ?>
    <div class="sidebar-widget p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <div class="flex items-center gap-4 mb-4">
            <img src="<?= e($author['avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200') ?>" alt="<?= e($author['display_name']) ?>" class="w-14 h-14 rounded-full object-cover ring-2 ring-slate-100 dark:ring-slate-800 shadow-sm" referrerpolicy="no-referrer">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-base leading-snug"><?= e($author['display_name']) ?></h3>
                <span class="text-xs text-slate-500 dark:text-slate-400">Senior Full-stack Engineer</span>
            </div>
        </div>
        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
            <?= e($author['bio'] ?: 'Đam mê công nghệ, kiến trúc web hiệu năng cao và chia sẻ kiến thức cộng đồng lập trình.') ?>
        </p>
        <a href="<?= BASE_URL ?>/page/gioi-thieu" class="text-xs font-semibold text-accent hover:underline flex items-center gap-1">
            Tìm hiểu thêm về tôi &rarr;
        </a>
    </div>
    <?php endif; ?>

    <!-- Search Box Widget -->
    <div class="sidebar-widget p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3">Tìm kiếm bài viết</h4>
        <form action="<?= BASE_URL ?>/search.php" method="GET" class="relative">
            <input type="text" name="q" placeholder="Từ khóa tìm kiếm..." required class="w-full pl-3.5 pr-10 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent text-slate-900 dark:text-white">
            <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    <!-- Popular Posts Widget -->
    <div class="sidebar-widget p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-4 flex items-center justify-between">
            <span>Bài viết nổi bật</span>
            <span class="text-[11px] text-slate-400 font-normal">Lượt xem</span>
        </h4>
        <div class="space-y-4">
            <?php foreach ($popular_posts as $p_post): ?>
            <a href="<?= BASE_URL ?>/post/<?= e($p_post['slug']) ?>" class="group flex gap-3 items-start">
                <?php if (!empty($p_post['featured_image'])): ?>
                <img src="<?= e($p_post['featured_image']) ?>" alt="<?= e($p_post['title']) ?>" class="w-16 h-14 rounded-lg object-cover shrink-0 group-hover:opacity-90 transition-opacity" referrerpolicy="no-referrer">
                <?php else: ?>
                <div class="w-16 h-14 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <?php endif; ?>
                <div class="min-w-0">
                    <h5 class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-accent transition-colors line-clamp-2 leading-snug mb-1">
                        <?= e($p_post['title']) ?>
                    </h5>
                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                        <span><?= format_date($p_post['created_at']) ?></span>
                        <span>·</span>
                        <span><?= number_format($p_post['views_count']) ?> xem</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Categories List Widget -->
    <div class="sidebar-widget p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3">Chuyên mục</h4>
        <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
            <?php foreach ($all_cats as $cat_item): ?>
            <a href="<?= BASE_URL ?>/category/<?= e($cat_item['slug']) ?>" class="py-2.5 flex justify-between items-center text-slate-600 dark:text-slate-300 hover:text-accent dark:hover:text-accent-light transition-colors">
                <span><?= e($cat_item['name']) ?></span>
                <span class="px-2 py-0.5 rounded-full text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono">
                    <?= (int)$cat_item['total'] ?>
                </span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</aside>
