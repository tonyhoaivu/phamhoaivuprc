<?php
/**
 * TonyHoaiVu CMS - Category Archive Page
 */

define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = get_db();

// Fetch category
$stmt = $db->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
$stmt->execute(['slug' => $slug]);
$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    $page_title = 'Không tìm thấy chuyên mục';
    require_once CMS_ROOT . '/includes/header.php';
    echo '<div class="container py-24 text-center"><h1 class="text-3xl font-bold mb-4">404 - Chuyên mục không tồn tại</h1><a href="' . BASE_URL . '/" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-semibold">Về trang chủ</a></div>';
    require_once CMS_ROOT . '/includes/footer.php';
    exit;
}

// Fetch posts in this category
$post_stmt = $db->prepare("
    SELECT p.*, u.display_name AS author_name, u.avatar AS author_avatar
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    WHERE p.category_id = :cat_id AND p.status = 'publish'
    ORDER BY p.created_at DESC
");
$post_stmt->execute(['cat_id' => $category['id']]);
$posts = $post_stmt->fetchAll();

$page_title = 'Chuyên mục: ' . $category['name'];
$page_desc = $category['description'] ?: 'Tất cả bài viết thuộc chuyên mục ' . $category['name'];

require_once CMS_ROOT . '/includes/header.php';
?>

<main class="container py-10">
    <!-- Category Header -->
    <div class="mb-10 pb-6 border-b border-slate-200 dark:border-slate-800">
        <span class="text-xs uppercase tracking-widest text-accent font-semibold block mb-1">Chuyên mục bài viết</span>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2"><?= e($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-2xl leading-relaxed"><?= e($category['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8">
            <?php if (empty($posts)): ?>
            <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-500 text-sm">
                Chưa có bài viết nào trong chuyên mục này.
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                <?php foreach ($posts as $post_item): ?>
                <article class="post-card group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <a href="<?= BASE_URL ?>/post/<?= e($post_item['slug']) ?>" class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative block">
                        <?php if (!empty($post_item['featured_image'])): ?>
                        <img src="<?= e($post_item['featured_image']) ?>" alt="<?= e($post_item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" referrerpolicy="no-referrer">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">TonyHoaiVu Article</div>
                        <?php endif; ?>
                    </a>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-2.5">
                                <span><?= format_date($post_item['created_at']) ?></span>
                                <span aria-hidden="true">·</span>
                                <span><?= reading_time($post_item['content']) ?> phút đọc</span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug mb-2 group-hover:text-accent transition-colors line-clamp-2">
                                <a href="<?= BASE_URL ?>/post/<?= e($post_item['slug']) ?>">
                                    <?= e($post_item['title']) ?>
                                </a>
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3 mb-4">
                                <?= e($post_item['excerpt'] ?: strip_tags(substr($post_item['content'], 0, 160))) ?>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-500"><?= e($post_item['author_name']) ?></span>
                            <span class="text-accent font-semibold group-hover:translate-x-0.5 transition-transform">Đọc tiếp &rarr;</span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-4">
            <?php require_once CMS_ROOT . '/includes/sidebar.php'; ?>
        </div>
    </div>
</main>

<?php require_once CMS_ROOT . '/includes/footer.php'; ?>
