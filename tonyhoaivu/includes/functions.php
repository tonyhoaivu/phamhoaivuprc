<?php
/**
 * TonyHoaiVu CMS - Core Helper Functions
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

require_once CMS_ROOT . '/includes/db.php';

/**
 * Escapes string for safe HTML output
 */
function e(?string $string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get setting value from database
 */
function get_setting(string $key, string $default = ''): string {
    static $cache = [];
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    
    $db = get_db();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :k LIMIT 1");
    $stmt->execute(['k' => $key]);
    $val = $stmt->fetchColumn();
    $res = ($val !== false) ? (string)$val : $default;
    $cache[$key] = $res;
    return $res;
}

/**
 * Update or insert setting value
 */
function set_setting(string $key, string $value): bool {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = :v2");
    return $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
}

/**
 * Converts Vietnamese unicode string to clean URL slug
 */
function slugify(string $text): string {
    // Vietnamese accents map
    $accents_regex = [
        'a' => '/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u',
        'e' => '/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u',
        'i' => '/(ì|í|ị|ỉ|ĩ)/u',
        'o' => '/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u',
        'u' => '/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u',
        'y' => '/(ỳ|ý|ỵ|ỷ|ỹ)/u',
        'd' => '/(đ)/u',
        'A' => '/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/u',
        'E' => '/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/u',
        'I' => '/(Ì|Í|Ị|Ỉ|Ĩ)/u',
        'O' => '/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/u',
        'U' => '/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/u',
        'Y' => '/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/u',
        'D' => '/(Đ)/u',
    ];

    foreach ($accents_regex as $replacement => $pattern) {
        $text = preg_replace($pattern, $replacement, $text);
    }

    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    return empty($text) ? 'n-a-' . time() : $text;
}

/**
 * Format readable date for Vietnamese readers
 */
function format_date(string $datetime): string {
    $timestamp = strtotime($datetime);
    if (!$timestamp) return '';
    return date('d/m/Y', $timestamp);
}

/**
 * Calculate estimated reading time
 */
function reading_time(string $content): int {
    $word_count = str_word_count(strip_tags($content));
    $minutes = ceil($word_count / 200);
    return max(1, (int)$minutes);
}

/**
 * Record view for post with 1-hour IP debounce
 */
function record_post_view(int $post_id): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $db = get_db();

    // Check if viewed within last 1 hour
    $stmt = $db->prepare("SELECT id FROM post_views WHERE post_id = :p AND ip_address = :ip AND viewed_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) LIMIT 1");
    $stmt->execute(['p' => $post_id, 'ip' => $ip]);
    
    if (!$stmt->fetch()) {
        $insert = $db->prepare("INSERT INTO post_views (post_id, ip_address) VALUES (:p, :ip)");
        $insert->execute(['p' => $post_id, 'ip' => $ip]);

        $update = $db->prepare("UPDATE posts SET views_count = views_count + 1 WHERE id = :p");
        $update->execute(['p' => $post_id]);
    }
}

/**
 * Flash messaging system (success, error, warning)
 */
function set_flash(string $type, string $message): void {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/**
 * Safe file upload handler
 */
function handle_file_upload(array $file, int $uploader_id): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Lỗi tải file lên (Mã: ' . $file['error'] . ')'];
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'error' => 'Dung lượng file vượt quá 5MB.'];
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $allowed_mimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'
    ];

    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_extensions, true)) {
        return ['success' => false, 'error' => 'Định dạng file không được phép. Chỉ chấp nhận JPG, PNG, GIF, WEBP, SVG.'];
    }

    // Verify MIME using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_mimes, true)) {
        return ['success' => false, 'error' => 'MIME type không hợp lệ (' . $mime . ').'];
    }

    // Create unique safe filename
    $safe_name = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    
    // Ensure upload directory exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $destination = UPLOAD_DIR . '/' . $safe_name;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Không thể lưu file vào thư mục uploads. Kiểm tra phân quyền thư mục.'];
    }

    // Insert record into media table
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO media (uploader_id, filename, original_name, file_path, file_size, file_type) VALUES (:uid, :fn, :orig, :fp, :fs, :ft)");
    $stmt->execute([
        'uid' => $uploader_id,
        'fn'  => $safe_name,
        'orig'=> $filename,
        'fp'  => 'uploads/' . $safe_name,
        'fs'  => $file['size'],
        'ft'  => $mime
    ]);

    return [
        'success' => true,
        'file_url' => UPLOAD_URL . '/' . $safe_name,
        'media_id' => (int)$db->lastInsertId(),
        'filename' => $safe_name
    ];
}

/**
 * Get active header menus
 */
function get_menus(string $position = 'header'): array {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM menus WHERE position = :pos ORDER BY sort_order ASC, id ASC");
    $stmt->execute(['pos' => $position]);
    return $stmt->fetchAll();
}
