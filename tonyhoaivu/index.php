<?php
/**
 * TonyHoaiVu CMS - Blog Home Page
 */

define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/functions.php';

$page_title = 'Trang chủ';
$page_desc = get_setting('site_description');
$posts_per_page = (int)get_setting('posts_per_page', '6');

$current_page = max(1, (int)($_GET['p'] ?? 1));
$offset = ($current_page - 1) * $posts_per_page;

$db = get_db();

// Total count for pagination
$count_stmt = $db->query("SELECT COUNT(*) FROM posts WHERE status = 'publish'");
$total_posts = (int)$count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts with author and category details
$stmt = $db->prepare("
    SELECT p.*, u.display_name AS author_name, u.avatar AS author_avatar, c.name AS category_name, c.slug AS category_slug
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'publish'
    ORDER BY p.is_featured DESC, p.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Include Header
require_once CMS_ROOT . '/includes/header.php';
?>

<main class="container py-10">
    <!-- Hero / Lead Banner -->
    <div class="mb-12 py-10 px-8 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl text-white shadow-xl relative overflow-hidden border border-slate-700/50">
        <div class="relative z-10 max-w-2xl">
            <span class="text-xs uppercase tracking-widest text-accent-light font-semibold mb-2 block">Chào mừng bạn đến với</span>
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-4 text-balance">
                TonyHoaiVu Blog
            </h1>
            <p class="text-slate-300 text-sm md:text-base leading-relaxed mb-6 font-normal">
                Không gian chia sẻ kinh nghiệm kỹ thuật thực chiến, kiến trúc phần mềm, bảo mật hệ thống và văn hóa kỹ sư công nghệ.
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= BASE_URL ?>/category/lap-trinh" class="px-4 py-2 text-xs font-semibold bg-white text-slate-900 rounded-xl hover:bg-slate-100 transition-colors shadow-sm">
                    Khám phá bài viết &rarr;
                </a>
                <a href="<?= BASE_URL ?>/page/gioi-thieu" class="px-4 py-2 text-xs font-semibold bg-slate-800/80 text-white rounded-xl hover:bg-slate-700 transition-colors border border-slate-700">
                    Về tác giả
                </a>
            </div>
        </div>
        <!-- Decorative subtle pattern -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-accent/20 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Main Content Area with 2-Column Split: Posts (Left) & Sidebar (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Post Grid Section -->
        <div class="lg:col-span-8">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-200 dark:border-slate-800">
                <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                    <span>Bài viết mới nhất</span>
                </h2>
                <span class="text-xs text-slate-500 font-mono"><?= $total_posts ?> bài viết</span>
            </div>

            <?php if (empty($posts)): ?>
            <div class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                <p class="text-slate-500 text-sm">Chưa có bài viết nào được xuất bản. Hãy đăng nhập quản trị để đăng bài mới!</p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                <?php foreach ($posts as $post_item): ?>
                <article class="post-card group bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <!-- Featured Image -->
                    <a href="<?= BASE_URL ?>/post/<?= e($post_item['slug']) ?>" class="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative block">
                        <?php if (!empty($post_item['featured_image'])): ?>
                        <img src="<?= e($post_item['featured_image']) ?>" alt="<?= e($post_item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" referrerpolicy="no-referrer">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <span class="text-xs uppercase tracking-wider font-semibold">TonyHoaiVu Article</span>
                        </div>
                        <?php endif; ?>

                        <?php if ($post_item['is_featured']): ?>
                        <span class="absolute top-3 left-3 bg-accent text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg shadow-sm">
                            Nổi bật
                        </span>
                        <?php endif; ?>
                    </a>

                    <!-- Post Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Category and Date (Quiet unboxed text) -->
                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-2.5">
                                <?php if (!empty($post_item['category_name'])): ?>
                                <a href="<?= BASE_URL ?>/category/<?= e($post_item['category_slug']) ?>" class="font-medium hover:text-accent transition-colors">
                                    <?= e($post_item['category_name']) ?>
                                </a>
                                <span aria-hidden="true">·</span>
                                <?php endif; ?>
                                <span><?= format_date($post_item['created_at']) ?></span>
                                <span aria-hidden="true">·</span>
                                <span><?= reading_time($post_item['content']) ?> phút đọc</span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug mb-2 group-hover:text-accent transition-colors line-clamp-2">
                                <a href="<?= BASE_URL ?>/post/<?= e($post_item['slug']) ?>">
                                    <?= e($post_item['title']) ?>
                                </a>
                            </h3>

                            <!-- Excerpt -->
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3 mb-4">
                                <?= e($post_item['excerpt'] ?: strip_tags(substr($post_item['content'], 0, 160))) ?>
                            </p>
                        </div>

                        <!-- Footer Author & Link -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <img src="<?= e($post_item['author_avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80') ?>" alt="<?= e($post_item['author_name']) ?>" class="w-6 h-6 rounded-full object-cover" referrerpolicy="no-referrer">
                                <span class="font-medium text-slate-700 dark:text-slate-300"><?= e($post_item['author_name']) ?></span>
                            </div>
                            <span class="text-accent font-semibold group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                                Đọc bài &rarr;
                            </span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Bar -->
            <?php if ($total_pages > 1): ?>
            <div class="mt-12 flex justify-center items-center gap-2">
                <?php if ($current_page > 1): ?>
                <a href="?p=<?= $current_page - 1 ?>" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50">
                    &larr; Trang trước
                </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?p=<?= $i ?>" class="w-9 h-9 flex items-center justify-center text-xs font-semibold rounded-xl <?= $i === $current_page ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                <a href="?p=<?= $current_page + 1 ?>" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50">
                    Trang tiếp &rarr;
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar Section -->
        <div class="lg:col-span-4">
            <?php require_once CMS_ROOT . '/includes/sidebar.php'; ?>
        </div>
    </div>
</main>

<?php require_once CMS_ROOT . '/includes/footer.php'; ?>
