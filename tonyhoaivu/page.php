<?php
/**
 * TonyHoaiVu CMS - Static Page Template
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

// Fetch page
$stmt = $db->prepare("SELECT * FROM pages WHERE slug = :slug AND status = 'publish' LIMIT 1");
$stmt->execute(['slug' => $slug]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    $page_title = 'Không tìm thấy trang';
    require_once CMS_ROOT . '/includes/header.php';
    echo '<div class="container py-24 text-center"><h1 class="text-3xl font-bold mb-4">404 - Trang không tồn tại</h1><a href="' . BASE_URL . '/" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-semibold">Về trang chủ</a></div>';
    require_once CMS_ROOT . '/includes/footer.php';
    exit;
}

$page_title = $page['title'];

require_once CMS_ROOT . '/includes/header.php';
?>

<main class="container py-12">
    <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 sm:p-14 shadow-sm">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-8 pb-4 border-b border-slate-100 dark:border-slate-800">
            <?= e($page['title']) ?>
        </h1>

        <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-200 text-base leading-relaxed space-y-4">
            <?= $page['content'] ?>
        </div>
    </div>
</main>

<?php require_once CMS_ROOT . '/includes/footer.php'; ?>
