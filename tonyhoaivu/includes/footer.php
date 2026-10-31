<?php
/**
 * TonyHoaiVu CMS - Frontend Footer Template
 */

if (!defined('CMS_ROOT')) {
    exit('Direct access not permitted.');
}

$site_title = get_setting('site_title', 'TonyHoaiVu');
$site_desc = get_setting('site_description', 'Blog chia sẻ kiến thức phần mềm, kiến trúc hệ thống và giải pháp số.');
$footer_menus = get_menus('footer');
$fb_url = get_setting('facebook_url', '#');
$gh_url = get_setting('github_url', '#');
$li_url = get_setting('linkedin_url', '#');
$tw_url = get_setting('twitter_url', '#');
$copyright = get_setting('footer_copyright', '© ' . date('Y') . ' TonyHoaiVu. All rights reserved.');
?>
<footer class="site-footer bg-slate-900 text-slate-300 border-t border-slate-800 mt-20 pt-16 pb-12 text-sm">
    <div class="container">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
            <!-- Brand & Bio -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-white text-slate-900 flex items-center justify-center font-bold text-sm">T</span>
                    <span class="font-bold text-lg text-white"><?= e($site_title) ?></span>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed max-w-md">
                    <?= e($site_desc) ?>
                </p>
                <div class="flex items-center gap-4 pt-2">
                    <?php if ($gh_url && $gh_url !== '#'): ?>
                    <a href="<?= e($gh_url) ?>" target="_blank" rel="noopener" class="text-slate-400 hover:text-white transition-colors" title="GitHub">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($li_url && $li_url !== '#'): ?>
                    <a href="<?= e($li_url) ?>" target="_blank" rel="noopener" class="text-slate-400 hover:text-white transition-colors" title="LinkedIn">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if ($fb_url && $fb_url !== '#'): ?>
                    <a href="<?= e($fb_url) ?>" target="_blank" rel="noopener" class="text-slate-400 hover:text-white transition-colors" title="Facebook">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Categories Quick Links -->
            <div>
                <h4 class="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Chuyên mục</h4>
                <ul class="space-y-2 text-slate-400 text-xs">
                    <li><a href="<?= BASE_URL ?>/category/lap-trinh" class="hover:text-white transition-colors">Lập trình Web &amp; PHP</a></li>
                    <li><a href="<?= BASE_URL ?>/category/kien-truc-he-thong" class="hover:text-white transition-colors">Kiến trúc hệ thống</a></li>
                    <li><a href="<?= BASE_URL ?>/category/devops-cloud" class="hover:text-white transition-colors">DevOps &amp; Cloud Computing</a></li>
                    <li><a href="<?= BASE_URL ?>/category/goc-suy-ngam" class="hover:text-white transition-colors">Góc suy ngẫm &amp; Đời thường</a></li>
                </ul>
            </div>

            <!-- Fast Links & Admin -->
            <div>
                <h4 class="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Liên kết</h4>
                <ul class="space-y-2 text-slate-400 text-xs">
                    <li><a href="<?= BASE_URL ?>/page/gioi-thieu" class="hover:text-white transition-colors">Về tác giả Tony Hoài Vũ</a></li>
                    <li><a href="<?= BASE_URL ?>/contact.php" class="hover:text-white transition-colors">Liên hệ hợp tác</a></li>
                    <li><a href="<?= BASE_URL ?>/page/chinh-sach-bao-mat" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    <li><a href="<?= ADMIN_URL ?>/" class="text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">Quản trị viên (CMS) &rarr;</a></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p><?= e($copyright) ?></p>
            <p class="text-slate-400">Thiết kế chuẩn WordPress-like kiến trúc PHP &amp; MySQL độc lập.</p>
        </div>
    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
