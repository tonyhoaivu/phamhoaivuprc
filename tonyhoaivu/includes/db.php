<?php
/**
 * TonyHoaiVu CMS - Database Connection (PDO Singleton)
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

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
                // If installer not executed yet, redirect to install.php
                if (!file_exists(CMS_ROOT . '/install.lock') && file_exists(CMS_ROOT . '/install.php')) {
                    header('Location: ' . BASE_URL . '/install.php');
                    exit;
                }
                
                die("<h3>Lỗi kết nối cơ sở dữ liệu MySQL</h3><p>" . htmlspecialchars($e->getMessage()) . "</p><p>Vui lòng kiểm tra lại cấu hình tại <code>config/config.php</code> hoặc chạy <code>install.php</code> để cài đặt.</p>");
            }
        }
        return self::$instance;
    }
}

// Global db helper function
function get_db(): PDO {
    return Database::getInstance();
}
