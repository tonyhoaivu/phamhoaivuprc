<?php
/**
 * TonyHoaiVu CMS - Admin Header Template
 * WordPress-inspired administrative layout
 */

if (!defined('CMS_ROOT')) {
    define('CMS_ROOT', dirname(__DIR__));
}

require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/csrf.php';
require_once CMS_ROOT . '/includes/functions.php';

require_login();
$current_user = current_user();
$site_title = get_setting('site_title', 'TonyHoaiVu');
$flashes = get_flashes();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($admin_page_title) ? e($admin_page_title) . ' &lsaquo; ' . e($site_title) . ' &#8212; Quản trị' : e($site_title) . ' &#8212; Bảng tin quản trị' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="wp-admin-body antialiased">

<!-- WordPress-style Top Admin Bar (#wpadminbar) -->
<header id="wpadminbar" class="wp-admin-bar">
    <div class="wp-bar-left">
        <a href="<?= ADMIN_URL ?>/" class="wp-logo-link" title="Bảng điều khiển CMS">
            <span class="wp-logo-badge">W</span>
            <span class="site-name"><?= e($site_title) ?></span>
        </a>
        <a href="<?= BASE_URL ?>/" target="_blank" class="wp-visit-site" title="Xem trang web ngoài giao diện">
            <svg class="icon-site" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            <span>Xem website</span>
        </a>
        <div class="wp-bar-new">
            <a href="<?= ADMIN_URL ?>/post-new.php" class="wp-new-btn">
                <span>+ Mới</span>
            </a>
            <div class="wp-new-dropdown">
                <a href="<?= ADMIN_URL ?>/post-new.php">Bài viết</a>
                <a href="<?= ADMIN_URL ?>/page-new.php">Trang</a>
                <a href="<?= ADMIN_URL ?>/media.php">Media</a>
                <a href="<?= ADMIN_URL ?>/users.php">Thành viên</a>
            </div>
        </div>
    </div>

    <div class="wp-bar-right">
        <div class="wp-user-menu">
            <span class="greeting">Chào, <strong><?= e($current_user['display_name']) ?></strong></span>
            <img src="<?= e($current_user['avatar'] ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60') ?>" alt="" class="wp-avatar" referrerpolicy="no-referrer">
            <div class="wp-user-dropdown">
                <div class="user-meta">
                    <strong><?= e($current_user['display_name']) ?></strong>
                    <span class="role"><?= $current_user['role'] === 'administrator' ? 'Quản trị viên (Admin)' : 'Biên tập viên (Editor)' ?></span>
                </div>
                <hr>
                <a href="<?= ADMIN_URL ?>/users.php?edit=<?= $current_user['id'] ?>">Hồ sơ của tôi</a>
                <a href="<?= ADMIN_URL ?>/logout.php" class="text-danger">Đăng xuất</a>
            </div>
        </div>
    </div>
</header>

<div class="wp-admin-wrap">
    <!-- WordPress Left Sidebar Navigation -->
    <?php require_once CMS_ROOT . '/admin/sidebar.php'; ?>

    <!-- Main Workspace -->
    <main id="wpbody-content" class="wp-body-content">
        <?php if (!empty($flashes)): ?>
        <div class="wp-flashes-wrap">
            <?php foreach ($flashes as $f): ?>
            <div class="notice notice-<?= e($f['type']) ?> is-dismissible">
                <p><?= e($f['message']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
