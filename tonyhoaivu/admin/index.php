<?php
/**
 * TonyHoaiVu CMS - Admin Dashboard (Bảng tin)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Bảng tin';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Handle Quick Draft submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_quick_draft'])) {
    validate_csrf();
    $draft_title = trim($_POST['title'] ?? '');
    $draft_content = trim($_POST['content'] ?? '');

    if (!empty($draft_title)) {
        $slug = slugify($draft_title) . '-' . time();
        $ins = $db->prepare("
            INSERT INTO posts (author_id, title, slug, content, excerpt, status, is_featured)
            VALUES (:uid, :title, :slug, :content, :excerpt, 'draft', 0)
        ");
        $ins->execute([
            'uid'     => $current_user['id'],
            'title'   => $draft_title,
            'slug'    => $slug,
            'content' => nl2br(htmlspecialchars($draft_content)),
            'excerpt' => substr(strip_tags($draft_content), 0, 150)
        ]);
        set_flash('success', 'Bản thảo nhanh "' . htmlspecialchars($draft_title) . '" đã được lưu thành công!');
        header('Location: ' . ADMIN_URL . '/index.php');
        exit;
    }
}

// Fetch metrics
$total_posts = (int)$db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$published_posts = (int)$db->query("SELECT COUNT(*) FROM posts WHERE status = 'publish'")->fetchColumn();
$draft_posts = (int)$db->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();
$total_cats = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_pages = (int)$db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$total_views = (int)$db->query("SELECT SUM(views_count) FROM posts")->fetchColumn();
$total_comments = (int)$db->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$pending_comments = (int)$db->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();

// Fetch recent posts
$recent_posts = $db->query("
    SELECT p.id, p.title, p.slug, p.status, p.created_at, p.views_count, c.name as category_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC LIMIT 5
")->fetchAll();

// Fetch recent comments
$recent_comments = $db->query("
    SELECT c.*, p.title as post_title, p.slug as post_slug
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC LIMIT 4
")->fetchAll();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Bảng tin</h1>
    <a href="<?= ADMIN_URL ?>/post-new.php" class="page-title-action">Viết bài mới</a>
    <hr class="wp-header-end">

    <!-- Welcome Panel -->
    <div class="welcome-panel">
        <div class="welcome-panel-content">
            <h2>Chào mừng bạn đến với hệ thống CMS của TonyHoaiVu!</h2>
            <p class="about-description">Hệ thống quản lý nội dung độc lập bằng PHP 8 &amp; MySQL với đầy đủ chức năng như WordPress.</p>
            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <h3>Bắt đầu ngay</h3>
                    <a href="<?= ADMIN_URL ?>/post-new.php" class="button button-primary button-hero">Viết bài mới cho Blog</a>
                    <p class="description">hoặc <a href="<?= BASE_URL ?>/" target="_blank">Xem website ngoài trang chủ</a></p>
                </div>
                <div class="welcome-panel-column">
                    <h3>Các bước tiếp theo</h3>
                    <ul>
                        <li><a href="<?= ADMIN_URL ?>/categories.php" class="welcome-icon welcome-write-blog">Quản lý chuyên mục</a></li>
                        <li><a href="<?= ADMIN_URL ?>/page-new.php" class="welcome-icon welcome-add-page">Tạo trang giới thiệu/liên hệ</a></li>
                        <li><a href="<?= ADMIN_URL ?>/media.php" class="welcome-icon welcome-view-site">Tải ảnh lên thư viện Media</a></li>
                    </ul>
                </div>
                <div class="welcome-panel-column">
                    <h3>Cấu hình thêm</h3>
                    <ul>
                        <li><a href="<?= ADMIN_URL ?>/menus.php" class="welcome-icon welcome-widgets-menus">Tùy biến Menu điều hướng</a></li>
                        <li><a href="<?= ADMIN_URL ?>/settings.php" class="welcome-icon welcome-comments">Cài đặt SEO &amp; Mạng xã hội</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="stats-overview-grid">
        <div class="stat-card">
            <div class="stat-icon bg-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($total_posts) ?></div>
                <div class="stat-label">Tổng bài viết (<?= $published_posts ?> xuất bản, <?= $draft_posts ?> nháp)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-purple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($total_views) ?></div>
                <div class="stat-label">Tổng lượt xem bài viết</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($total_cats) ?></div>
                <div class="stat-label">Chuyên mục &amp; <?= $total_pages ?> Trang tĩnh</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= number_format($total_comments) ?></div>
                <div class="stat-label">Bình luận (<?= $pending_comments ?> chờ duyệt)</div>
            </div>
        </div>
    </div>

    <!-- 2 Column Dashboard Widgets -->
    <div id="dashboard-widgets-wrap">
        <div id="dashboard-widgets" class="metabox-holder">
            <!-- Left Column -->
            <div class="postbox-container">
                <!-- At a Glance Widget -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>Tổng quan nhanh</h2>
                    </div>
                    <div class="inside">
                        <ul class="glance-list">
                            <li><a href="<?= ADMIN_URL ?>/posts.php"><strong><?= $published_posts ?></strong> Bài viết</a></li>
                            <li><a href="<?= ADMIN_URL ?>/pages.php"><strong><?= $total_pages ?></strong> Trang tĩnh</a></li>
                            <li><a href="<?= ADMIN_URL ?>/categories.php"><strong><?= $total_cats ?></strong> Chuyên mục</a></li>
                            <li><a href="<?= ADMIN_URL ?>/comments.php"><strong><?= $total_comments ?></strong> Bình luận</a></li>
                        </ul>
                        <p class="version-notice">TonyHoaiVu CMS chạy trên PHP <?= phpversion() ?> với MySQL <?= $db->getAttribute(PDO::ATTR_SERVER_VERSION) ?></p>
                    </div>
                </div>

                <!-- Recent Posts Activity -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>Bài viết gần đây</h2>
                    </div>
                    <div class="inside">
                        <table class="widefat striped">
                            <thead>
                                <tr>
                                    <th>Tiêu đề</th>
                                    <th>Chuyên mục</th>
                                    <th>Trạng thái</th>
                                    <th>Lượt xem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_posts as $rp): ?>
                                <tr>
                                    <td>
                                        <strong><a href="<?= ADMIN_URL ?>/post-edit.php?id=<?= $rp['id'] ?>"><?= e($rp['title']) ?></a></strong>
                                    </td>
                                    <td><?= e($rp['category_name'] ?: '—') ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $rp['status'] ?>">
                                            <?= $rp['status'] === 'publish' ? 'Đã xuất bản' : ($rp['status'] === 'draft' ? 'Bản nháp' : 'Riêng tư') ?>
                                        </span>
                                    </td>
                                    <td class="font-mono"><?= number_format($rp['views_count']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="postbox-container">
                <!-- Quick Draft Widget -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>Soạn nháp nhanh</h2>
                    </div>
                    <div class="inside">
                        <form action="" method="POST">
                            <?= csrf_field() ?>
                            <div class="input-text-wrap mb-3">
                                <label for="title" class="screen-reader-text">Tiêu đề</label>
                                <input type="text" name="title" id="title" placeholder="Tiêu đề ý tưởng..." required class="regular-text w-100">
                            </div>
                            <div class="textarea-wrap mb-3">
                                <label for="content" class="screen-reader-text">Nội dung</label>
                                <textarea name="content" id="content" placeholder="Ghi chú nhanh suy nghĩ của bạn..." rows="4" class="w-100"></textarea>
                            </div>
                            <p class="submit">
                                <button type="submit" name="save_quick_draft" class="button button-primary">Lưu bản nháp</button>
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Recent Comments -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2>Bình luận mới nhất</h2>
                    </div>
                    <div class="inside">
                        <?php if (empty($recent_comments)): ?>
                        <p class="text-muted">Chưa có bình luận nào.</p>
                        <?php else: ?>
                        <div class="activity-comments">
                            <?php foreach ($recent_comments as $rc): ?>
                            <div class="comment-item pb-3 mb-3 border-b">
                                <div class="comment-author">
                                    <strong><?= e($rc['author_name']) ?></strong> trong bài <em><a href="<?= BASE_URL ?>/post/<?= e($rc['post_slug']) ?>" target="_blank"><?= e($rc['post_title']) ?></a></em>:
                                </div>
                                <p class="comment-excerpt text-xs text-muted"><?= e(substr($rc['content'], 0, 100)) ?>...</p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
