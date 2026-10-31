<?php
/**
 * TonyHoaiVu CMS - Admin Logout
 */

define('CMS_ROOT', dirname(__DIR__));
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/auth.php';

logout_user();
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
