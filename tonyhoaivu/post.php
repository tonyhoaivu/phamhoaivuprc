<?php
/**
 * TonyHoaiVu CMS - Single Post Detail Page
 */

define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/csrf.php';
require_once CMS_ROOT . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = get_db();

// Fetch post
$stmt = $db->prepare("
    SELECT p.*, u.display_name AS author_name, u.avatar AS author_avatar, u.bio AS author_bio,
           c.name AS category_name, c.slug AS category_slug
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.slug = :slug AND p.status = 'publish'
    LIMIT 1
");
$stmt->execute(['slug' => $slug]);
$post = $stmt->fetch();

if (!$post) {
    http_response_code(404);
    $page_title = 'Không tìm thấy bài viết';
    require_once CMS_ROOT . '/includes/header.php';
    echo '<div class="container py-24 text-center"><h1 class="text-3xl font-bold mb-4">404 - Bài viết không tồn tại</h1><p class="text-slate-500 mb-6">Bài viết bạn tìm kiếm có thể đã bị xóa hoặc đổi địa chỉ.</p><a href="' . BASE_URL . '/" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-semibold">Về trang chủ</a></div>';
    require_once CMS_ROOT . '/includes/footer.php';
    exit;
}

// Record view
record_post_view($post['id']);

// Handle Comment Submission
$comment_msg = '';
$comment_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    validate_csrf();
    $name = trim($_POST['author_name'] ?? '');
    $email = trim($_POST['author_email'] ?? '');
    $website = trim($_POST['author_website'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($name) || empty($email) || empty($content)) {
        $comment_error = 'Vui lòng điền đầy đủ họ tên, email và nội dung bình luận.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $comment_error = 'Địa chỉ email không hợp lệ.';
    } else {
        $c_stmt = $db->prepare("
            INSERT INTO comments (post_id, author_name, author_email, author_website, content, status)
            VALUES (:post_id, :name, :email, :website, :content, 'approved')
        ");
        $c_stmt->execute([
            'post_id' => $post['id'],
            'name'    => $name,
            'email'   => $email,
            'website' => $website,
            'content' => $content
        ]);
        $comment_msg = 'Bình luận của bạn đã được đăng thành công!';
    }
}

// Fetch approved comments
$comments_stmt = $db->prepare("SELECT * FROM comments WHERE post_id = :p AND status = 'approved' ORDER BY created_at DESC");
$comments_stmt->execute(['p' => $post['id']]);
$comments = $comments_stmt->fetchAll();

// Related posts
$rel_stmt = $db->prepare("SELECT id, title, slug, featured_image, created_at FROM posts WHERE category_id = :c AND id != :p AND status = 'publish' ORDER BY id DESC LIMIT 3");
$rel_stmt->execute(['c' => $post['category_id'], 'p' => $post['id']]);
$related_posts = $rel_stmt->fetchAll();

// SEO Meta
$page_title = $post['seo_title'] ?: $post['title'];
$page_desc = $post['seo_description'] ?: $post['excerpt'];
$og_image = $post['featured_image'];

require_once CMS_ROOT . '/includes/header.php';
?>

<main class="container py-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Post Main Content Area -->
        <article class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-sm">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
                <a href="<?= BASE_URL ?>/" class="hover:text-accent">Trang chủ</a>
                <span>/</span>
                <?php if ($post['category_name']): ?>
                <a href="<?= BASE_URL ?>/category/<?= e($post['category_slug']) ?>" class="hover:text-accent"><?= e($post['category_name']) ?></a>
                <span>/</span>
                <?php endif; ?>
                <span class="text-slate-400 truncate max-w-xs"><?= e($post['title']) ?></span>
            </nav>

            <!-- Headline -->
            <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-6">
                <?= e($post['title']) ?>
            </h1>

            <!-- Meta bar -->
            <div class="flex flex-wrap items-center justify-between pb-6 mb-8 border-b border-slate-100 dark:border-slate-800 text-xs text-slate-500 gap-4">
                <div class="flex items-center gap-3">
                    <img src="<?= e($post['author_avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80') ?>" alt="<?= e($post['author_name']) ?>" class="w-9 h-9 rounded-full object-cover" referrerpolicy="no-referrer">
                    <div>
                        <div class="font-bold text-slate-800 dark:text-slate-200"><?= e($post['author_name']) ?></div>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                            <span>Đăng ngày <?= format_date($post['created_at']) ?></span>
                            <span>·</span>
                            <span><?= reading_time($post['content']) ?> phút đọc</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-slate-400 font-mono text-xs">
                    <span><?= number_format($post['views_count']) ?> lượt xem</span>
                </div>
            </div>

            <!-- Featured Image -->
            <?php if (!empty($post['featured_image'])): ?>
            <div class="mb-8 rounded-2xl overflow-hidden aspect-video bg-slate-100 dark:bg-slate-800">
                <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="w-full h-full object-cover" referrerpolicy="no-referrer">
            </div>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="article-content prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-200 text-base leading-relaxed space-y-4">
                <?= $post['content'] ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($post['tags'])): ?>
            <div class="mt-10 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-slate-400">Từ khóa:</span>
                <?php foreach (explode(',', $post['tags']) as $tag): $tag = trim($tag); if ($tag): ?>
                <a href="<?= BASE_URL ?>/search.php?q=<?= urlencode($tag) ?>" class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition-colors">
                    #<?= e($tag) ?>
                </a>
                <?php endif; endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Author Box -->
            <div class="mt-12 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <img src="<?= e($post['author_avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150') ?>" alt="<?= e($post['author_name']) ?>" class="w-16 h-16 rounded-full object-cover shrink-0" referrerpolicy="no-referrer">
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1"><?= e($post['author_name']) ?></h4>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-3">
                        <?= e($post['author_bio'] ?: 'Kỹ sư phần mềm Full-stack đam mê nghiên cứu kiến trúc hệ thống và lập trình ứng dụng.') ?>
                    </p>
                    <a href="<?= BASE_URL ?>/page/gioi-thieu" class="text-xs font-semibold text-accent hover:underline">
                        Đọc thêm về tác giả &rarr;
                    </a>
                </div>
            </div>

            <!-- Related Posts -->
            <?php if (!empty($related_posts)): ?>
            <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white text-base mb-4">Bài viết cùng chuyên mục</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <?php foreach ($related_posts as $rel): ?>
                    <a href="<?= BASE_URL ?>/post/<?= e($rel['slug']) ?>" class="group block p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                        <?php if ($rel['featured_image']): ?>
                        <img src="<?= e($rel['featured_image']) ?>" alt="<?= e($rel['title']) ?>" class="w-full h-24 object-cover rounded-lg mb-2" referrerpolicy="no-referrer">
                        <?php endif; ?>
                        <h4 class="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-accent line-clamp-2 leading-snug">
                            <?= e($rel['title']) ?>
                        </h4>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Comments Section -->
            <section class="mt-14 pt-8 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span>Bình luận</span>
                    <span class="text-xs text-slate-400 font-mono">(<?= count($comments) ?>)</span>
                </h3>

                <?php if ($comment_msg): ?>
                <div class="p-3 mb-6 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs">
                    <?= e($comment_msg) ?>
                </div>
                <?php endif; ?>

                <?php if ($comment_error): ?>
                <div class="p-3 mb-6 bg-red-50 dark:bg-red-950/50 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800 rounded-xl text-xs">
                    <?= e($comment_error) ?>
                </div>
                <?php endif; ?>

                <!-- Comment Form -->
                <form action="" method="POST" class="mb-10 p-6 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-800 space-y-4">
                    <?= csrf_field() ?>
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300">Gửi bình luận của bạn</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Họ và tên *</label>
                            <input type="text" name="author_name" required class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Nguyễn Văn A">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Địa chỉ Email * (không công khai)</label>
                            <input type="email" name="author_email" required class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="email@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Website / Blog (tùy chọn)</label>
                        <input type="url" name="author_website" class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="https://mywebsite.com">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Nội dung bình luận *</label>
                        <textarea name="content" rows="4" required class="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Chia sẻ suy nghĩ hoặc câu hỏi của bạn..."></textarea>
                    </div>

                    <button type="submit" name="submit_comment" class="px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:opacity-90 transition-opacity">
                        Đăng bình luận
                    </button>
                </form>

                <!-- Comments List -->
                <div class="space-y-4">
                    <?php if (empty($comments)): ?>
                    <p class="text-xs text-slate-500 italic">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ cảm nghĩ!</p>
                    <?php else: ?>
                    <?php foreach ($comments as $cm): ?>
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-900 dark:text-white">
                                <?php if ($cm['author_website']): ?>
                                <a href="<?= e($cm['author_website']) ?>" target="_blank" rel="nofollow noopener" class="hover:text-accent"><?= e($cm['author_name']) ?></a>
                                <?php else: ?>
                                <?= e($cm['author_name']) ?>
                                <?php endif; ?>
                            </span>
                            <span class="text-[11px] text-slate-400"><?= format_date($cm['created_at']) ?></span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 leading-relaxed">
                            <?= nl2br(e($cm['content'])) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </article>

        <!-- Sidebar -->
        <div class="lg:col-span-4">
            <?php require_once CMS_ROOT . '/includes/sidebar.php'; ?>
        </div>
    </div>
</main>

<?php require_once CMS_ROOT . '/includes/footer.php'; ?>
