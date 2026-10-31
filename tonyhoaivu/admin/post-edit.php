<?php
/**
 * TonyHoaiVu CMS - Edit Post (/admin/post-edit.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Chỉnh sửa bài viết';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: ' . ADMIN_URL . '/posts.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM posts WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    die("Không tìm thấy bài viết này.");
}

$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }

    // Check slug collision with other posts
    $chk = $db->prepare("SELECT id FROM posts WHERE slug = :s AND id != :id LIMIT 1");
    $chk->execute(['s' => $slug, 'id' => $id]);
    if ($chk->fetch()) {
        $slug .= '-' . time();
    }

    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    if (empty($excerpt)) {
        $excerpt = substr(strip_tags($content), 0, 160);
    }

    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $featured_image = trim($_POST['featured_image'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['publish', 'draft', 'private']) ? $_POST['status'] : 'publish';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $seo_title = trim($_POST['seo_title'] ?? '');
    $seo_description = trim($_POST['seo_description'] ?? '');

    if (empty($title)) {
        $error = 'Tiêu đề không được để trống.';
    } else {
        $up = $db->prepare("
            UPDATE posts 
            SET category_id = :cid, title = :title, slug = :slug, excerpt = :excerpt,
                content = :content, featured_image = :img, tags = :tags, status = :status,
                is_featured = :feat, seo_title = :seot, seo_description = :seod
            WHERE id = :id
        ");
        $up->execute([
            'cid'     => $category_id,
            'title'   => $title,
            'slug'    => $slug,
            'excerpt' => $excerpt,
            'content' => $content,
            'img'     => $featured_image,
            'tags'    => $tags,
            'status'  => $status,
            'feat'    => $is_featured,
            'seot'    => $seo_title,
            'seod'    => $seo_description,
            'id'      => $id
        ]);

        set_flash('success', 'Bài viết đã được cập nhật thành công! <a href="' . BASE_URL . '/post/' . $slug . '" target="_blank">Xem bài viết</a>');
        header('Location: ' . ADMIN_URL . '/post-edit.php?id=' . $id);
        exit;
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Chỉnh sửa bài viết</h1>
    <a href="<?= ADMIN_URL ?>/post-new.php" class="page-title-action">Thêm bài viết mới</a>
    <hr class="wp-header-end">

    <?php if (!empty($error)): ?>
    <div class="notice notice-error"><p><?= e($error) ?></p></div>
    <?php endif; ?>

    <form method="POST" action="" id="post-form">
        <?= csrf_field() ?>
        
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- Main Column -->
                <div id="post-body-content">
                    <div id="titlediv" class="mb-4">
                        <div id="titlewrap">
                            <label class="screen-reader-text" for="title">Thêm tiêu đề</label>
                            <input type="text" name="title" size="30" value="<?= e($post['title']) ?>" id="title" spellcheck="true" autocomplete="off" placeholder="Tiêu đề bài viết..." required class="large-text">
                        </div>
                        <div class="permalink-area">
                            <span class="permalink-label">Đường dẫn tĩnh (Slug):</span>
                            <span class="permalink-prefix"><?= BASE_URL ?>/post/</span>
                            <input type="text" name="slug" id="slug" value="<?= e($post['slug']) ?>" class="permalink-input">
                        </div>
                    </div>

                    <!-- WYSIWYG Editor -->
                    <div class="postbox editor-postbox">
                        <div class="postbox-header">
                            <h2>Nội dung bài viết</h2>
                            <div class="editor-mode-toggle">
                                <button type="button" class="button button-small active" id="btn-mode-visual">Trực quan (Visual)</button>
                                <button type="button" class="button button-small" id="btn-mode-html">Văn bản HTML</button>
                            </div>
                        </div>
                        <div class="inside p-0">
                            <!-- Visual Toolbar -->
                            <div class="wysiwyg-toolbar" id="editor-toolbar">
                                <div class="toolbar-group">
                                    <button type="button" class="tb-btn" data-cmd="bold" title="In đậm (Ctrl+B)"><b>B</b></button>
                                    <button type="button" class="tb-btn" data-cmd="italic" title="In nghiêng (Ctrl+I)"><i>I</i></button>
                                    <button type="button" class="tb-btn" data-cmd="underline" title="Gạch chân"><u>U</u></button>
                                    <button type="button" class="tb-btn" data-cmd="strikeThrough" title="Gạch ngang"><s>S</s></button>
                                </div>
                                <div class="toolbar-group">
                                    <select id="heading-selector" class="tb-select" title="Định dạng tiêu đề">
                                        <option value="p">Đoạn văn (Paragraph)</option>
                                        <option value="h2">Tiêu đề 2 (H2)</option>
                                        <option value="h3">Tiêu đề 3 (H3)</option>
                                        <option value="h4">Tiêu đề 4 (H4)</option>
                                        <option value="h5">Tiêu đề 5 (H5)</option>
                                        <option value="h6">Tiêu đề 6 (H6)</option>
                                    </select>
                                </div>
                                <div class="toolbar-group">
                                    <button type="button" class="tb-btn" data-cmd="justifyLeft" title="Căn trái">Left</button>
                                    <button type="button" class="tb-btn" data-cmd="justifyCenter" title="Căn giữa">Center</button>
                                    <button type="button" class="tb-btn" data-cmd="justifyRight" title="Căn phải">Right</button>
                                </div>
                                <div class="toolbar-group">
                                    <button type="button" class="tb-btn" data-cmd="insertUnorderedList" title="Danh sách chấm">• List</button>
                                    <button type="button" class="tb-btn" data-cmd="insertOrderedList" title="Danh sách số">1. List</button>
                                    <button type="button" class="tb-btn" data-cmd="formatBlock" data-val="blockquote" title="Trích dẫn blockquote">"Quote"</button>
                                    <button type="button" class="tb-btn" data-cmd="formatBlock" data-val="pre" title="Khối mã Code">&lt;/&gt;</button>
                                </div>
                                <div class="toolbar-group">
                                    <button type="button" class="tb-btn" id="tb-insert-link" title="Chèn liên kết">Link</button>
                                    <button type="button" class="tb-btn" id="tb-insert-img" title="Chèn hình ảnh">Ảnh</button>
                                    <button type="button" class="tb-btn" id="tb-insert-table" title="Tạo bảng">Bảng</button>
                                </div>
                            </div>

                            <!-- Editable Canvas -->
                            <div id="wysiwyg-content" contenteditable="true" class="wysiwyg-editor-area"><?= $post['content'] ?></div>
                            <textarea name="content" id="raw-content" class="raw-textarea hidden" rows="18"><?= htmlspecialchars($post['content']) ?></textarea>
                        </div>
                    </div>

                    <!-- Excerpt Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Tóm tắt ngắn (Excerpt)</h2>
                        </div>
                        <div class="inside">
                            <textarea name="excerpt" rows="3" class="large-text"><?= e($post['excerpt']) ?></textarea>
                        </div>
                    </div>

                    <!-- SEO Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Tối ưu hóa tìm kiếm (SEO)</h2>
                        </div>
                        <div class="inside">
                            <div class="form-field mb-3">
                                <label for="seo_title" class="font-semibold block mb-1">Tiêu đề SEO</label>
                                <input type="text" name="seo_title" id="seo_title" value="<?= e($post['seo_title']) ?>" class="regular-text w-100">
                            </div>
                            <div class="form-field">
                                <label for="seo_description" class="font-semibold block mb-1">Mô tả SEO</label>
                                <textarea name="seo_description" id="seo_description" rows="3" class="regular-text w-100"><?= e($post['seo_description']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar Column -->
                <div id="postbox-container-1" class="postbox-container">
                    <!-- Publish Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Cập nhật bài viết</h2>
                        </div>
                        <div class="inside">
                            <div class="submitbox">
                                <div class="misc-pub-section">
                                    <label class="block font-semibold mb-1">Trạng thái:</label>
                                    <select name="status" class="w-100">
                                        <option value="publish" <?= $post['status'] === 'publish' ? 'selected' : '' ?>>Công khai (Published)</option>
                                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Bản nháp (Draft)</option>
                                        <option value="private" <?= $post['status'] === 'private' ? 'selected' : '' ?>>Riêng tư (Private)</option>
                                    </select>
                                </div>
                                <div class="misc-pub-section">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_featured" value="1" <?= $post['is_featured'] ? 'checked' : '' ?>>
                                        <span>Đặt làm bài viết nổi bật</span>
                                    </label>
                                </div>
                                <div class="misc-pub-section text-xs text-muted">
                                    <span>Lượt xem: <strong><?= number_format($post['views_count']) ?></strong></span>
                                </div>
                                <div class="major-publishing-actions">
                                    <div class="delete-action">
                                        <a href="<?= ADMIN_URL ?>/posts.php?action=delete&id=<?= $post['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này vĩnh viễn không?');" class="submitdelete deletion text-danger">Xóa bài viết</a>
                                    </div>
                                    <div class="publishing-action">
                                        <button type="button" id="btn-preview-post" class="button">Xem trước</button>
                                        <input type="submit" class="button button-primary button-large" value="Cập nhật">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Chuyên mục</h2>
                        </div>
                        <div class="inside">
                            <div class="categorychecklist">
                                <label class="radio-label block mb-2">
                                    <input type="radio" name="category_id" value="" <?= empty($post['category_id']) ? 'checked' : '' ?>>
                                    <span>-- Không chọn --</span>
                                </label>
                                <?php foreach ($categories as $cat): ?>
                                <label class="radio-label block mb-2">
                                    <input type="radio" name="category_id" value="<?= $cat['id'] ?>" <?= $post['category_id'] == $cat['id'] ? 'checked' : '' ?>>
                                    <span><?= e($cat['name']) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Ảnh đại diện</h2>
                        </div>
                        <div class="inside">
                            <div id="featured-image-preview" class="featured-img-box mb-2">
                                <?php if (!empty($post['featured_image'])): ?>
                                <img src="<?= e($post['featured_image']) ?>" alt="" style="max-width:100%; height:auto; border-radius:4px;">
                                <?php else: ?>
                                <span class="placeholder-text text-xs text-muted">Chưa chọn ảnh</span>
                                <?php endif; ?>
                            </div>
                            <input type="text" name="featured_image" id="featured_image" value="<?= e($post['featured_image']) ?>" class="regular-text w-100 mb-2">
                            <a href="<?= ADMIN_URL ?>/media.php" target="_blank" class="button button-secondary button-small w-100 text-center">Mở thư viện Media</a>
                        </div>
                    </div>

                    <!-- Tags Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Thẻ từ khóa (Tags)</h2>
                        </div>
                        <div class="inside">
                            <input type="text" name="tags" value="<?= e($post['tags']) ?>" class="regular-text w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="modal-backdrop hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="preview-modal-title">Xem trước bài viết</h3>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <div class="modal-body" id="preview-modal-body"></div>
    </div>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
