<?php
/**
 * TonyHoaiVu CMS - Automatic Web Installer (Trình cài đặt tự động)
 * Guides user through environment checks, database configuration, admin setup, and lock creation
 */

define('CMS_ROOT', __DIR__);

// Check if already installed
if (file_exists(CMS_ROOT . '/install.lock')) {
    die('<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Hệ thống đã cài đặt</title><link rel="stylesheet" href="assets/css/admin.css"></head><body class="wp-core-ui"><div class="wp-login-container"><div class="notice notice-info"><h2>Hệ thống đã được cài đặt!</h2><p>Tệp tin <code>install.lock</code> đã tồn tại. Để bảo mật, trình cài đặt đã bị khóa.</p><p><a href="admin/login.php" class="button button-primary">Đăng nhập Quản trị (Admin)</a> <a href="index.php" class="button">Về trang chủ</a></p></div></div></body></html>');
}

$step = (int)($_GET['step'] ?? 1);
$error = '';
$success = '';

// Step 1: Environment check
$php_version_ok = version_compare(PHP_VERSION, '8.1.0', '>=');
$pdo_ok = extension_loaded('pdo') && extension_loaded('pdo_mysql');
$config_writable = is_writable(CMS_ROOT . '/config') || is_writable(CMS_ROOT . '/config/config.php');
$uploads_writable = is_dir(CMS_ROOT . '/uploads') ? is_writable(CMS_ROOT . '/uploads') : is_writable(CMS_ROOT);

// Step 2: Handle DB Configuration & Installation Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_install'])) {
    $site_name = trim($_POST['site_name'] ?? 'TonyHoaiVu');
    $site_url  = trim($_POST['site_url'] ?? 'http://localhost/tonyhoaivu');
    $db_host   = trim($_POST['db_host'] ?? 'localhost');
    $db_port   = trim($_POST['db_port'] ?? '3306');
    $db_name   = trim($_POST['db_name'] ?? 'tonyhoaivu_db');
    $db_user   = trim($_POST['db_user'] ?? 'root');
    $db_pass   = $_POST['db_pass'] ?? '';

    $admin_user  = trim($_POST['admin_user'] ?? 'admin');
    $admin_email = trim($_POST['admin_email'] ?? 'admin@tonyhoaivu.com');
    $admin_pass  = $_POST['admin_pass'] ?? 'admin123';
    $admin_name  = trim($_POST['admin_name'] ?? 'Tony Hoài Vũ');

    if (empty($site_name) || empty($db_name) || empty($admin_user) || empty($admin_pass) || empty($admin_email)) {
        $error = 'Vui lòng điền đầy đủ các thông tin bắt buộc.';
    } else {
        try {
            // Test connection
            $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$db_name}`");

            // Execute SQL schema file
            $sql_file = CMS_ROOT . '/database.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                $pdo->exec($sql);
            }

            // Update admin user credentials
            $hash = password_hash($admin_pass, PASSWORD_BCRYPT);
            $up_user = $pdo->prepare("
                UPDATE users 
                SET username = :u, email = :e, password = :p, display_name = :dn 
                WHERE id = 1
            ");
            $up_user->execute([
                'u'  => $admin_user,
                'e'  => $admin_email,
                'p'  => $hash,
                'dn' => $admin_name
            ]);

            // Update settings
            $st = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = :v2");
            $st->execute(['k' => 'site_title', 'v' => $site_name, 'v2' => $site_name]);
            $st->execute(['k' => 'site_url', 'v' => $site_url, 'v2' => $site_url]);
            $st->execute(['k' => 'admin_email', 'v' => $admin_email, 'v2' => $admin_email]);

            // Write config.php
            $config_content = "<?php\n"
                . "// TonyHoaiVu CMS - Generated Configuration\n"
                . "if (!defined('CMS_ROOT')) define('CMS_ROOT', dirname(__DIR__));\n\n"
                . "define('DB_HOST', " . var_export($db_host, true) . ");\n"
                . "define('DB_PORT', " . var_export($db_port, true) . ");\n"
                . "define('DB_NAME', " . var_export($db_name, true) . ");\n"
                . "define('DB_USER', " . var_export($db_user, true) . ");\n"
                . "define('DB_PASS', " . var_export($db_pass, true) . ");\n"
                . "define('DB_CHARSET', 'utf8mb4');\n\n"
                . "define('BASE_URL', " . var_export(rtrim($site_url, '/'), true) . ");\n"
                . "define('ADMIN_URL', BASE_URL . '/admin');\n"
                . "define('UPLOAD_DIR', CMS_ROOT . '/uploads');\n"
                . "define('UPLOAD_URL', BASE_URL . '/uploads');\n"
                . "define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);\n\n"
                . "date_default_timezone_set('Asia/Ho_Chi_Minh');\n"
                . "define('APP_ENV', 'production');\n"
                . "error_reporting(0);\n"
                . "ini_set('display_errors', '0');\n\n"
                . "if (session_status() === PHP_SESSION_NONE) {\n"
                . "    ini_set('session.cookie_httponly', '1');\n"
                . "    session_start();\n"
                . "}\n";

            file_put_contents(CMS_ROOT . '/config/config.php', $config_content);

            // Create install.lock file to prevent re-running installer
            file_put_contents(CMS_ROOT . '/install.lock', 'Installed on ' . date('c'));

            // Redirect to step 3 (Success)
            header('Location: install.php?step=3');
            exit;

        } catch (Exception $e) {
            $error = 'Lỗi kết nối hoặc cài đặt MySQL: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt website &lsaquo; TonyHoaiVu CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .install-box { max-width: 680px; margin: 40px auto; background: #fff; padding: 36px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .install-steps { display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; }
        .step-pill { font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 8px; color: #64748b; background: #f1f5f9; }
        .step-pill.active { background: #1d2327; color: #fff; }
        .step-pill.done { background: #10b981; color: #fff; }
        .req-list { list-style: none; padding: 0; }
        .req-list li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #e2e8f0; font-size: 13px; }
        .badge-ok { color: #10b981; font-weight: 600; }
        .badge-fail { color: #ef4444; font-weight: 600; }
    </style>
</head>
<body class="wp-core-ui bg-slate-50">
    <div class="install-box">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-900">TonyHoaiVu CMS &#8212; Trình cài đặt</h1>
            <p class="text-slate-500 text-sm">Hệ thống quản trị nội dung độc lập chuẩn WordPress PHP &amp; MySQL</p>
        </div>

        <div class="install-steps">
            <span class="step-pill <?= $step === 1 ? 'active' : ($step > 1 ? 'done' : '') ?>">1. Kiểm tra môi trường</span>
            <span class="step-pill <?= $step === 2 ? 'active' : ($step > 2 ? 'done' : '') ?>">2. Cơ sở dữ liệu &amp; Quản trị</span>
            <span class="step-pill <?= $step === 3 ? 'active' : '' ?>">3. Hoàn tất</span>
        </div>

        <?php if (!empty($error)): ?>
        <div class="notice notice-error mb-4"><p><?= htmlspecialchars($error) ?></p></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
        <!-- Step 1: Environment Checks -->
        <div>
            <h2 class="text-lg font-bold mb-3">Kiểm tra yêu cầu hệ thống máy chủ</h2>
            <ul class="req-list mb-6">
                <li>
                    <span>Phiên bản PHP (Yêu cầu &ge; 8.1.0): Hiện tại PHP <?= PHP_VERSION ?></span>
                    <span class="<?= $php_version_ok ? 'badge-ok' : 'badge-fail' ?>"><?= $php_version_ok ? '✓ Đạt yêu cầu' : '✗ Cần nâng cấp' ?></span>
                </li>
                <li>
                    <span>PHP Extension PDO &amp; PDO_MySQL</span>
                    <span class="<?= $pdo_ok ? 'badge-ok' : 'badge-fail' ?>"><?= $pdo_ok ? '✓ Đã kích hoạt' : '✗ Chưa bật' ?></span>
                </li>
                <li>
                    <span>Quyền ghi tệp tin cấu hình (config/config.php)</span>
                    <span class="<?= $config_writable ? 'badge-ok' : 'badge-fail' ?>"><?= $config_writable ? '✓ Có thể ghi' : '✗ Kiểm tra chmod 755' ?></span>
                </li>
                <li>
                    <span>Quyền ghi thư mục tệp tin tải lên (uploads/)</span>
                    <span class="<?= $uploads_writable ? 'badge-ok' : 'badge-fail' ?>"><?= $uploads_writable ? '✓ Có thể ghi' : '✗ Kiểm tra chmod 755' ?></span>
                </li>
            </ul>

            <div class="text-right">
                <a href="install.php?step=2" class="button button-primary button-large">Tiếp tục bước cấu hình &rarr;</a>
            </div>
        </div>

        <?php elseif ($step === 2): ?>
        <!-- Step 2: Database & Admin Configuration Form -->
        <form method="POST" action="">
            <h2 class="text-lg font-bold mb-4">Thông tin kết nối MySQL &amp; Tài khoản</h2>
            
            <h3 class="font-bold text-sm text-slate-700 border-b pb-2 mb-3">1. Cơ sở dữ liệu (MySQL)</h3>
            <table class="form-table mb-4">
                <tr>
                    <th scope="row"><label for="db_host">Database Host</label></th>
                    <td><input type="text" name="db_host" id="db_host" value="localhost" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="db_port">Database Port</label></th>
                    <td><input type="text" name="db_port" id="db_port" value="3306" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="db_name">Tên Database</label></th>
                    <td><input type="text" name="db_name" id="db_name" value="tonyhoaivu_db" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="db_user">Tên người dùng MySQL</label></th>
                    <td><input type="text" name="db_user" id="db_user" value="root" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="db_pass">Mật khẩu MySQL</label></th>
                    <td><input type="password" name="db_pass" id="db_pass" value="" class="regular-text"></td>
                </tr>
            </table>

            <h3 class="font-bold text-sm text-slate-700 border-b pb-2 mb-3">2. Thông tin website &amp; Quản trị viên</h3>
            <table class="form-table mb-4">
                <tr>
                    <th scope="row"><label for="site_name">Tên Website</label></th>
                    <td><input type="text" name="site_name" id="site_name" value="TonyHoaiVu" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="site_url">Địa chỉ Website (URL)</label></th>
                    <td><input type="url" name="site_url" id="site_url" value="http://localhost/tonyhoaivu" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="admin_name">Tên hiển thị Quản trị</label></th>
                    <td><input type="text" name="admin_name" id="admin_name" value="Tony Hoài Vũ" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="admin_user">Tên đăng nhập Admin</label></th>
                    <td><input type="text" name="admin_user" id="admin_user" value="admin" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="admin_email">Email Quản trị</label></th>
                    <td><input type="email" name="admin_email" id="admin_email" value="admin@tonyhoaivu.com" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="admin_pass">Mật khẩu Admin</label></th>
                    <td><input type="password" name="admin_pass" id="admin_pass" value="admin123" class="regular-text" required></td>
                </tr>
            </table>

            <p class="submit text-right">
                <input type="submit" name="run_install" class="button button-primary button-hero" value="Cài đặt CMS ngay bây giờ">
            </p>
        </form>

        <?php elseif ($step === 3): ?>
        <!-- Step 3: Success -->
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">✓</div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Chúc mừng! Cài đặt thành công!</h2>
            <p class="text-slate-600 text-sm mb-6 max-w-md mx-auto">
                TonyHoaiVu CMS đã được khởi tạo xong các bảng cơ sở dữ liệu và cấu hình quản trị. Tệp tin bảo vệ <code>install.lock</code> đã được tạo tự động để ngăn chặn truy cập trái phép.
            </p>
            <div class="flex justify-center gap-4">
                <a href="admin/login.php" class="button button-primary button-hero">Đăng nhập Quản trị &rarr;</a>
                <a href="index.php" class="button button-large">Xem trang chủ Blog</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
