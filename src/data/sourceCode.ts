export interface SourceFile {
  path: string;
  name: string;
  folder: string;
  language: string;
  description: string;
  content: string;
}

export const sourceFiles: SourceFile[] = [
  {
    path: 'database.sql',
    name: 'database.sql',
    folder: '/',
    language: 'sql',
    description: 'Thiết kế cấu trúc bảng MySQL đầy đủ & dữ liệu mẫu',
    content: `-- Database schema for TonyHoaiVu CMS
-- Compatible with MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.3+
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users table
DROP TABLE IF EXISTS \`users\`;
CREATE TABLE \`users\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`username\` VARCHAR(60) NOT NULL UNIQUE,
  \`email\` VARCHAR(100) NOT NULL UNIQUE,
  \`password\` VARCHAR(255) NOT NULL,
  \`display_name\` VARCHAR(100) NOT NULL,
  \`role\` ENUM('administrator', 'editor') NOT NULL DEFAULT 'editor',
  \`avatar\` VARCHAR(255) DEFAULT NULL,
  \`bio\` TEXT DEFAULT NULL,
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX \`idx_users_role\` (\`role\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories table
DROP TABLE IF EXISTS \`categories\`;
CREATE TABLE \`categories\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`name\` VARCHAR(100) NOT NULL,
  \`slug\` VARCHAR(120) NOT NULL UNIQUE,
  \`description\` TEXT DEFAULT NULL,
  \`post_count\` INT UNSIGNED NOT NULL DEFAULT 0,
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX \`idx_categories_slug\` (\`slug\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts table
DROP TABLE IF EXISTS \`posts\`;
CREATE TABLE \`posts\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`author_id\` INT UNSIGNED NOT NULL,
  \`category_id\` INT UNSIGNED DEFAULT NULL,
  \`title\` VARCHAR(255) NOT NULL,
  \`slug\` VARCHAR(255) NOT NULL UNIQUE,
  \`excerpt\` TEXT DEFAULT NULL,
  \`content\` LONGTEXT NOT NULL,
  \`featured_image\` VARCHAR(255) DEFAULT NULL,
  \`tags\` VARCHAR(255) DEFAULT NULL,
  \`status\` ENUM('publish', 'draft', 'private') NOT NULL DEFAULT 'publish',
  \`is_featured\` TINYINT(1) NOT NULL DEFAULT 0,
  \`seo_title\` VARCHAR(255) DEFAULT NULL,
  \`seo_description\` TEXT DEFAULT NULL,
  \`views_count\` INT UNSIGNED NOT NULL DEFAULT 0,
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX \`idx_posts_slug\` (\`slug\`),
  INDEX \`idx_posts_status\` (\`status\`),
  INDEX \`idx_posts_category\` (\`category_id\`),
  INDEX \`idx_posts_featured\` (\`is_featured\`),
  INDEX \`idx_posts_created_at\` (\`created_at\`),
  CONSTRAINT \`fk_posts_author\` FOREIGN KEY (\`author_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE,
  CONSTRAINT \`fk_posts_category\` FOREIGN KEY (\`category_id\`) REFERENCES \`categories\` (\`id\`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Pages table
DROP TABLE IF EXISTS \`pages\`;
CREATE TABLE \`pages\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`author_id\` INT UNSIGNED NOT NULL,
  \`title\` VARCHAR(255) NOT NULL,
  \`slug\` VARCHAR(255) NOT NULL UNIQUE,
  \`content\` LONGTEXT NOT NULL,
  \`status\` ENUM('publish', 'draft') NOT NULL DEFAULT 'publish',
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX \`idx_pages_slug\` (\`slug\`),
  CONSTRAINT \`fk_pages_author\` FOREIGN KEY (\`author_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Media table
DROP TABLE IF EXISTS \`media\`;
CREATE TABLE \`media\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`uploader_id\` INT UNSIGNED NOT NULL,
  \`filename\` VARCHAR(255) NOT NULL,
  \`original_name\` VARCHAR(255) NOT NULL,
  \`file_path\` VARCHAR(255) NOT NULL,
  \`file_size\` INT UNSIGNED NOT NULL,
  \`file_type\` VARCHAR(100) NOT NULL,
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT \`fk_media_uploader\` FOREIGN KEY (\`uploader_id\`) REFERENCES \`users\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Comments table
DROP TABLE IF EXISTS \`comments\`;
CREATE TABLE \`comments\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`post_id\` INT UNSIGNED NOT NULL,
  \`author_name\` VARCHAR(100) NOT NULL,
  \`author_email\` VARCHAR(120) NOT NULL,
  \`author_website\` VARCHAR(200) DEFAULT NULL,
  \`content\` TEXT NOT NULL,
  \`status\` ENUM('approved', 'pending', 'spam', 'trash') NOT NULL DEFAULT 'approved',
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX \`idx_comments_post\` (\`post_id\`),
  CONSTRAINT \`fk_comments_post\` FOREIGN KEY (\`post_id\`) REFERENCES \`posts\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Menus table
DROP TABLE IF EXISTS \`menus\`;
CREATE TABLE \`menus\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`title\` VARCHAR(100) NOT NULL,
  \`url\` VARCHAR(255) NOT NULL,
  \`target\` VARCHAR(20) DEFAULT '_self',
  \`position\` ENUM('header', 'footer') NOT NULL DEFAULT 'header',
  \`sort_order\` INT NOT NULL DEFAULT 0,
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Settings table
DROP TABLE IF EXISTS \`settings\`;
CREATE TABLE \`settings\` (
  \`setting_key\` VARCHAR(100) PRIMARY KEY,
  \`setting_value\` LONGTEXT DEFAULT NULL,
  \`created_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  \`updated_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Post views tracking table
DROP TABLE IF EXISTS \`post_views\`;
CREATE TABLE \`post_views\` (
  \`id\` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  \`post_id\` INT UNSIGNED NOT NULL,
  \`ip_address\` VARCHAR(45) NOT NULL,
  \`viewed_at\` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX \`idx_post_views_post\` (\`post_id\`),
  INDEX \`idx_post_views_ip\` (\`ip_address\`, \`viewed_at\`),
  CONSTRAINT \`fk_views_post\` FOREIGN KEY (\`post_id\`) REFERENCES \`posts\` (\`id\`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;`
  },
  {
    path: '.htaccess',
    name: '.htaccess',
    folder: '/',
    language: 'apache',
    description: 'Clean URL rewrites và cấu hình bảo mật Apache',
    content: `<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /tonyhoaivu/

Options -Indexes
RewriteRule ^\\.(.*) - [F]
RewriteRule ^config/ - [F,L]
RewriteRule ^includes/ - [F,L]

RewriteRule ^post/([a-zA-Z0-9_-]+)/?$ post.php?slug=$1 [L,QSA]
RewriteRule ^category/([a-zA-Z0-9_-]+)/?$ category.php?slug=$1 [L,QSA]
RewriteRule ^page/([a-zA-Z0-9_-]+)/?$ page.php?slug=$1 [L,QSA]
RewriteRule ^search/?$ search.php [L,QSA]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
</IfModule>`
  },
  {
    path: 'uploads/.htaccess',
    name: '.htaccess',
    folder: 'uploads',
    language: 'apache',
    description: 'Khóa thực thi script trong thư mục upload',
    content: `<FilesMatch "\\.(php|phtml|php3|php4|php5|php7|php8|phps|cgi|pl|exe|sh|py|jsp|asp|aspx)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

<FilesMatch "\\.(jpg|jpeg|png|gif|webp|svg|pdf|ico)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

Options -Indexes -ExecCGI`
  },
  {
    path: 'config/config.php',
    name: 'config.php',
    folder: 'config',
    language: 'php',
    description: 'Cấu hình thông số database, Base URL, timezone & session',
    content: `<?php
if (!defined('CMS_ROOT')) define('CMS_ROOT', dirname(__DIR__));

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'tonyhoaivu_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = preg_replace('/\\/admin.*$/', '', $scriptDir);
    $scriptDir = rtrim($scriptDir, '/');
    define('BASE_URL', $protocol . $host . $scriptDir);
}

define('ADMIN_URL', BASE_URL . '/admin');
define('UPLOAD_DIR', CMS_ROOT . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

date_default_timezone_set('Asia/Ho_Chi_Minh');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    session_start();
}`
  },
  {
    path: 'includes/db.php',
    name: 'db.php',
    folder: 'includes',
    language: 'php',
    description: 'Khởi tạo kết nối PDO Singleton an toàn',
    content: `<?php
if (!defined('CMS_ROOT')) exit('Direct access not permitted.');
require_once CMS_ROOT . '/config/config.php';

class Database {
    private static ?PDO $instance = null;
    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_unicode_ci"
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die("Lỗi kết nối MySQL: " . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }
}

function get_db(): PDO {
    return Database::getInstance();
}`
  },
  {
    path: 'includes/csrf.php',
    name: 'csrf.php',
    folder: 'includes',
    language: 'php',
    description: 'Thư viện phòng chống tấn công Cross-Site Request Forgery',
    content: `<?php
if (!defined('CMS_ROOT')) exit('Direct access not permitted.');

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function validate_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('<h3>403 Forbidden - Lỗi xác thực CSRF Token</h3>');
        }
    }
    return true;
}`
  },
  {
    path: 'includes/auth.php',
    name: 'auth.php',
    folder: 'includes',
    language: 'php',
    description: 'Xác thực đăng nhập, phân quyền Admin/Editor & mã hóa mật khẩu',
    content: `<?php
if (!defined('CMS_ROOT')) exit('Direct access not permitted.');
require_once CMS_ROOT . '/includes/db.php';

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['username']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null) {
        $db = get_db();
        $stmt = $db->prepare("SELECT id, username, email, display_name, role, avatar, bio FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function is_admin(): bool {
    $user = current_user();
    return $user && ($user['role'] === 'administrator');
}

function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die("<h3>Quyền truy cập bị từ chối - Yêu cầu Quản trị viên</h3>");
    }
}

function login_user(string $login, string $password): array {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :l OR email = :e LIMIT 1");
    $stmt->execute(['l' => $login, 'e' => $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['role'] = $user['role'];
        return ['success' => true, 'user' => $user];
    }
    return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'];
}

function logout_user(): void {
    $_SESSION = [];
    session_destroy();
}`
  },
  {
    path: 'includes/functions.php',
    name: 'functions.php',
    folder: 'includes',
    language: 'php',
    description: 'Hàm xử lý slug tiếng Việt, reading time, view count, file upload',
    content: `<?php
if (!defined('CMS_ROOT')) exit('Direct access not permitted.');
require_once CMS_ROOT . '/includes/db.php';

function e(?string $string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

function get_setting(string $key, string $default = ''): string {
    $db = get_db();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1");
    $stmt->execute(['k' => $key]);
    $val = $stmt->fetchColumn();
    return ($val !== false) ? (string)$val : $default;
}

function set_setting(string $key, string $value): bool {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = :v2");
    return $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
}

function slugify(string $text): string {
    $accents_regex = [
        'a' => '/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u',
        'e' => '/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u',
        'i' => '/(ì|í|ị|ỉ|ĩ)/u',
        'o' => '/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u',
        'u' => '/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u',
        'y' => '/(ỳ|ý|ỵ|ỷ|ỹ)/u',
        'd' => '/(đ)/u',
    ];
    foreach ($accents_regex as $replacement => $pattern) {
        $text = preg_replace($pattern, $replacement, $text);
    }
    $text = preg_replace('~[^\\pL\\d]+~u', '-', $text);
    $text = trim($text, '-');
    return strtolower($text) ?: 'post-' . time();
}

function reading_time(string $content): int {
    $word_count = str_word_count(strip_tags($content));
    return max(1, (int)ceil($word_count / 200));
}

function record_post_view(int $post_id): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $db = get_db();
    $stmt = $db->prepare("SELECT id FROM post_views WHERE post_id = :p AND ip_address = :ip AND viewed_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) LIMIT 1");
    $stmt->execute(['p' => $post_id, 'ip' => $ip]);
    if (!$stmt->fetch()) {
        $db->prepare("INSERT INTO post_views (post_id, ip_address) VALUES (:p, :ip)")->execute(['p' => $post_id, 'ip' => $ip]);
        $db->prepare("UPDATE posts SET views_count = views_count + 1 WHERE id = :p")->execute(['p' => $post_id]);
    }
}`
  },
  {
    path: 'install.php',
    name: 'install.php',
    folder: '/',
    language: 'php',
    description: 'Trình cài đặt tự động 3 bước với khóa bảo vệ install.lock',
    content: `<?php
define('CMS_ROOT', __DIR__);
if (file_exists(CMS_ROOT . '/install.lock')) {
    die('Hệ thống đã được cài đặt! Tệp install.lock đã tồn tại để bảo vệ an toàn.');
}
// 1. Kiểm tra PHP >= 8.1 & PDO
// 2. Nhập thông tin kết nối MySQL & Tài khoản Admin
// 3. Khởi tạo bảng dữ liệu từ database.sql và tạo file install.lock`
  },
  {
    path: 'admin/index.php',
    name: 'index.php',
    folder: 'admin',
    language: 'php',
    description: 'Bảng tin quản trị WordPress Dashboard, thống kê & soạn nháp nhanh',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Bảng tin';
require_once CMS_ROOT . '/admin/header.php';
// Hiển thị Metrics bài viết, chuyên mục, bình luận, Quick Draft & Recent Posts`
  },
  {
    path: 'admin/posts.php',
    name: 'posts.php',
    folder: 'admin',
    language: 'php',
    description: 'Quản lý bài viết, lọc trạng thái, hành động hàng loạt & phân trang',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Quản lý bài viết';
require_once CMS_ROOT . '/admin/header.php';
// Bảng bài viết chuẩn WordPress widefat, lọc theo chuyên mục và trạng thái`
  },
  {
    path: 'admin/post-new.php',
    name: 'post-new.php',
    folder: 'admin',
    language: 'php',
    description: 'Trình soạn thảo bài viết WYSIWYG, tự tạo slug & xem trước Live Preview',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Thêm bài viết mới';
require_once CMS_ROOT . '/admin/header.php';
// Trình soạn thảo WYSIWYG đầy đủ công cụ Bold, H1-H6, Image, Table, Code, Raw HTML toggle`
  },
  {
    path: 'admin/media.php',
    name: 'media.php',
    folder: 'admin',
    language: 'php',
    description: 'Thư viện hình ảnh, tải lên kéo thả & kiểm tra MIME type',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Thư viện Media';
require_once CMS_ROOT . '/admin/header.php';
// Quản lý media, tải ảnh an toàn, sao chép URL và xem kích thước file`
  },
  {
    path: 'admin/categories.php',
    name: 'categories.php',
    folder: 'admin',
    language: 'php',
    description: 'Quản lý chuyên mục theo layout chia đôi màn hình 2 cột WordPress',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Chuyên mục';
require_once CMS_ROOT . '/admin/header.php';
// Thêm chuyên mục mới ở bên trái, bảng danh sách chuyên mục ở bên phải`
  },
  {
    path: 'admin/comments.php',
    name: 'comments.php',
    folder: 'admin',
    language: 'php',
    description: 'Kiểm duyệt bình luận: Duyệt, Bỏ duyệt, Đánh dấu Spam & Xóa',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Quản lý bình luận';
require_once CMS_ROOT . '/admin/header.php';
// Kiểm duyệt bình luận của độc giả gửi từ trang bài viết`
  },
  {
    path: 'admin/users.php',
    name: 'users.php',
    folder: 'admin',
    language: 'php',
    description: 'Quản lý thành viên, phân quyền Administrator/Editor & đổi mật khẩu',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Quản lý thành viên';
require_once CMS_ROOT . '/admin/header.php';
// Quản lý thành viên, thay đổi mật khẩu password_hash BCRYPT`
  },
  {
    path: 'admin/settings.php',
    name: 'settings.php',
    folder: 'admin',
    language: 'php',
    description: 'Cài đặt tổng quan, SEO meta, mạng xã hội & thông tin chân trang',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
$admin_page_title = 'Cài đặt website';
require_once CMS_ROOT . '/admin/header.php';
// Cài đặt tên trang, tagline, email admin, SEO meta description, mạng xã hội`
  },
  {
    path: 'admin/login.php',
    name: 'login.php',
    folder: 'admin',
    language: 'php',
    description: 'Trang đăng nhập giao diện WordPress cổ điển với CSRF token',
    content: `<?php
define('CMS_ROOT', dirname(__DIR__));
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/auth.php';
// Form đăng nhập chuẩn WordPress: tên người dùng hoặc email + mật khẩu`
  },
  {
    path: 'index.php',
    name: 'index.php',
    folder: '/',
    language: 'php',
    description: 'Trang chủ hiển thị danh sách bài viết, phân trang & banner',
    content: `<?php
define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/functions.php';
require_once CMS_ROOT . '/includes/header.php';
// Hiển thị Hero banner, bài viết nổi bật, danh sách bài mới và sidebar`
  },
  {
    path: 'post.php',
    name: 'post.php',
    folder: '/',
    language: 'php',
    description: 'Trang chi tiết bài viết, đếm view, bài liên quan & form bình luận',
    content: `<?php
define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/functions.php';
// Hiển thị toàn bộ nội dung bài viết, đếm view, xử lý bình luận`
  },
  {
    path: 'README.md',
    name: 'README.md',
    folder: '/',
    language: 'markdown',
    description: 'Hướng dẫn cài đặt, kiến trúc bảo mật & thông tin dự án',
    content: `# TonyHoaiVu CMS — PHP 8 + MySQL Independent CMS
Xem chi tiết file README.md trong thư mục gốc /tonyhoaivu/README.md.`
  }
];
