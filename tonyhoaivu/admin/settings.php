<?php
/**
 * TonyHoaiVu CMS - Website Settings (/admin/settings.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Cài đặt website';

require_once CMS_ROOT . '/admin/header.php';
require_admin(); // Settings require administrator role

$db = get_db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $settings_keys = [
        'site_title', 'site_tagline', 'admin_email', 'posts_per_page',
        'site_description', 'site_keywords', 'footer_copyright',
        'github_url', 'linkedin_url', 'facebook_url', 'twitter_url'
    ];

    foreach ($settings_keys as $key) {
        if (isset($_POST[$key])) {
            set_setting($key, trim($_POST[$key]));
        }
    }

    set_flash('success', 'Đã lưu các thiết lập cài đặt website thành công.');
    header('Location: ' . ADMIN_URL . '/settings.php');
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Cài đặt tổng quan</h1>
    <hr class="wp-header-end">

    <form method="POST" action="">
        <?= csrf_field() ?>

        <h2 class="title">Thông tin cơ bản</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="site_title">Tên Website</label></th>
                <td>
                    <input name="site_title" type="text" id="site_title" value="<?= e(get_setting('site_title')) ?>" class="regular-text" required>
                    <p class="description">Tên xuất hiện trên tiêu đề trang web và thanh điều hướng.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="site_tagline">Khẩu hiệu (Tagline)</label></th>
                <td>
                    <input name="site_tagline" type="text" id="site_tagline" value="<?= e(get_setting('site_tagline')) ?>" class="regular-text">
                    <p class="description">Trong một vài từ, giải thích trang web này nói về điều gì.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="admin_email">Email quản trị</label></th>
                <td>
                    <input name="admin_email" type="email" id="admin_email" value="<?= e(get_setting('admin_email')) ?>" class="regular-text" required>
                    <p class="description">Địa chỉ email nhận thông báo từ hệ thống và biểu mẫu liên hệ.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="posts_per_page">Số bài viết trên một trang</label></th>
                <td>
                    <input name="posts_per_page" type="number" id="posts_per_page" value="<?= e(get_setting('posts_per_page', '6')) ?>" class="small-text" min="1" max="50">
                    <span>bài viết</span>
                </td>
            </tr>
        </table>

        <h2 class="title mt-8">Cấu hình SEO &amp; Meta</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="site_description">Mô tả website (SEO Description)</label></th>
                <td>
                    <textarea name="site_description" id="site_description" rows="3" class="large-text"><?= e(get_setting('site_description')) ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="site_keywords">Từ khóa mặc định (Keywords)</label></th>
                <td>
                    <input name="site_keywords" type="text" id="site_keywords" value="<?= e(get_setting('site_keywords')) ?>" class="large-text">
                    <p class="description">Phân cách các từ khóa bằng dấu phẩy.</p>
                </td>
            </tr>
        </table>

        <h2 class="title mt-8">Liên kết Mạng xã hội &amp; Chân trang</h2>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="github_url">GitHub URL</label></th>
                <td><input name="github_url" type="url" id="github_url" value="<?= e(get_setting('github_url')) ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="linkedin_url">LinkedIn URL</label></th>
                <td><input name="linkedin_url" type="url" id="linkedin_url" value="<?= e(get_setting('linkedin_url')) ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="facebook_url">Facebook URL</label></th>
                <td><input name="facebook_url" type="url" id="facebook_url" value="<?= e(get_setting('facebook_url')) ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="twitter_url">Twitter / X URL</label></th>
                <td><input name="twitter_url" type="url" id="twitter_url" value="<?= e(get_setting('twitter_url')) ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="footer_copyright">Nội dung bản quyền chân trang</label></th>
                <td><input name="footer_copyright" type="text" id="footer_copyright" value="<?= e(get_setting('footer_copyright')) ?>" class="large-text"></td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Lưu thay đổi">
        </p>
    </form>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
