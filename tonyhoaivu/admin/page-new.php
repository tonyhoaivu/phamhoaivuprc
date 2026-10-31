<?php
/**
 * TonyHoaiVu CMS - Add/Edit Static Page (/admin/page-new.php)
 */

define('CMS_ROOT', dirname(__DIR__));
require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

$id = (int)($_GET['id'] ?? 0);
$page = null;
if ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $page = $stmt->fetch();
}

$is_edit = !empty($page);
$admin_page_title = $is_edit ? 'Chỉnh sửa trang tĩnh' : 'Thêm trang tĩnh mới';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    $content = $_POST['content'] ?? '';
    $status = in_array($_POST['status'] ?? '', ['publish', 'draft']) ? $_POST['status'] : 'publish';

    if (empty($title)) {
        $error = 'Tiêu đề không được để trống.';
    } else {
        if ($is_edit) {
            $up = $db->prepare("UPDATE pages SET title = :t, slug = :s, content = :c, status = :st WHERE id = :id");
            $up->execute(['t' => $title, 's' => $slug, 'c' => $content, 'st' => $status, 'id' => $id]);
            set_flash('success', 'Trang đã được cập nhật thành công! <a href="' . BASE_URL . '/page/' . $slug . '" target="_blank">Xem trang</a>');
            header('Location: ' . ADMIN_URL . '/page-new.php?id=' . $id);
            exit;
        } else {
            $ins = $db->prepare("INSERT INTO pages (author_id, title, slug, content, status) VALUES (:uid, :t, :s, :c, :st)");
            $ins->execute(['uid' => $current_user['id'], 't' => $title, 's' => $slug, 'c' => $content, 'st' => $status]);
            $new_id = (int)$db->lastInsertId();
            set_flash('success', 'Trang mới đã được tạo thành công! <a href="' . BASE_URL . '/page/' . $slug . '" target="_blank">Xem trang</a>');
            header('Location: ' . ADMIN_URL . '/page-new.php?id=' . $new_id);
            exit;
        }
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= $is_edit ? 'Chỉnh sửa trang tĩnh' : 'Thêm trang tĩnh mới' ?></h1>
    <a href="<?= ADMIN_URL ?>/pages.php" class="page-title-action">Quay lại danh sách</a>
    <hr class="wp-header-end">

    <?php if (!empty($error)): ?>
    <div class="notice notice-error"><p><?= e($error) ?></p></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?= csrf_field() ?>
        
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div id="titlediv" class="mb-4">
                        <input type="text" name="title" value="<?= e($page['title'] ?? '') ?>" id="title" placeholder="Thêm tiêu đề trang tại đây..." required class="large-text">
                        <div class="permalink-area">
                            <span class="permalink-label">Đường dẫn trang:</span>
                            <span class="permalink-prefix"><?= BASE_URL ?>/page/</span>
                            <input type="text" name="slug" id="slug" value="<?= e($page['slug'] ?? '') ?>" class="permalink-input">
                        </div>
                    </div>

                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Nội dung trang</h2>
                        </div>
                        <div class="inside p-0">
                            <!-- Visual Toolbar -->
                            <div class="wysiwyg-toolbar" id="editor-toolbar">
                                <div class="toolbar-group">
                                    <button type="button" class="tb-btn" data-cmd="bold"><b>B</b></button>
                                    <button type="button" class="tb-btn" data-cmd="italic"><i>I</i></button>
                                    <button type="button" class="tb-btn" data-cmd="underline"><u>U</u></button>
                                    <button type="button" class="tb-btn" data-cmd="insertUnorderedList">• List</button>
                                    <button type="button" class="tb-btn" data-cmd="insertOrderedList">1. List</button>
                                </div>
                            </div>
                            <div id="wysiwyg-content" contenteditable="true" class="wysiwyg-editor-area" style="min-height:350px;"><?= $page['content'] ?? '' ?></div>
                            <textarea name="content" id="raw-content" class="raw-textarea hidden" rows="18"><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Đăng trang</h2>
                        </div>
                        <div class="inside">
                            <div class="misc-pub-section">
                                <label class="block font-semibold mb-1">Trạng thái:</label>
                                <select name="status" class="w-100">
                                    <option value="publish" <?= ($page['status'] ?? 'publish') === 'publish' ? 'selected' : '' ?>>Đã xuất bản</option>
                                    <option value="draft" <?= ($page['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
                                </select>
                            </div>
                            <div class="major-publishing-actions">
                                <input type="submit" class="button button-primary button-large" value="<?= $is_edit ? 'Cập nhật' : 'Xuất bản trang' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
