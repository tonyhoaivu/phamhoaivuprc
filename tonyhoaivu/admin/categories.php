<?php
/**
 * TonyHoaiVu CMS - Categories Manager (/admin/categories.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Chuyên mục';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    validate_csrf();
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (empty($name)) {
        $error = 'Tên chuyên mục không được để trống.';
    } else {
        if (empty($slug)) {
            $slug = slugify($name);
        } else {
            $slug = slugify($slug);
        }

        // Check duplicate
        $chk = $db->prepare("SELECT id FROM categories WHERE slug = :s LIMIT 1");
        $chk->execute(['s' => $slug]);
        if ($chk->fetch()) {
            $slug .= '-' . time();
        }

        $ins = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :desc)");
        $ins->execute(['name' => $name, 'slug' => $slug, 'desc' => $desc]);
        set_flash('success', 'Đã thêm chuyên mục "' . htmlspecialchars($name) . '" thành công.');
        header('Location: ' . ADMIN_URL . '/categories.php');
        exit;
    }
}

// Handle Delete Category
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    validate_csrf();
    $id = (int)$_GET['id'];
    $del = $db->prepare("DELETE FROM categories WHERE id = :id");
    $del->execute(['id' => $id]);
    set_flash('success', 'Đã xóa chuyên mục thành công.');
    header('Location: ' . ADMIN_URL . '/categories.php');
    exit;
}

// Fetch categories with post count
$cats = $db->query("
    SELECT c.*, COUNT(p.id) as count
    FROM categories c
    LEFT JOIN posts p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name ASC
")->fetchAll();
?>

<div class="wrap nosubsub">
    <h1 class="wp-heading-inline">Chuyên mục bài viết</h1>
    <hr class="wp-header-end">

    <?php if (!empty($error)): ?>
    <div class="notice notice-error"><p><?= e($error) ?></p></div>
    <?php endif; ?>

    <div id="col-container" class="wp-clearfix">
        <!-- Left: Add New Category Form -->
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2>Thêm chuyên mục mới</h2>
                    <form method="POST" action="">
                        <?= csrf_field() ?>
                        <div class="form-field form-required term-name-wrap">
                            <label for="tag-name">Tên chuyên mục</label>
                            <input name="name" id="tag-name" type="text" value="" size="40" aria-required="true" required>
                            <p class="description">Tên xuất hiện trên website của bạn.</p>
                        </div>
                        <div class="form-field term-slug-wrap">
                            <label for="tag-slug">Đường dẫn (Slug)</label>
                            <input name="slug" id="tag-slug" type="text" value="" size="40">
                            <p class="description">Chuỗi định danh dạng chữ thường không dấu, ví dụ: <code>lap-trinh-php</code>.</p>
                        </div>
                        <div class="form-field term-description-wrap">
                            <label for="tag-description">Mô tả</label>
                            <textarea name="description" id="tag-description" rows="5" cols="40"></textarea>
                            <p class="description">Đoạn tóm tắt giới thiệu về chuyên mục này.</p>
                        </div>
                        <p class="submit">
                            <input type="submit" name="add_category" id="submit" class="button button-primary" value="Thêm chuyên mục mới">
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Categories Table -->
        <div id="col-right">
            <div class="col-wrap">
                <table class="wp-list-table widefat fixed striped tags">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-name column-primary">Tên</th>
                            <th scope="col" class="manage-column column-description">Mô tả</th>
                            <th scope="col" class="manage-column column-slug">Đường dẫn tĩnh</th>
                            <th scope="col" class="manage-column column-posts font-mono">Số bài viết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cats)): ?>
                        <tr><td colspan="4">Chưa có chuyên mục nào.</td></tr>
                        <?php else: ?>
                        <?php foreach ($cats as $c): ?>
                        <tr>
                            <td class="name column-name has-row-actions column-primary">
                                <strong><a class="row-title" href="<?= BASE_URL ?>/category/<?= e($c['slug']) ?>" target="_blank"><?= e($c['name']) ?></a></strong>
                                <div class="row-actions">
                                    <span class="delete">
                                        <a href="<?= ADMIN_URL ?>/categories.php?action=delete&id=<?= $c['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa chuyên mục này không? Các bài viết sẽ không bị xóa.');" class="delete-tag text-danger">Xóa</a> | 
                                    </span>
                                    <span class="view"><a href="<?= BASE_URL ?>/category/<?= e($c['slug']) ?>" target="_blank">Xem</a></span>
                                </div>
                            </td>
                            <td class="description column-description"><?= e($c['description'] ?: '—') ?></td>
                            <td class="slug column-slug font-mono text-xs"><?= e($c['slug']) ?></td>
                            <td class="posts column-posts font-mono">
                                <a href="<?= ADMIN_URL ?>/posts.php?cat=<?= $c['id'] ?>"><?= (int)$c['count'] ?></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
