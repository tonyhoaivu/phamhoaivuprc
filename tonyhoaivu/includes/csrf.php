<?php
/**
 * TonyHoaiVu CMS - CSRF Protection Library
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

/**
 * Generate or get existing CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate hidden input field for forms
 */
function csrf_field(): string {
    $token = csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate incoming POST request CSRF token
 */
function validate_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('<h3>403 Forbidden - Lỗi xác thực bảo mật (CSRF Token Invalid)</h3><p>Phiên làm việc đã hết hạn hoặc yêu cầu không hợp lệ. Vui lòng quay lại và thử lại.</p>');
        }
    }
    return true;
}
