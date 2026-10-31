<?php
/**
 * TonyHoaiVu CMS - Media Library (/admin/media.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Thư viện Media';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Handle File Upload
$upload_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_file'])) {
    validate_csrf();
    $res = handle_file_upload($_FILES['media_file'], $current_user['id']);
    if ($res['success']) {
        set_flash('success', 'Tải lên hình ảnh thành công: <code>' . htmlspecialchars($res['filename']) . '</code>');
        header('Location: ' . ADMIN_URL . '/media.php');
        exit;
    } else {
        $upload_err = $res['error'];
    }
}

// Handle Delete Media
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    validate_csrf();
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT file_path FROM media WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $media = $stmt->fetch();

    if ($media) {
        $file_path = CMS_ROOT . '/' . $media['file_path'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
        $db->prepare("DELETE FROM media WHERE id = :id")->execute(['id' => $id]);
        set_flash('success', 'Đã xóa file media vĩnh viễn.');
    }
    header('Location: ' . ADMIN_URL . '/media.php');
    exit;
}

$media_items = $db->query("SELECT m.*, u.display_name FROM media m LEFT JOIN users u ON m.uploader_id = u.id ORDER BY m.created_at DESC")->fetchAll();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Thư viện Media</h1>
    <hr class="wp-header-end">

    <?php if ($upload_err): ?>
    <div class="notice notice-error"><p><?= e($upload_err) ?></p></div>
    <?php endif; ?>

    <!-- Upload Box -->
    <div class="postbox mb-6">
        <div class="postbox-header">
            <h2>Tải tệp tin mới lên</h2>
        </div>
        <div class="inside">
            <form action="" method="POST" enctype="multipart/form-data" class="media-upload-form">
                <?= csrf_field() ?>
                <div class="upload-dropzone">
                    <input type="file" name="media_file" id="media_file" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml" required class="hidden">
                    <label for="media_file" class="dropzone-label">
                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span class="main-text">Kéo thả tệp tin vào đây hoặc nhấn để chọn file</span>
                        <span class="sub-text">Hỗ trợ JPG, PNG, GIF, WEBP, SVG. Kích thước tối đa: 5MB</span>
                    </label>
                    <div id="selected-file-name" class="mt-2 text-xs font-semibold text-slate-700 hidden"></div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="button button-primary">Bắt đầu tải lên</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Media Grid -->
    <h2>Tất cả tệp tin đã tải lên (<?= count($media_items) ?>)</h2>

    <?php if (empty($media_items)): ?>
    <div class="notice notice-info">
        <p>Thư viện media đang trống. Bạn có thể tải hình ảnh lên để chèn vào bài viết hoặc đặt làm ảnh đại diện!</p>
    </div>
    <?php else: ?>
    <div class="media-grid">
        <?php foreach ($media_items as $item): 
            $url = BASE_URL . '/' . $item['file_path'];
        ?>
        <div class="media-card">
            <div class="media-thumbnail">
                <img src="<?= e($url) ?>" alt="<?= e($item['original_name']) ?>" loading="lazy" referrerpolicy="no-referrer">
            </div>
            <div class="media-meta">
                <div class="media-title truncate" title="<?= e($item['original_name']) ?>"><?= e($item['original_name']) ?></div>
                <div class="media-sub font-mono"><?= round($item['file_size'] / 1024, 1) ?> KB · <?= format_date($item['created_at']) ?></div>
                <div class="media-actions">
                    <button type="button" class="btn-copy-url button button-small" data-url="<?= e($url) ?>">Sao chép URL</button>
                    <a href="<?= ADMIN_URL ?>/media.php?action=delete&id=<?= $item['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa hình ảnh này vĩnh viễn không?');" class="button button-small text-danger">Xóa</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
