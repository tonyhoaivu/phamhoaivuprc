<?php
/**
 * TonyHoaiVu CMS - Admin Sidebar Navigation
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

$current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');
$db = get_db();

// Count pending comments
$pending_stmt = $db->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
$pending_comments_count = (int)$pending_stmt->fetchColumn();

// Helper to determine active state
function is_active_menu(array $pages, string $current): string {
    return in_array($current, $pages, true) ? 'current wp-has-current-submenu' : '';
}
?>
<aside id="adminmenumain" class="wp-sidebar">
    <nav class="wp-menu">
        <!-- Dashboard -->
        <div class="menu-item <?= is_active_menu(['index.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/index.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="wp-menu-name">Bảng tin</span>
            </a>
        </div>

        <div class="wp-menu-separator"></div>

        <!-- Posts -->
        <div class="menu-item wp-has-submenu <?= is_active_menu(['posts.php', 'post-new.php', 'post-edit.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/posts.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span class="wp-menu-name">Bài viết</span>
            </a>
            <div class="wp-submenu">
                <a href="<?= ADMIN_URL ?>/posts.php" class="<?= $current_script === 'posts.php' ? 'current' : '' ?>">Tất cả bài viết</a>
                <a href="<?= ADMIN_URL ?>/post-new.php" class="<?= $current_script === 'post-new.php' ? 'current' : '' ?>">Viết bài mới</a>
                <a href="<?= ADMIN_URL ?>/categories.php" class="<?= $current_script === 'categories.php' ? 'current' : '' ?>">Chuyên mục</a>
            </div>
        </div>

        <!-- Categories Direct link -->
        <div class="menu-item <?= is_active_menu(['categories.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/categories.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span class="wp-menu-name">Chuyên mục</span>
            </a>
        </div>

        <!-- Media Library -->
        <div class="menu-item <?= is_active_menu(['media.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/media.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="wp-menu-name">Media thư viện</span>
            </a>
        </div>

        <!-- Pages -->
        <div class="menu-item <?= is_active_menu(['pages.php', 'page-new.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/pages.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="wp-menu-name">Trang tĩnh</span>
            </a>
        </div>

        <!-- Comments -->
        <div class="menu-item <?= is_active_menu(['comments.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/comments.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="wp-menu-name">Bình luận</span>
                <?php if ($pending_comments_count > 0): ?>
                <span class="update-plugins count-<?= $pending_comments_count ?>"><span class="plugin-count"><?= $pending_comments_count ?></span></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="wp-menu-separator"></div>

        <!-- Menus navigation -->
        <div class="menu-item <?= is_active_menu(['menus.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/menus.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                <span class="wp-menu-name">Menu điều hướng</span>
            </a>
        </div>

        <!-- Users -->
        <div class="menu-item <?= is_active_menu(['users.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/users.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="wp-menu-name">Thành viên</span>
            </a>
        </div>

        <!-- Settings -->
        <div class="menu-item <?= is_active_menu(['settings.php'], $current_script) ?>">
            <a href="<?= ADMIN_URL ?>/settings.php" class="menu-top">
                <svg class="wp-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="wp-menu-name">Cài đặt</span>
            </a>
        </div>
    </nav>
</aside>
