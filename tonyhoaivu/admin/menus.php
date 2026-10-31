<?php
/**
 * TonyHoaiVu CMS - Menu Management (/admin/menus.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Menu điều hướng';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

// Handle Add Menu Item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_menu_item'])) {
    validate_csrf();
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $target = $_POST['target'] === '_blank' ? '_blank' : '_self';
    $position = in_array($_POST['position'] ?? '', ['header', 'footer']) ? $_POST['position'] : 'header';
    $order = (int)($_POST['sort_order'] ?? 0);

    if (!empty($title) && !empty($url)) {
        $ins = $db->prepare("INSERT INTO menus (title, url, target, position, sort_order) VALUES (:t, :u, :tg, :p, :o)");
        $ins->execute(['t' => $title, 'u' => $url, 'tg' => $target, 'p' => $position, 'o' => $order]);
        set_flash('success', 'Đã thêm liên kết menu mới.');
        header('Location: ' . ADMIN_URL . '/menus.php');
        exit;
    }
}

// Handle Delete Menu Item
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    validate_csrf();
    $id = (int)$_GET['id'];
    $db->prepare("DELETE FROM menus WHERE id = :id")->execute(['id' => $id]);
    set_flash('success', 'Đã xóa mục menu.');
    header('Location: ' . ADMIN_URL . '/menus.php');
    exit;
}

$header_menus = $db->query("SELECT * FROM menus WHERE position = 'header' ORDER BY sort_order ASC, id ASC")->fetchAll();
$footer_menus = $db->query("SELECT * FROM menus WHERE position = 'footer' ORDER BY sort_order ASC, id ASC")->fetchAll();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Menu điều hướng</h1>
    <hr class="wp-header-end">

    <div id="col-container" class="wp-clearfix">
        <!-- Add Menu Item -->
        <div id="col-left">
            <div class="col-wrap">
                <div class="form-wrap">
                    <h2>Thêm liên kết vào Menu</h2>
                    <form method="POST" action="">
                        <?= csrf_field() ?>
                        <div class="form-field">
                            <label for="menu-title">Tên hiển thị (Nhãn)</label>
                            <input name="title" id="menu-title" type="text" placeholder="Ví dụ: Giới thiệu" required>
                        </div>
                        <div class="form-field">
                            <label for="menu-url">Đường dẫn URL</label>
                            <input name="url" id="menu-url" type="text" placeholder="Ví dụ: /page/gioi-thieu hoặc https://..." required>
                        </div>
                        <div class="form-field">
                            <label for="menu-pos">Vị trí hiển thị</label>
                            <select name="position" id="menu-pos">
                                <option value="header">Header (Thanh điều hướng trên cùng)</option>
                                <option value="footer">Footer (Chân trang)</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="menu-target">Mở liên kết trong</label>
                            <select name="target" id="menu-target">
                                <option value="_self">Cùng một tab (_self)</option>
                                <option value="_blank">Tab mới (_blank)</option>
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="menu-order">Thứ tự sắp xếp (Số nhỏ đứng trước)</label>
                            <input name="sort_order" id="menu-order" type="number" value="1">
                        </div>
                        <p class="submit">
                            <input type="submit" name="add_menu_item" class="button button-primary" value="Thêm vào Menu">
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Menu Lists -->
        <div id="col-right">
            <div class="col-wrap">
                <h2>Menu Header (Thanh điều hướng chính)</h2>
                <table class="wp-list-table widefat fixed striped mb-6">
                    <thead>
                        <tr>
                            <th>Nhãn</th>
                            <th>Đường dẫn</th>
                            <th>Target</th>
                            <th>Thứ tự</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($header_menus)): ?>
                        <tr><td colspan="5">Chưa có mục menu header nào.</td></tr>
                        <?php else: ?>
                        <?php foreach ($header_menus as $hm): ?>
                        <tr>
                            <td><strong><?= e($hm['title']) ?></strong></td>
                            <td class="font-mono text-xs"><?= e($hm['url']) ?></td>
                            <td><?= e($hm['target']) ?></td>
                            <td><?= (int)$hm['sort_order'] ?></td>
                            <td>
                                <a href="<?= ADMIN_URL ?>/menus.php?action=delete&id=<?= $hm['id'] ?>&csrf_token=<?= csrf_token() ?>" onclick="return confirm('Xóa mục menu này?');" class="text-danger">Xóa</a>
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
