<?php
/**
 * TonyHoaiVu CMS - User Management (/admin/users.php)
 */

define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Quản lý thành viên';

require_once CMS_ROOT . '/admin/header.php';
$db = get_db();

$edit_id = (int)($_GET['edit'] ?? 0);
$edit_user = null;
if ($edit_id > 0) {
    $u_stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $u_stmt->execute(['id' => $edit_id]);
    $edit_user = $u_stmt->fetch();
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    require_admin();
    validate_csrf();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['administrator', 'editor']) ? $_POST['role'] : 'editor';

    if (empty($username) || empty($email) || empty($pass)) {
        $error = 'Vui lòng điền đủ tên đăng nhập, email và mật khẩu.';
    } else {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $ins = $db->prepare("INSERT INTO users (username, email, password, display_name, role) VALUES (:u, :e, :p, :dn, :r)");
        $ins->execute([
            'u'  => $username,
            'e'  => $email,
            'p'  => $hash,
            'dn' => $display_name ?: $username,
            'r'  => $role
        ]);
        set_flash('success', 'Đã thêm thành viên mới thành công.');
        header('Location: ' . ADMIN_URL . '/users.php');
        exit;
    }
}

// Handle Update Profile / Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    validate_csrf();
    $target_id = (int)$_POST['user_id'];
    
    // Only admin or the user themselves can update
    if (!is_admin() && $target_id !== (int)$current_user['id']) {
        die('Không có quyền chỉnh sửa.');
    }

    $email = trim($_POST['email'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $avatar = trim($_POST['avatar'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');

    if (!empty($new_pass)) {
        $hash = password_hash($new_pass, PASSWORD_BCRYPT);
        $up = $db->prepare("UPDATE users SET email = :e, display_name = :dn, avatar = :av, bio = :b, password = :p WHERE id = :id");
        $up->execute(['e' => $email, 'dn' => $display_name, 'av' => $avatar, 'b' => $bio, 'p' => $hash, 'id' => $target_id]);
    } else {
        $up = $db->prepare("UPDATE users SET email = :e, display_name = :dn, avatar = :av, bio = :b WHERE id = :id");
        $up->execute(['e' => $email, 'dn' => $display_name, 'av' => $avatar, 'b' => $bio, 'id' => $target_id]);
    }

    set_flash('success', 'Đã cập nhật hồ sơ thành công.');
    header('Location: ' . ADMIN_URL . '/users.php?edit=' . $target_id);
    exit;
}

// Fetch all users with post count
$users = $db->query("
    SELECT u.*, COUNT(p.id) as post_count
    FROM users u
    LEFT JOIN posts p ON u.id = p.author_id
    GROUP BY u.id
    ORDER BY u.id ASC
")->fetchAll();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Thành viên</h1>
    <hr class="wp-header-end">

    <?php if ($edit_user): ?>
    <!-- Edit User Profile Form -->
    <div class="postbox max-w-2xl mb-8">
        <div class="postbox-header">
            <h2>Chỉnh sửa hồ sơ: <?= e($edit_user['display_name']) ?> (<?= e($edit_user['username']) ?>)</h2>
        </div>
        <div class="inside p-6">
            <form method="POST" action="">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?>">

                <table class="form-table">
                    <tr>
                        <th scope="row">Tên đăng nhập</th>
                        <td><input type="text" value="<?= e($edit_user['username']) ?>" disabled class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="display_name">Tên hiển thị</label></th>
                        <td><input type="text" name="display_name" id="display_name" value="<?= e($edit_user['display_name']) ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="email">Địa chỉ Email</label></th>
                        <td><input type="email" name="email" id="email" value="<?= e($edit_user['email']) ?>" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="avatar">URL Ảnh đại diện (Avatar)</label></th>
                        <td>
                            <input type="text" name="avatar" id="avatar" value="<?= e($edit_user['avatar']) ?>" class="regular-text">
                            <p class="description">Dán link ảnh đại diện của bạn.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="bio">Tiểu sử (Bio)</label></th>
                        <td>
                            <textarea name="bio" id="bio" rows="4" class="regular-text"><?= e($edit_user['bio']) ?></textarea>
                            <p class="description">Được hiển thị trong khung tác giả ở chân bài viết và thanh bên.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="new_password">Mật khẩu mới</label></th>
                        <td>
                            <input type="password" name="new_password" id="new_password" placeholder="Để trống nếu không đổi..." class="regular-text">
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" name="update_profile" class="button button-primary" value="Cập nhật hồ sơ">
                    <a href="<?= ADMIN_URL ?>/users.php" class="button">Hủy bỏ</a>
                </p>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Users Table -->
    <table class="wp-list-table widefat fixed striped users">
        <thead>
            <tr>
                <th class="manage-column column-username column-primary">Tên đăng nhập</th>
                <th class="manage-column column-name">Tên hiển thị</th>
                <th class="manage-column column-email">Email</th>
                <th class="manage-column column-role">Vai trò</th>
                <th class="manage-column column-posts font-mono">Số bài viết</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="username column-username has-row-actions column-primary">
                    <img src="<?= e($u['avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60') ?>" class="avatar avatar-32 photo" width="32" height="32" alt="" referrerpolicy="no-referrer">
                    <strong><a href="<?= ADMIN_URL ?>/users.php?edit=<?= $u['id'] ?>"><?= e($u['username']) ?></a></strong>
                    <div class="row-actions">
                        <span class="edit"><a href="<?= ADMIN_URL ?>/users.php?edit=<?= $u['id'] ?>">Chỉnh sửa</a></span>
                    </div>
                </td>
                <td class="name column-name"><?= e($u['display_name']) ?></td>
                <td class="email column-email"><a href="mailto:<?= e($u['email']) ?>"><?= e($u['email']) ?></a></td>
                <td class="role column-role">
                    <span class="role-badge <?= $u['role'] === 'administrator' ? 'role-admin' : 'role-editor' ?>">
                        <?= $u['role'] === 'administrator' ? 'Quản trị viên' : 'Biên tập viên' ?>
                    </span>
                </td>
                <td class="posts column-posts font-mono"><?= (int)$u['post_count'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once CMS_ROOT . '/admin/footer.php'; ?>
