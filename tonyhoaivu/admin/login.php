<?php
/**
 * TonyHoaiVu CMS - Admin Login Screen
 * Classic WordPress aesthetic login interface
 */

define('CMS_ROOT', dirname(__DIR__));
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/csrf.php';
require_once CMS_ROOT . '/includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ' . ADMIN_URL . '/index.php');
    exit;
}

$site_title = get_setting('site_title', 'TonyHoaiVu');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $login = trim($_POST['log'] ?? '');
    $pwd = $_POST['pwd'] ?? '';

    if (empty($login) || empty($pwd)) {
        $error = 'Vui lòng nhập tên đăng nhập/email và mật khẩu.';
    } else {
        $res = login_user($login, $pwd);
        if ($res['success']) {
            $redirect = $_SESSION['redirect_after_login'] ?? (ADMIN_URL . '/index.php');
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $res['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập &lsaquo; <?= e($site_title) ?> &#8212; WordPress-style CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="login-action-login wp-core-ui">
    <div id="login" class="wp-login-container">
        <h1>
            <a href="<?= BASE_URL ?>/" title="<?= e($site_title) ?>" class="login-logo-link">
                <span class="logo-circle">T</span>
                <span class="logo-text"><?= e($site_title) ?> CMS</span>
            </a>
        </h1>

        <?php if (!empty($error)): ?>
        <div id="login_error" class="notice notice-error">
            <strong>LỖI:</strong> <?= e($error) ?>
        </div>
        <?php endif; ?>

        <form name="loginform" id="loginform" action="" method="post">
            <?= csrf_field() ?>

            <p>
                <label for="user_login">Tên người dùng hoặc Địa chỉ Email</label>
                <input type="text" name="log" id="user_login" class="input" value="<?= e($_POST['log'] ?? '') ?>" size="20" autofocus required>
            </p>

            <div class="user-pass-wrap">
                <label for="user_pass">Mật khẩu</label>
                <div class="wp-pwd">
                    <input type="password" name="pwd" id="user_pass" class="input password-input" value="" size="20" required>
                </div>
            </div>

            <p class="forgetmenot">
                <label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Tự động đăng nhập</label>
            </p>

            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Đăng nhập">
            </p>
        </form>

        <p id="nav">
            <a href="<?= BASE_URL ?>/">&larr; Quay lại trang chủ <?= e($site_title) ?></a>
        </p>

        <div class="demo-credentials-card">
            <strong>Tài khoản Quản trị mặc định:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>admin123</code>
        </div>
    </div>
</body>
</html>
