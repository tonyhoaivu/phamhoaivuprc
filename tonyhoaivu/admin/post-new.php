<?php
/**
 * TonyHoaiVu CMS - Add New Post (/admin/post-new.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Thêm bài viết mới';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug) && !empty($title)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }
    
    // Check if slug exists, append random if duplicate
    $chk = $db->prepare("SELECT id FROM posts WHERE slug = :s LIMIT 1");
    $chk->execute(['s' => $slug]);
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
        $error = 'Vui lòng nhập tiêu đề bài viết.';
    } else {
        $ins = $db->prepare("
            INSERT INTO posts (author_id, category_id, title, slug, excerpt, content, featured_image, tags, status, is_featured, seo_title, seo_description)
            VALUES (:uid, :cid, :title, :slug, :excerpt, :content, :img, :tags, :status, :feat, :seot, :seod)
        ");
        $ins->execute([
            'uid'     => $current_user['id'],
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
            'seod'    => $seo_description
        ]);

        $new_id = (int)$db->lastInsertId();
        
        // Update category post count
        if ($category_id) {
            $db->prepare("UPDATE categories SET post_count = (SELECT COUNT(*) FROM posts WHERE category_id = :cid AND status = 'publish') WHERE id = :cid2")
               ->execute(['cid' => $category_id, 'cid2' => $category_id]);
        }

        set_flash('success', 'Bài viết đã được tạo thành công! <a href="' . BASE_URL . '/post/' . $slug . '" target="_blank">Xem bài viết</a>');
        header('Location: ' . ADMIN_URL . '/post-edit.php?id=' . $new_id);
        exit;
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Thêm bài viết mới</h1>
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
                            <input type="text" name="title" size="30" value="" id="title" spellcheck="true" autocomplete="off" placeholder="Thêm tiêu đề bài viết tại đây..." required class="large-text">
                        </div>
                        <div class="permalink-area">
                            <span class="permalink-label">Đường dẫn tĩnh (Slug):</span>
                            <span class="permalink-prefix"><?= BASE_URL ?>/post/</span>
                            <input type="text" name="slug" id="slug" value="" class="permalink-input" placeholder="tu-dong-theo-tieu-de">
                        </div>
                    </div>

                    <!-- WYSIWYG Editor Component -->
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
                            <div id="wysiwyg-content" contenteditable="true" class="wysiwyg-editor-area"></div>
                            <!-- Hidden Textarea to submit data -->
                            <textarea name="content" id="raw-content" class="raw-textarea hidden" rows="18"></textarea>
                        </div>
                    </div>

                    <!-- Excerpt Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Tóm tắt ngắn (Excerpt)</h2>
                        </div>
                        <div class="inside">
                            <textarea name="excerpt" rows="3" class="large-text" placeholder="Đoạn văn ngắn hiển thị ngoài danh sách bài viết..."></textarea>
                            <p class="description">Nếu để trống, hệ thống sẽ tự động trích 160 ký tự đầu tiên của nội dung bài viết.</p>
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
                                <input type="text" name="seo_title" id="seo_title" class="regular-text w-100" placeholder="Tiêu đề hiển thị trên Google">
                            </div>
                            <div class="form-field">
                                <label for="seo_description" class="font-semibold block mb-1">Mô tả SEO (Meta Description)</label>
                                <textarea name="seo_description" id="seo_description" rows="3" class="regular-text w-100" placeholder="Đoạn mô tả ngắn gọn thu hút người đọc trên công cụ tìm kiếm..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar Column -->
                <div id="postbox-container-1" class="postbox-container">
                    <!-- Publish Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Đăng bài viết</h2>
                        </div>
                        <div class="inside">
                            <div class="submitbox">
                                <div class="misc-pub-section">
                                    <label class="block font-semibold mb-1">Trạng thái:</label>
                                    <select name="status" class="w-100">
                                        <option value="publish" selected>Công khai (Published)</option>
                                        <option value="draft">Bản nháp (Draft)</option>
                                        <option value="private">Riêng tư (Private)</option>
                                    </select>
                                </div>
                                <div class="misc-pub-section">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_featured" value="1">
                                        <span>Đặt làm bài viết nổi bật</span>
                                    </label>
                                </div>
                                <div class="major-publishing-actions">
                                    <div class="publishing-action">
                                        <button type="button" id="btn-preview-post" class="button">Xem trước</button>
                                        <input type="submit" class="button button-primary button-large" value="Xuất bản">
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
                                    <input type="radio" name="category_id" value="" checked>
                                    <span>-- Không chọn --</span>
                                </label>
                                <?php foreach ($categories as $cat): ?>
                                <label class="radio-label block mb-2">
                                    <input type="radio" name="category_id" value="<?= $cat['id'] ?>">
                                    <span><?= e($cat['name']) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="pt-2 border-t mt-3">
                                <a href="<?= ADMIN_URL ?>/categories.php" target="_blank" class="text-xs text-blue-600">+ Quản lý chuyên mục</a>
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
                                <span class="placeholder-text text-xs text-muted">Chưa chọn ảnh</span>
                            </div>
                            <input type="text" name="featured_image" id="featured_image" class="regular-text w-100 mb-2" placeholder="Dán URL hình ảnh hoặc chọn từ Media...">
                            <a href="<?= ADMIN_URL ?>/media.php" target="_blank" class="button button-secondary button-small w-100 text-center">Mở thư viện Media</a>
                        </div>
                    </div>

                    <!-- Tags Box -->
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2>Thẻ từ khóa (Tags)</h2>
                        </div>
                        <div class="inside">
                            <input type="text" name="tags" class="regular-text w-100" placeholder="PHP, MySQL, Cloud, Web...">
                            <p class="description">Phân cách các thẻ bằng dấu phẩy (,)</p>
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
