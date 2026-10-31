# TonyHoaiVu CMS — Hệ thống Quản trị Blog Cá nhân (PHP 8 + MySQL)

Website blog cá nhân công nghệ cao cấp mang thương hiệu **TonyHoaiVu** (tham khảo bố cục hiện đại tại [tonyhoaivu.vercel.app](https://tonyhoaivu.vercel.app/)) được phát triển với hệ thống CMS độc lập viết bằng **PHP 8 thuần và MySQL**, giao diện và trải nghiệm quản trị mang đậm phong cách **WordPress**.

---

## 🌟 Tính năng nổi bật

### 1. Giao diện Blog Người dùng (Frontend)
- **Thiết kế tối giản, thanh lịch**: Layout hiện đại với font chữ Plus Jakarta Sans & Playfair Display, hỗ trợ chuyển đổi giao diện **Sáng / Tối (Dark & Light Mode)** lưu qua localStorage.
- **Tối ưu SEO**: Thẻ meta OpenGraph, Twitter Cards, Semantic HTML5, Slug tự động thân thiện với công cụ tìm kiếm.
- **Trang chủ & Danh sách bài viết**: Banner Hero chào mừng, lọc bài viết theo chuyên mục, bài viết nổi bật (Featured), phân trang thông minh.
- **Trang chi tiết bài viết**: Hiển thị thời gian đọc ước tính (Reading time), đếm lượt xem (View counter chống spam IP trong 1 giờ), hộp thông tin tác giả, bài viết liên quan cùng chuyên mục.
- **Hệ thống bình luận tương tác**: Gửi bình luận an toàn với CSRF Token, kiểm tra hợp lệ email và cơ chế kiểm duyệt trước khi xuất bản.
- **Trang chuyên mục, Tìm kiếm bài viết**: Tìm kiếm theo thời gian thực (Search modal phím tắt `Ctrl + K` hoặc `Cmd + K`).
- **Trang giới thiệu tác giả & Trang liên hệ**: Form liên hệ hợp tác với xác thực phía server.

### 2. Trang Quản trị Admin phong cách WordPress (`/admin/`)
- **Top Admin Bar (#wpadminbar)**: Truy cập nhanh website ngoài trang chủ, nút tạo bài viết nhanh (+ Mới), thông tin tài khoản và đăng xuất.
- **Sidebar menu phong cách WordPress**: Bảng tin, Bài viết, Chuyên mục, Media thư viện, Trang tĩnh, Bình luận (có huy hiệu số lượng chờ duyệt), Menu điều hướng, Thành viên, Cài đặt hệ thống.
- **Bảng tin (Dashboard)**: Thống kê số lượng bài viết (đã xuất bản vs bản nháp), tổng lượt xem, chuyên mục, bình luận; widget soạn nháp nhanh (Quick Draft), danh sách hoạt động gần đây.
- **Soạn thảo bài viết chuẩn WYSIWYG**: Hỗ trợ in đậm, nghiêng, gạch chân, tiêu đề H1-H6, căn lề, trích dẫn blockquote, chèn code, bảng, ảnh, liên kết và chuyển đổi nhanh giữa chế độ Trực quan (Visual) và Mã HTML.
- **Chức năng xem trước bài viết (Live Preview)** trước khi lưu hoặc xuất bản.
- **Thư viện Media**: Tải lên hình ảnh bằng cách kéo thả hoặc chọn file, kiểm tra MIME type, mã hóa tên file ngẫu nhiên chống ghi đè, chống upload file thực thi độc hại (`.php`, `.phtml`), sao chép URL hình ảnh với 1 cú click.
- **Quản lý chuyên mục**: Giao diện chia đôi màn hình 2 cột đặc trưng của WordPress.
- **Điều hướng Menu**: Tùy biến các liên kết hiển thị trên thanh Header và chân trang Footer.
- **Phân quyền người dùng**: Vai trò Quản trị viên (`administrator`) và Biên tập viên (`editor`), đổi mật khẩu mã hóa bằng chuẩn `password_hash()`.

---

## 🛡️ Bảo mật hệ thống

1. **Chống SQL Injection**: Sử dụng 100% `PDO::prepare()` và Parameter Binding cho tất cả các câu truy vấn. Không nối chuỗi biến người dùng vào SQL.
2. **Chống tấn công XSS**: Lọc dữ liệu đầu ra bằng hàm `htmlspecialchars()` với charset `UTF-8` và cờ `ENT_QUOTES`.
3. **Chống tấn công CSRF**: Cơ chế sinh token ngẫu nhiên qua `bin2hex(random_bytes(32))` và kiểm tra bằng `hash_equals()` trên mọi form POST.
4. **Bảo mật thư mục Uploads**: File `.htaccess` trong thư mục `uploads/` ngăn chặn thực thi tất cả các file PHP/CGI/Script, chỉ cho phép các định dạng ảnh hợp lệ (jpg, png, gif, webp, svg).
5. **Mã hóa mật khẩu**: Sử dụng thuật toán `PASSWORD_BCRYPT` chuẩn của PHP 8.
6. **Bảo mật phiên làm việc (Session)**: Kích hoạt `session.cookie_httponly = 1`, `session.use_only_cookies = 1`, chống Session Fixation qua `session_regenerate_id(true)`.

---

## 📁 Cấu trúc thư mục

```text
/tonyhoaivu/
│
├── /admin/                  # Hệ thống quản trị WordPress-like
│   ├── index.php            # Dashboard thống kê
│   ├── posts.php            # Danh sách bài viết & bộ lọc
│   ├── post-new.php         # Soạn bài viết mới với WYSIWYG
│   ├── post-edit.php        # Chỉnh sửa bài viết
│   ├── categories.php       # Quản lý chuyên mục
│   ├── pages.php            # Danh sách trang tĩnh
│   ├── page-new.php         # Tạo/Sửa trang tĩnh
│   ├── media.php            # Thư viện Media & Upload
│   ├── menus.php            # Quản lý menu điều hướng
│   ├── comments.php         # Kiểm duyệt bình luận
│   ├── users.php            # Quản lý thành viên & phân quyền
│   ├── settings.php         # Cài đặt website, SEO & MXH
│   ├── login.php            # Giao diện đăng nhập chuẩn WordPress
│   ├── logout.php           # Xử lý đăng xuất an toàn
│   ├── header.php           # Top bar và layout header admin
│   ├── footer.php           # Footer admin
│   └── sidebar.php          # Menu điều hướng admin bên trái
│
├── /includes/               # Thư viện mã nguồn tầng thấp
│   ├── db.php               # Kết nối PDO Singleton
│   ├── functions.php        # Các hàm tiện ích (slugify, reading_time...)
│   ├── auth.php             # Xác thực đăng nhập & phân quyền
│   ├── csrf.php             # Bảo vệ chống CSRF
│   ├── header.php           # Header giao diện người dùng
│   ├── footer.php           # Footer giao diện người dùng
│   └── sidebar.php          # Thanh bên frontend (bio, bài nổi bật)
│
├── /config/
│   └── config.php           # Cấu hình Database & Đường dẫn BASE_URL
│
├── /assets/
│   ├── /css/style.css       # Style frontend
│   ├── /css/admin.css       # Style WordPress admin
│   ├── /js/main.js          # JavaScript tương tác frontend
│   └── /js/admin.js         # JavaScript trình soạn thảo & uploader
│
├── /uploads/                # Thư mục lưu trữ hình ảnh tải lên
│   └── .htaccess            # Khóa thực thi mã độc trong upload
│
├── index.php                # Trang chủ blog
├── post.php                 # Trang chi tiết bài viết
├── category.php             # Trang danh mục bài viết
├── page.php                 # Trang tĩnh (Giới thiệu...)
├── search.php               # Trang tìm kiếm bài viết
├── contact.php              # Trang liên hệ
├── database.sql             # Bản thiết kế CSDL MySQL + Dữ liệu mẫu
├── install.php              # Trình cài đặt tự động 3 bước
├── .htaccess                # Cấu hình Clean URL rewrite Apache
└── README.md                # Tài liệu hướng dẫn sử dụng
```

---

## 🚀 Hướng dẫn Cài đặt & Triển khai

### Cách 1: Cài đặt tự động qua `install.php` (Khuyên dùng)
1. Tải toàn bộ mã nguồn lên hosting (cPanel, DirectAdmin) hoặc thư mục web của Localhost (`htdocs` trên XAMPP hoặc `www` trên Laragon).
2. Tạo một cơ sở dữ liệu trống trong **phpMyAdmin** (ví dụ: `tonyhoaivu_db`).
3. Truy cập vào đường dẫn: `http://ten-mien-cua-ban/install.php`.
4. Trình cài đặt sẽ:
   - Tự động kiểm tra phiên bản PHP (&ge; 8.1) và các extension PDO.
   - Cho phép bạn nhập thông tin DB (Host, Port, DB Name, User, Password).
   - Thiết lập tài khoản quản trị Admin (Username, Email, Mật khẩu).
   - Tự động nạp cấu trúc bảng và dữ liệu mẫu từ `database.sql`.
   - Ghi file cấu hình `config/config.php` và tạo file khóa `install.lock` để bảo vệ an toàn.
5. Sau khi hoàn tất, hệ thống sẽ chuyển hướng bạn đến trang đăng nhập quản trị `/admin/login.php`.

### Cách 2: Cài đặt thủ công
1. Mở phpMyAdmin và nhập (Import) file `database.sql` vào cơ sở dữ liệu của bạn.
2. Mở file `config/config.php` và điền thông số kết nối:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'tonyhoaivu_db');
   define('DB_USER', 'root');
   define('DB_PASS', 'mat_khau_cua_ban');
   ```
3. Tạo file trống có tên `install.lock` tại thư mục gốc để khóa trình cài đặt.
4. Đăng nhập quản trị với tài khoản mặc định:
   - **Đường dẫn**: `/admin/login.php`
   - **Tài khoản**: `admin`
   - **Mật khẩu**: `admin123`
   *(Hãy đổi mật khẩu ngay trong mục Thành viên sau khi đăng nhập).*

---

## ⚙️ Yêu cầu Hệ thống
- **PHP**: Phiên bản 8.1 trở lên.
- **MySQL**: 5.7 trở lên hoặc MariaDB 10.3 trở lên.
- **Web Server**: Apache có bật `mod_rewrite` (hỗ trợ file `.htaccess`) hoặc Nginx có cấu hình rewrite tương đương.
- **Extensions**: `pdo_mysql`, `mbstring`, `fileinfo`.
- Hoàn toàn **không yêu cầu Node.js** hay lệnh terminal phức tạp trên hosting. Phù hợp 100% với các gói Shared Hosting phổ thông!
