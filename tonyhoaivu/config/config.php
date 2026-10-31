<?php
/**
 * TonyHoaiVu CMS - Main Configuration File
 * Author: Tony Hoai Vu
 * License: Proprietary / MIT
 */

// Prevent direct execution outside app
if (!defined('CMS_ROOT')) {
    define('CMS_ROOT', dirname(__DIR__));
}

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'tonyhoaivu_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Auto-detect Base URL if not explicitly defined
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // If inside /admin, strip /admin
    $scriptDir = preg_replace('/\/admin.*$/', '', $scriptDir);
    $scriptDir = rtrim($scriptDir, '/');
    define('BASE_URL', $protocol . $host . $scriptDir);
}

// Admin URL
define('ADMIN_URL', BASE_URL . '/admin');

// Upload directory settings
define('UPLOAD_DIR', CMS_ROOT . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Timezone and Locale
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Environment & Error reporting (Change to 0 in production)
define('APP_ENV', 'development');
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Start Secure Session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}
