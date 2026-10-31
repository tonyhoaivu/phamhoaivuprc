<?php
/**
 * TonyHoaiVu CMS - Manage Static Pages (/admin/pages.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Trang tĩnh';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Delete page
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    validate_csrf();
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM pages WHERE id = :id");
    $stmt->execute(['id' => $id]);
    set_flash('success', 'Đã xóa trang thành công.');
    header('Location: ' . ADMIN_URL . '/pages.php');
    exit;
}

$pages = $db->query("SELECT p.*, u.display_name AS author_name FROM pages p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.created_at DESC")->fetchAll();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Trang tĩnh</h1>
    <a href="<?= ADMIN_URL ?>/page-new.php" class="page-title-action">Thêm trang mới</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <tr>
                <th class="manage-column column-title column-primary">Tiêu đề</th>
                <th class="manage-column column-author">Tác giả</th>
                <th class="manage-column column-slug">Đường dẫn</th>
                <th class="manage-column column-date">Thời gian</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pages)): ?>
            <tr><td colspan="4">Chưa có trang tĩnh nào.</td></tr>
            <?php else: ?>
            <?php foreach ($pages as $p): ?>
            <tr>
                <td class="title column-title has-row-actions column-primary">
                    <strong><a class="row-title" href="<?= ADMIN_URL ?>/page-new.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></strong>
                    <div class="row-actions">
                        <span class="edit"><a href="<?= ADMIN_URL ?>/page-new.php?id=<?= $p['id'] ?>">Chỉnh sửa</a> | </span>
                        <span class="trash"><a href="<?= ADMIN_URL ?>/pages.php?action=delete&id=<?= $p['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Bạn có chắc muốn xóa trang này?');" class="submitdelete text-danger">Xóa</a> | </span>
                        <span class="view"><a href="<?= BASE_URL ?>/page/<?= e($p['slug']) ?>" target="_blank">Xem trang</a></span>
                    </div>
                </td>
                <td class="author column-author"><?= e($p['author_name']) ?></td>
                <td class="slug column-slug font-mono text-xs">/page/<?= e($p['slug']) ?></td>
                <td class="date column-date"><?= format_date($p['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
