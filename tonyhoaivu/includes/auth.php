<?php
/**
 * TonyHoaiVu CMS - Authentication & Role-Based Access Control
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

require_once CMS_ROOT . '/includes/db.php';

/**
 * Check if current visitor is logged in as a user
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['username']);
}

/**
 * Get currently authenticated user data
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    static $user = null;
    if ($user === null) {
        $db = get_db();
        $stmt = $db->prepare("SELECT id, username, email, display_name, role, avatar, bio FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

/**
 * Check if user has administrator role
 */
function is_admin(): bool {
    $user = current_user();
    return $user && ($user['role'] === 'administrator');
}

/**
 * Require user to be logged in to view page
 */
function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Require administrator role
 */
function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        die("<h3>Quyền truy cập bị từ chối</h3><p>Tài khoản của bạn chỉ có quyền Biên tập viên (Editor), không có quyền thực hiện thao tác Quản trị viên (Administrator).</p><p><a href='" . BASE_URL . "/admin/'>Quay lại Dashboard</a></p>");
    }
}

/**
 * Attempt login with username/email and password
 */
function login_user(string $login, string $password): array {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :login OR email = :email LIMIT 1");
    $stmt->execute(['login' => $login, 'email' => $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['role'] = $user['role'];
        return ['success' => true, 'user' => $user];
    }

    return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'];
}

/**
 * Logout user cleanly
 */
function logout_user(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
