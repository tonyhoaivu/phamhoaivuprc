<?php
/**
 * TonyHoaiVu CMS - Search Results Page
 */

define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/functions.php';

$query = trim($_GET['q'] ?? '');
$db = get_db();
$posts = [];

if (!empty($query)) {
    $search_term = '%' . $query . '%';
    $stmt = $db->prepare("
        SELECT p.*, u.display_name AS author_name, c.name AS category_name, c.slug AS category_slug
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'publish' 
          AND (p.title LIKE :q1 OR p.content LIKE :q2 OR p.tags LIKE :q3 OR p.excerpt LIKE :q4)
        ORDER BY p.created_at DESC
        LIMIT 20
    ");
    $stmt->execute([
        'q1' => $search_term,
        'q2' => $search_term,
        'q3' => $search_term,
        'q4' => $search_term
    ]);
    $posts = $stmt->fetchAll();
}

$page_title = !empty($query) ? 'Tìm kiếm: ' . $query : 'Tìm kiếm bài viết';
require_once CMS_ROOT . '/includes/header.php';
?>

<main class="container py-10">
    <div class="mb-10 pb-6 border-b border-slate-200 dark:border-slate-800">
        <span class="text-xs uppercase tracking-widest text-accent font-semibold block mb-1">Kết quả tìm kiếm</span>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Từ khóa: "<?= e($query) ?>"
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Tìm thấy <strong class="text-slate-800 dark:text-slate-200"><?= count($posts) ?></strong> bài viết phù hợp.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8">
            <?php if (empty($posts)): ?>
            <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-500 text-sm">
                Không tìm thấy bài viết nào khớp với từ khóa "<?= e($query) ?>". Vui lòng thử lại với từ khóa khác!
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                <?php foreach ($posts as $post_item): ?>
                <article class="post-card group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col hover:shadow-lg transition-all duration-300">
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
                                <?php if ($post_item['category_name']): ?>
                                <span class="font-medium"><?= e($post_item['category_name']) ?></span>
                                <span aria-hidden="true">·</span>
                                <?php endif; ?>
                                <span><?= format_date($post_item['created_at']) ?></span>
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
                            <span class="text-accent font-semibold">Xem chi tiết &rarr;</span>
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
