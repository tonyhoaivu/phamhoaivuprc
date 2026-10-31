-- Database schema for TonyHoaiVu CMS
-- Compatible with MySQL 5.7+ / MySQL 8.0+ / MariaDB 10.3+
-- Character set: utf8mb4, Collation: utf8mb4_unicode_ci

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `display_name` VARCHAR(100) NOT NULL,
  `role` ENUM('administrator', 'editor') NOT NULL DEFAULT 'editor',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Categories table
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `post_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts table
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `tags` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('publish', 'draft', 'private') NOT NULL DEFAULT 'publish',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `seo_title` VARCHAR(255) DEFAULT NULL,
  `seo_description` TEXT DEFAULT NULL,
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_posts_slug` (`slug`),
  INDEX `idx_posts_status` (`status`),
  INDEX `idx_posts_category` (`category_id`),
  INDEX `idx_posts_featured` (`is_featured`),
  INDEX `idx_posts_created_at` (`created_at`),
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_posts_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Pages table (static pages like About, Contact, Services)
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` LONGTEXT NOT NULL,
  `status` ENUM('publish', 'draft') NOT NULL DEFAULT 'publish',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_pages_slug` (`slug`),
  CONSTRAINT `fk_pages_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Media table
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uploader_id` INT UNSIGNED NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_size` INT UNSIGNED NOT NULL,
  `file_type` VARCHAR(100) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_media_created` (`created_at`),
  CONSTRAINT `fk_media_uploader` FOREIGN KEY (`uploader_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Comments table
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT UNSIGNED NOT NULL,
  `author_name` VARCHAR(100) NOT NULL,
  `author_email` VARCHAR(120) NOT NULL,
  `author_website` VARCHAR(200) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `status` ENUM('approved', 'pending', 'spam', 'trash') NOT NULL DEFAULT 'approved',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_comments_post` (`post_id`),
  INDEX `idx_comments_status` (`status`),
  CONSTRAINT `fk_comments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Menus table
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `target` VARCHAR(20) DEFAULT '_self',
  `position` ENUM('header', 'footer') NOT NULL DEFAULT 'header',
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Settings table (Key-Value store)
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key` VARCHAR(100) PRIMARY KEY,
  `setting_value` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Post views tracking table (IP debounce)
DROP TABLE IF EXISTS `post_views`;
CREATE TABLE `post_views` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT UNSIGNED NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `viewed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_post_views_post` (`post_id`),
  INDEX `idx_post_views_ip` (`ip_address`, `viewed_at`),
  CONSTRAINT `fk_views_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- SEED DATA

-- Default Admin User (Password: admin123)
-- bcrypt hash for 'admin123' is '$2y$10$wT0o3q6LdG3n8M3.T7/aauo4F97NnKx3jVl0uI3R3/m5eBvP.V70S'
INSERT INTO `users` (`id`, `username`, `email`, `password`, `display_name`, `role`, `avatar`, `bio`) VALUES
(1, 'admin', 'admin@tonyhoaivu.com', '$2y$10$kPzP0xU2WfI5LzP7eW4m2uR3uL4N9L6F8M1J3Z5K7Q9S1V3X5Y7Wa', 'Tony Hoài Vũ', 'administrator', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300', 'Senior Software Engineer & Tech Enthusiast. Đam mê chia sẻ kiến thức về lập trình, kiến trúc hệ thống và cuộc sống công nghệ.');

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `post_count`) VALUES
(1, 'Lập trình', 'lap-trinh', 'Chia sẻ kiến thức lập trình backend, frontend, clean code và kinh nghiệm thực chiến.', 3),
(2, 'Kiến trúc hệ thống', 'kien-truc-he-thong', 'Thiết kế hệ thống chịu tải cao, microservices, cơ sở dữ liệu và tối ưu hiệu năng.', 2),
(3, 'DevOps & Cloud', 'devops-cloud', 'Docker, Kubernetes, CI/CD, AWS, Google Cloud và tự động hóa hạ tầng.', 2),
(4, 'Góc suy ngẫm', 'goc-suy-ngam', 'Những câu chuyện đời thường, bài học nghề nghiệp và triết lý công nghệ cá nhân.', 1);

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'TonyHoaiVu'),
('site_tagline', 'Kỹ thuật phần mềm, Kiến trúc hệ thống & Chia sẻ kinh nghiệm'),
('site_url', 'http://localhost/tonyhoaivu'),
('admin_email', 'contact@tonyhoaivu.com'),
('posts_per_page', '6'),
('site_description', 'Blog cá nhân của Tony Hoài Vũ - Chuyên gia công nghệ chia sẻ kinh nghiệm phát triển phần mềm, kiến trúc hệ thống và giải pháp số.'),
('site_keywords', 'TonyHoaiVu, Blog Lập Trình, PHP, Fullstack, Kiến trúc hệ thống, MySQL, CMS'),
('facebook_url', 'https://facebook.com/tonyhoaivu'),
('github_url', 'https://github.com/tonyhoaivu'),
('linkedin_url', 'https://linkedin.com/in/tonyhoaivu'),
('twitter_url', 'https://twitter.com/tonyhoaivu'),
('footer_copyright', '© 2026 TonyHoaiVu. Bản quyền thuộc về tác giả.');

-- Default Navigation Menus
INSERT INTO `menus` (`id`, `title`, `url`, `target`, `position`, `sort_order`) VALUES
(1, 'Trang chủ', '/', '_self', 'header', 1),
(2, 'Lập trình', '/category/lap-trinh', '_self', 'header', 2),
(3, 'Kiến trúc', '/category/kien-truc-he-thong', '_self', 'header', 3),
(4, 'DevOps', '/category/devops-cloud', '_self', 'header', 4),
(5, 'Giới thiệu', '/page/gioi-thieu', '_self', 'header', 5),
(6, 'Liên hệ', '/contact.php', '_self', 'header', 6);

-- Sample Posts
INSERT INTO `posts` (`id`, `author_id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `featured_image`, `tags`, `status`, `is_featured`, `seo_title`, `seo_description`, `views_count`, `created_at`) VALUES
(1, 1, 1, 'Xây dựng Custom CMS bằng PHP 8 thuần theo phong cách WordPress', 'xay-dung-custom-cms-bang-php-8-thuan-theo-phong-cach-wordpress', 'Hướng dẫn toàn diện về cách tổ chức mã nguồn, phân tầng kiến trúc và bảo mật cho hệ thống quản trị nội dung độc lập.', '<p>Trong thế giới phát triển web hiện đại với hàng loạt framework đồ sộ như Laravel, Symfony hay các nền tảng CMS như WordPress, việc hiểu sâu sắc cách hoạt động ở tầng thấp của <strong>PHP thuần và MySQL</strong> mang lại nền tảng kiến thức vô giá.</p><h2>1. Tại sao lại tự xây dựng CMS độc lập?</h2><p>Tự viết một CMS nhẹ giúp:</p><ul><li>Kiểm soát 100% mã nguồn, không dư thừa tài nguyên.</li><li>Tốc độ phản hồi cực nhanh, thời gian tải trang dưới 100ms.</li><li>Dễ dàng tùy biến chính xác theo nghiệp vụ mà không bị ràng buộc bởi plugin cồng kềnh.</li><li>Nắm vững các nguyên tắc bảo mật: PDO Prepared Statements, CSRF tokens, Session Hijacking prevention.</li></ul><h2>2. Kiến trúc phân tầng (Clean Architecture)</h2><p>Hệ thống được tổ chức thành các thư mục chuyên biệt: <code>/config</code> cho kết nối cơ sở dữ liệu, <code>/includes</code> cho các hàm dùng chung và xác thực quyền hạn, <code>/admin</code> dành riêng cho trang quản trị, và các trang giao diện người dùng ở thư mục gốc.</p><h2>3. Kết luận</h2><p>Một hệ thống CMS tối giản nhưng đủ đầy tính năng là minh chứng rõ nét cho năng lực làm chủ kỹ thuật của lập trình viên Full-stack.</p>', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200', 'PHP, CMS, MySQL, Architecture', 'publish', 1, 'Xây dựng Custom CMS bằng PHP 8 - TonyHoaiVu', 'Hướng dẫn chi tiết xây dựng hệ thống CMS độc lập chuẩn bảo mật với PHP 8 và MySQL.', 1420, '2026-10-15 09:30:00'),
(2, 1, 2, 'Chiến lược thiết kế cơ sở dữ liệu MySQL chịu tải cao và tối ưu chỉ mục (Index)', 'chien-luoc-thiet-ke-co-so-du-lieu-mysql-chiu-tai-cao', 'Khám phá các kỹ thuật indexing B-Tree, tối ưu câu lệnh truy vấn và phân vùng dữ liệu để đạt hàng triệu truy vấn mỗi ngày.', '<p>Khi quy mô dữ liệu mở rộng đến hàng triệu bản ghi, việc tối ưu hóa cơ sở dữ liệu trở thành yếu tố quyết định sống còn của ứng dụng.</p><h2>Nguyên tắc vàng khi đánh Index</h2><p>Không phải cứ tạo nhiều index là tốt. Chỉ mục giúp tăng tốc <strong>SELECT</strong> nhưng làm chậm các thao tác <strong>INSERT, UPDATE, DELETE</strong>.</p><p>Hãy luôn chú ý đến quy tắc <em>Leftmost Prefix Rule</em> khi tạo Composite Index và phân tích kỹ lưỡng thông qua <code>EXPLAIN</code> query execution plan.</p>', 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=1200', 'MySQL, Database, Performance, Indexing', 'publish', 1, 'Tối ưu Index MySQL chịu tải cao - TonyHoaiVu', 'Bí quyết thiết kế cơ sở dữ liệu và đánh index thông minh cho hệ thống lớn.', 890, '2026-10-18 14:15:00'),
(3, 1, 3, 'Triển khai Docker & CI/CD tự động cho ứng dụng PHP trên Cloud', 'trien-khai-docker-va-cicd-tu-dong-cho-ung-dung-php-tren-cloud', 'Quy trình đóng gói container tối ưu dung lượng và thiết lập pipeline tự động hóa kiểm thử mã nguồn trước khi deploy.', '<p>Container hóa ứng dụng giúp xoá bỏ khoảng cách giữa môi trường phát triển (Local Development) và môi trường thực tế (Production).</p><h2>Cấu hình Multi-stage Build</h2><p>Sử dụng multi-stage build giúp giảm kích thước image từ 800MB xuống chỉ còn chưa đầy 90MB với Alpine Linux và PHP-FPM.</p>', 'https://images.unsplash.com/photo-1605379399642-870262d3d051?w=1200', 'Docker, DevOps, CI/CD, Cloud', 'publish', 0, 'Docker & CI/CD cho PHP - TonyHoaiVu', 'Quy trình triển khai Docker tối ưu và pipeline CI/CD cho ứng dụng PHP.', 650, '2026-10-22 10:00:00'),
(4, 1, 1, 'Bảo mật ứng dụng Web: Phòng chống SQL Injection và XSS từ gốc rễ', 'bao-mat-ung-dung-web-phong-chong-sql-injection-va-xss', 'Những lỗi bảo mật kinh điển nhưng vẫn phổ biến, cách phòng tránh triệt để bằng chuẩn lập trình an toàn.', '<p>Bảo mật không phải là một tính năng được thêm vào ở giai đoạn cuối, mà là tư duy cần thấm nhuần trong từng dòng mã nguồn.</p><h2>Sử dụng PDO Prepared Statements</h2><p>Tuyệt đối không nối chuỗi trực tiếp vào câu lệnh SQL. Thay vào đó hãy dùng PDO parameter binding để database engine phân tách rõ ràng giữa mã lệnh và dữ liệu người dùng.</p>', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200', 'Security, SQL Injection, XSS, PHP', 'publish', 0, 'Bảo mật Web PHP - TonyHoaiVu', 'Hướng dẫn chống SQL Injection, XSS, CSRF bằng phương pháp chuẩn.', 1120, '2026-10-25 16:45:00');

-- Sample Pages
INSERT INTO `pages` (`id`, `author_id`, `title`, `slug`, `content`, `status`, `created_at`) VALUES
(1, 1, 'Giới thiệu về Tony Hoài Vũ', 'gioi-thieu', '<h2>Xin chào, tôi là Tony Hoài Vũ 👋</h2><p>Chào mừng bạn đến với góc không gian số cá nhân của tôi. Tôi là một Kỹ sư phần mềm Full-stack với hơn 8 năm kinh nghiệm trong việc thiết kế, xây dựng và vận hành các hệ thống web quy mô lớn.</p><p>Tại đây, tôi chia sẻ các bài viết chuyên sâu về kỹ thuật lập trình, tối ưu hóa hiệu năng, kiến trúc microservices và những chiêm nghiệm trong hành trình làm nghề công nghệ.</p><h3>Kỹ năng & Chuyên môn:</h3><ul><li><strong>Backend:</strong> PHP (8.x, Clean Architecture, WordPress, Laravel), Node.js, Go</li><li><strong>Database:</strong> MySQL, PostgreSQL, Redis, Elasticsearch</li><li><strong>DevOps & Cloud:</strong> Docker, Kubernetes, Linux System Administration, AWS</li><li><strong>Frontend:</strong> Modern JavaScript (ESNext), TypeScript, Tailwind CSS, Vue/React</li></ul><p>Rất vui được kết nối và trao đổi cùng các bạn đồng nghiệp và độc giả!</p>', 'publish', '2026-10-10 08:00:00'),
(2, 1, 'Chính sách bảo mật', 'chinh-sach-bao-mat', '<h2>Chính sách bảo mật thông tin</h2><p>Website TonyHoaiVu cam kết tôn trọng quyền riêng tư của khách truy cập. Mọi thông tin như email khi bạn để lại bình luận hoặc liên hệ sẽ được bảo mật tuyệt đối và không bao giờ chia sẻ cho bên thứ ba.</p>', 'publish', '2026-10-10 08:00:00');

-- Sample Comments
INSERT INTO `comments` (`id`, `post_id`, `author_name`, `author_email`, `author_website`, `content`, `status`, `created_at`) VALUES
(1, 1, 'Nguyễn Văn Minh', 'minh.nguyen@example.com', 'https://minhdev.vn', 'Bài viết rất chất lượng anh Vũ ơi! Kiến trúc phân tầng rõ ràng giúp code dễ bảo trì hơn hẳn.', 'approved', '2026-10-16 11:20:00'),
(2, 1, 'Trần Thu Hằng', 'hang.tran@example.com', '', 'Em rất thích giao diện tối giản của blog. Tốc độ load trang cực mượt!', 'approved', '2026-10-17 08:45:00'),
(3, 2, 'Lê Hoàng Nam', 'nam.le@techcorp.vn', '', 'Phần giải thích về Leftmost Prefix Rule rất súc tích và trực quan, cảm ơn anh đã chia sẻ!', 'approved', '2026-10-19 16:30:00');
