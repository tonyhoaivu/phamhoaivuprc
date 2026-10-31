<?php
/**
 * TonyHoaiVu CMS - Comments Moderation (/admin/comments.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Quản lý bình luận';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Handle Status Updates
if (isset($_GET['action']) && !empty($_GET['id'])) {
    validate_csrf();
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $db->prepare("UPDATE comments SET status = 'approved' WHERE id = :id")->execute(['id' => $id]);
        set_flash('success', 'Đã duyệt bình luận.');
    } elseif ($action === 'unapprove') {
        $db->prepare("UPDATE comments SET status = 'pending' WHERE id = :id")->execute(['id' => $id]);
        set_flash('success', 'Đã chuyển bình luận sang trạng thái chờ duyệt.');
    } elseif ($action === 'spam') {
        $db->prepare("UPDATE comments SET status = 'spam' WHERE id = :id")->execute(['id' => $id]);
        set_flash('success', 'Đã đánh dấu bình luận là Spam.');
    } elseif ($action === 'trash') {
        $db->prepare("DELETE FROM comments WHERE id = :id")->execute(['id' => $id]);
        set_flash('success', 'Đã xóa bình luận.');
    }
    header('Location: ' . ADMIN_URL . '/comments.php');
    exit;
}

$status_filter = trim($_GET['status'] ?? '');
$where = "1=1";
$params = [];

if ($status_filter && in_array($status_filter, ['approved', 'pending', 'spam', 'trash'])) {
    $where = "c.status = :status";
    $params['status'] = $status_filter;
}

$stmt = $db->prepare("
    SELECT c.*, p.title as post_title, p.slug as post_slug
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    WHERE $where
    ORDER BY c.created_at DESC
");
$stmt->execute($params);
$comments = $stmt->fetchAll();

$c_all = (int)$db->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$c_pending = (int)$db->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$c_approved = (int)$db->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'")->fetchColumn();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Bình luận</h1>
    <hr class="wp-header-end">

    <ul class="subsubsub">
        <li class="all"><a href="<?= ADMIN_URL ?>/comments.php" class="<?= empty($status_filter) ? 'current' : '' ?>">Tất cả <span class="count">(<?= $c_all ?>)</span></a> |</li>
        <li class="pending"><a href="<?= ADMIN_URL ?>/comments.php?status=pending" class="<?= $status_filter === 'pending' ? 'current' : '' ?>">Chờ duyệt <span class="count">(<?= $c_pending ?>)</span></a> |</li>
        <li class="approved"><a href="<?= ADMIN_URL ?>/comments.php?status=approved" class="<?= $status_filter === 'approved' ? 'current' : '' ?>">Đã duyệt <span class="count">(<?= $c_approved ?>)</span></a></li>
    </ul>

    <table class="wp-list-table widefat fixed striped comments">
        <thead>
            <tr>
                <th class="manage-column column-author">Tác giả bình luận</th>
                <th class="manage-column column-comment">Nội dung</th>
                <th class="manage-column column-response">Trong bài viết</th>
                <th class="manage-column column-date">Gửi lúc</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($comments)): ?>
            <tr><td colspan="4">Không có bình luận nào.</td></tr>
            <?php else: ?>
            <?php foreach ($comments as $c): ?>
            <tr class="<?= $c['status'] === 'pending' ? 'unapproved-row' : '' ?>">
                <td class="author column-author">
                    <strong><?= e($c['author_name']) ?></strong><br>
                    <a href="mailto:<?= e($c['author_email']) ?>" class="text-xs text-muted"><?= e($c['author_email']) ?></a>
                    <?php if ($c['author_website']): ?>
                    <br><a href="<?= e($c['author_website']) ?>" target="_blank" class="text-xs text-muted"><?= e($c['author_website']) ?></a>
                    <?php endif; ?>
                </td>
                <td class="comment column-comment has-row-actions">
                    <p class="mb-2"><?= nl2br(e($c['content'])) ?></p>
                    <div class="row-actions">
                        <?php if ($c['status'] === 'pending'): ?>
                        <span class="approve"><a href="<?= ADMIN_URL ?>/comments.php?action=approve&id=<?= $c['id'] ?>&csrf_token=<?= csrf_token() ?>" class="text-emerald-600 font-semibold">Chấp thuận</a> | </span>
                        <?php else: ?>
                        <span class="unapprove"><a href="<?= ADMIN_URL ?>/comments.php?action=unapprove&id=<?= $c['id'] ?>&csrf_token=<?= csrf_token() ?>">Bỏ duyệt</a> | </span>
                        <?php endif; ?>
                        <span class="spam"><a href="<?= ADMIN_URL ?>/comments.php?action=spam&id=<?= $c['id'] ?>&csrf_token=<?= csrf_token() ?>" class="text-amber-600">Đánh dấu Spam</a> | </span>
                        <span class="trash"><a href="<?= ADMIN_URL ?>/comments.php?action=trash&id=<?= $c['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Bạn có chắc muốn xóa bình luận này?');" class="text-danger">Xóa</a></span>
                    </div>
                </td>
                <td class="response column-response">
                    <a href="<?= BASE_URL ?>/post/<?= e($c['post_slug']) ?>" target="_blank" class="font-semibold"><?= e($c['post_title']) ?></a>
                </td>
                <td class="date column-date">
                    <?= format_date($c['created_at']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
