<?php
/**
 * TonyHoaiVu CMS - Manage Posts (/admin/posts.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Quản lý bài viết';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Handle Single Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    validate_csrf();
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    set_flash('success', 'Đã xóa bài viết thành công.');
    header('Location: ' . ADMIN_URL . '/posts.php');
    exit;
}

// Handle Bulk Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    validate_csrf();
    $action = $_POST['action'] ?? '';
    $post_ids = $_POST['post_ids'] ?? [];

    if (!empty($post_ids) && is_array($post_ids)) {
        $clean_ids = array_map('intval', $post_ids);
        $placeholders = implode(',', array_fill(0, count($clean_ids), '?'));

        if ($action === 'delete') {
            $stmt = $db->prepare("DELETE FROM posts WHERE id IN ($placeholders)");
            $stmt->execute($clean_ids);
            set_flash('success', 'Đã xóa các bài viết được chọn.');
        } elseif ($action === 'publish') {
            $stmt = $db->prepare("UPDATE posts SET status = 'publish' WHERE id IN ($placeholders)");
            $stmt->execute($clean_ids);
            set_flash('success', 'Đã xuất bản các bài viết được chọn.');
        } elseif ($action === 'draft') {
            $stmt = $db->prepare("UPDATE posts SET status = 'draft' WHERE id IN ($placeholders)");
            $stmt->execute($clean_ids);
            set_flash('success', 'Đã chuyển các bài viết được chọn thành bản nháp.');
        }
        header('Location: ' . ADMIN_URL . '/posts.php');
        exit;
    }
}

// Filters & Query
$status_filter = trim($_GET['status'] ?? '');
$cat_filter = (int)($_GET['cat'] ?? 0);
$search = trim($_GET['s'] ?? '');

$where = ["1=1"];
$params = [];

if ($status_filter && in_array($status_filter, ['publish', 'draft', 'private'])) {
    $where[] = "p.status = :status";
    $params['status'] = $status_filter;
}

if ($cat_filter > 0) {
    $where[] = "p.category_id = :cat_id";
    $params['cat_id'] = $cat_filter;
}

if ($search) {
    $where[] = "(p.title LIKE :s1 OR p.content LIKE :s2 OR p.tags LIKE :s3)";
    $params['s1'] = "%$search%";
    $params['s2'] = "%$search%";
    $params['s3'] = "%$search%";
}

$where_clause = implode(" AND ", $where);

// Count
$count_stmt = $db->prepare("SELECT COUNT(*) FROM posts p WHERE $where_clause");
$count_stmt->execute($params);
$total_items = (int)$count_stmt->fetchColumn();

// Fetch posts
$query = "
    SELECT p.*, u.display_name AS author_name, c.name AS category_name
    FROM posts p
    LEFT JOIN users u ON p.author_id = u.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE $where_clause
    ORDER BY p.created_at DESC
";
$stmt = $db->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Counts for status tabs
$c_all = (int)$db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$c_publish = (int)$db->query("SELECT COUNT(*) FROM posts WHERE status = 'publish'")->fetchColumn();
$c_draft = (int)$db->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();

$all_cats = $db->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Bài viết</h1>
    <a href="<?= ADMIN_URL ?>/post-new.php" class="page-title-action">Viết bài mới</a>
    <hr class="wp-header-end">

    <!-- Status Tabs -->
    <ul class="subsubsub">
        <li class="all"><a href="<?= ADMIN_URL ?>/posts.php" class="<?= empty($status_filter) ? 'current' : '' ?>">Tất cả <span class="count">(<?= $c_all ?>)</span></a> |</li>
        <li class="publish"><a href="<?= ADMIN_URL ?>/posts.php?status=publish" class="<?= $status_filter === 'publish' ? 'current' : '' ?>">Đã xuất bản <span class="count">(<?= $c_publish ?>)</span></a> |</li>
        <li class="draft"><a href="<?= ADMIN_URL ?>/posts.php?status=draft" class="<?= $status_filter === 'draft' ? 'current' : '' ?>">Bản nháp <span class="count">(<?= $c_draft ?>)</span></a></li>
    </ul>

    <!-- Filter Bar -->
    <form method="GET" action="<?= ADMIN_URL ?>/posts.php" class="posts-filter-form">
        <div class="tablenav top">
            <div class="alignleft actions">
                <select name="cat">
                    <option value="0">Tất cả chuyên mục</option>
                    <?php foreach ($all_cats as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat_filter === (int)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class="button" value="Lọc bài">
            </div>

            <div class="alignright search-box">
                <input type="search" name="s" value="<?= e($search) ?>" placeholder="Tìm bài viết...">
                <input type="submit" class="button" value="Tìm kiếm">
            </div>
            <br class="clear">
        </div>
    </form>

    <!-- Posts Table Form for Bulk Actions -->
    <form method="POST" action="<?= ADMIN_URL ?>/posts.php">
        <?= csrf_field() ?>
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="action">
                    <option value="-1">Hành động hàng loạt</option>
                    <option value="publish">Xuất bản</option>
                    <option value="draft">Chuyển thành bản nháp</option>
                    <option value="delete">Xóa vĩnh viễn</option>
                </select>
                <input type="submit" name="bulk_action" class="button action" value="Áp dụng">
            </div>
            <div class="tablenav-pages">
                <span class="displaying-num"><?= $total_items ?> mục</span>
            </div>
            <br class="clear">
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all-1">
                    </td>
                    <th class="manage-column column-title column-primary">Tiêu đề bài viết</th>
                    <th class="manage-column column-author">Tác giả</th>
                    <th class="manage-column column-categories">Chuyên mục</th>
                    <th class="manage-column column-tags">Thẻ</th>
                    <th class="manage-column column-views">Lượt xem</th>
                    <th class="manage-column column-date">Thời gian</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                <tr class="no-items"><td class="colspanchange" colspan="7">Không tìm thấy bài viết nào.</td></tr>
                <?php else: ?>
                <?php foreach ($posts as $p): ?>
                <tr>
                    <th scope="row" class="check-column">
                        <input type="checkbox" name="post_ids[]" value="<?= $p['id'] ?>">
                    </th>
                    <td class="title column-title has-row-actions column-primary">
                        <strong>
                            <a class="row-title" href="<?= ADMIN_URL ?>/post-edit.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a>
                            <?php if ($p['status'] !== 'publish'): ?>
                            — <span class="post-state"><?= $p['status'] === 'draft' ? 'Bản nháp' : 'Riêng tư' ?></span>
                            <?php endif; ?>
                            <?php if ($p['is_featured']): ?>
                            <span class="featured-tag">★ Nổi bật</span>
                            <?php endif; ?>
                        </strong>
                        <div class="row-actions">
                            <span class="edit"><a href="<?= ADMIN_URL ?>/post-edit.php?id=<?= $p['id'] ?>">Chỉnh sửa</a> | </span>
                            <span class="trash"><a href="<?= ADMIN_URL ?>/posts.php?action=delete&id=<?= $p['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');" class="submitdelete text-danger">Xóa</a> | </span>
                            <span class="view"><a href="<?= BASE_URL ?>/post/<?= e($p['slug']) ?>" target="_blank">Xem bài viết</a></span>
                        </div>
                    </td>
                    <td class="author column-author"><?= e($p['author_name']) ?></td>
                    <td class="categories column-categories"><?= e($p['category_name'] ?: '—') ?></td>
                    <td class="tags column-tags"><?= e($p['tags'] ?: '—') ?></td>
                    <td class="views column-views font-mono"><?= number_format($p['views_count']) ?></td>
                    <td class="date column-date">
                        <abbr title="<?= $p['created_at'] ?>"><?= format_date($p['created_at']) ?></abbr><br>
                        <?= $p['status'] === 'publish' ? 'Đã xuất bản' : 'Lần sửa cuối' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
