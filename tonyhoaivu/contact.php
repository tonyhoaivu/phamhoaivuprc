<?php
/**
 * TonyHoaiVu CMS - Contact Page
 */

define('CMS_ROOT', __DIR__);
require_once CMS_ROOT . '/config/config.php';
require_once CMS_ROOT . '/includes/db.php';
require_once CMS_ROOT . '/includes/auth.php';
require_once CMS_ROOT . '/includes/csrf.php';
require_once CMS_ROOT . '/includes/functions.php';

$page_title = 'Liên hệ';
$page_desc = 'Gửi tin nhắn hoặc liên hệ hợp tác với Tony Hoài Vũ.';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_msg = 'Vui lòng điền đầy đủ tất cả các trường thông tin.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Địa chỉ email không đúng định dạng.';
    } else {
        // Here we could store in database or send via mail() / SMTP
        $success_msg = 'Cảm ơn bạn! Tin nhắn đã được gửi thành công. Tôi sẽ phản hồi sớm nhất qua email ' . htmlspecialchars($email) . '.';
    }
}

require_once CMS_ROOT . '/includes/header.php';
?>

<main class="container py-12">
    <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-8 sm:p-14 shadow-sm">
        <div class="mb-10 text-center max-w-xl mx-auto">
            <span class="text-xs uppercase tracking-widest text-accent font-semibold block mb-1">Kết nối</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">Liên hệ hợp tác</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Bạn có câu hỏi, đề xuất hợp tác kỹ thuật hoặc muốn trao đổi về dự án phần mềm? Hãy gửi tin nhắn cho tôi qua biểu mẫu bên dưới.
            </p>
        </div>

        <?php if ($success_msg): ?>
        <div class="p-4 mb-8 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-xs sm:text-sm">
            <?= e($success_msg) ?>
        </div>
        <?php endif; ?>

        <?php if ($error_msg): ?>
        <div class="p-4 mb-8 bg-red-50 dark:bg-red-950/50 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800 rounded-2xl text-xs sm:text-sm">
            <?= e($error_msg) ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Họ và tên *</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Địa chỉ Email *</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="email@domain.com">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Chủ đề tin nhắn *</label>
                <input type="text" name="subject" required class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Tư vấn kiến trúc hệ thống / Hợp tác bài viết...">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Nội dung chi tiết *</label>
                <textarea name="message" rows="6" required class="w-full px-4 py-3 text-xs sm:text-sm rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Viết tin nhắn của bạn ở đây..."></textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs sm:text-sm rounded-xl hover:opacity-90 transition-all shadow-md">
                    Gửi tin nhắn liên hệ
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once CMS_ROOT . '/includes/footer.php'; ?>
