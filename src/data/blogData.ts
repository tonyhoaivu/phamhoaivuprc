export interface Post {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  category_id: number;
  category_name: string;
  category_slug: string;
  featured_image: string;
  tags: string[];
  status: 'publish' | 'draft' | 'private';
  is_featured: boolean;
  views_count: number;
  created_at: string;
  author_name: string;
  author_avatar: string;
  seo_title?: string;
  seo_description?: string;
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string;
  post_count: number;
}

export interface Comment {
  id: number;
  post_id: number;
  post_title: string;
  post_slug: string;
  author_name: string;
  author_email: string;
  author_website?: string;
  content: string;
  status: 'approved' | 'pending' | 'spam' | 'trash';
  created_at: string;
}

export interface Page {
  id: number;
  title: string;
  slug: string;
  content: string;
  status: 'publish' | 'draft';
  created_at: string;
}

export interface MediaItem {
  id: number;
  filename: string;
  original_name: string;
  url: string;
  file_size: number;
  file_type: string;
  created_at: string;
}

export interface MenuItem {
  id: number;
  title: string;
  url: string;
  target: string;
  position: 'header' | 'footer';
  sort_order: number;
}

export interface User {
  id: number;
  username: string;
  email: string;
  display_name: string;
  role: 'administrator' | 'editor';
  avatar: string;
  bio: string;
}

export const initialCategories: Category[] = [
  {
    id: 1,
    name: 'Lập trình',
    slug: 'lap-trinh',
    description: 'Chia sẻ kiến thức lập trình backend, frontend, clean code và kinh nghiệm thực chiến.',
    post_count: 3
  },
  {
    id: 2,
    name: 'Kiến trúc hệ thống',
    slug: 'kien-truc-he-thong',
    description: 'Thiết kế hệ thống chịu tải cao, microservices, cơ sở dữ liệu và tối ưu hiệu năng.',
    post_count: 2
  },
  {
    id: 3,
    name: 'DevOps & Cloud',
    slug: 'devops-cloud',
    description: 'Docker, Kubernetes, CI/CD, AWS, Google Cloud và tự động hóa hạ tầng.',
    post_count: 2
  },
  {
    id: 4,
    name: 'Góc suy ngẫm',
    slug: 'goc-suy-ngam',
    description: 'Những câu chuyện đời thường, bài học nghề nghiệp và triết lý công nghệ cá nhân.',
    post_count: 1
  }
];

export const initialPosts: Post[] = [
  {
    id: 1,
    title: 'Xây dựng Custom CMS bằng PHP 8 thuần theo phong cách WordPress',
    slug: 'xay-dung-custom-cms-bang-php-8-thuan-theo-phong-cach-wordpress',
    excerpt: 'Hướng dẫn toàn diện về cách tổ chức mã nguồn, phân tầng kiến trúc và bảo mật cho hệ thống quản trị nội dung độc lập.',
    content: `<p>Trong thế giới phát triển web hiện đại với hàng loạt framework đồ sộ như Laravel, Symfony hay các nền tảng CMS như WordPress, việc hiểu sâu sắc cách hoạt động ở tầng thấp của <strong>PHP thuần và MySQL</strong> mang lại nền tảng kiến thức vô giá cho bất kỳ kỹ sư phần mềm nào.</p>
<h2>1. Tại sao lại tự xây dựng CMS độc lập?</h2>
<p>Nhiều người sẽ tự hỏi: <em>Tại sao lại phải tự viết một CMS mới khi WordPress đã có sẵn hàng triệu plugin?</em> Câu trả lời nằm ở ba yếu tố then chốt:</p>
<ul>
  <li><strong>Kiểm soát 100% mã nguồn:</strong> Không có bất kỳ dòng code dư thừa nào, không bloatware, thời gian phản hồi máy chủ đạt dưới 50ms.</li>
  <li><strong>Bảo mật tối đa:</strong> Hacker thường nhắm vào các lỗ hổng đã biết trong plugin WordPress phổ biến. Với CMS độc lập, bề mặt tấn công được thu hẹp tối đa.</li>
  <li><strong>Tự do tùy biến nghiệp vụ:</strong> Dễ dàng tích hợp với các hệ thống nội bộ, API thanh toán mà không bị gò bó bởi hooks hay filters phức tạp.</li>
</ul>
<h2>2. Kiến trúc phân tầng và bảo mật cốt lõi</h2>
<p>Một hệ thống CMS bền vững đòi hỏi việc tách biệt rành mạch giữa các tầng:</p>
<ul>
  <li><code>/config:</code> Lưu trữ thông số kết nối cơ sở dữ liệu, Base URL và môi trường chạy.</li>
  <li><code>/includes:</code> Tầng xử lý cơ sở dữ liệu qua mẫu thiết kế PDO Singleton, các hàm tiện ích và middleware xác thực CSRF Token.</li>
  <li><code>/admin:</code> Hệ thống bảng điều khiển độc lập với quy chuẩn phân quyền nghiêm ngặt.</li>
</ul>
<h2>3. Lời kết</h2>
<p>Làm chủ một hệ thống quản trị nội dung từ đầu tới cuối là trải nghiệm quý báu giúp nâng tầm tư duy kiến trúc của lập trình viên Full-stack.</p>`,
    category_id: 1,
    category_name: 'Lập trình',
    category_slug: 'lap-trinh',
    featured_image: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200',
    tags: ['PHP', 'CMS', 'MySQL', 'Architecture'],
    status: 'publish',
    is_featured: true,
    views_count: 1420,
    created_at: '2026-10-15 09:30:00',
    author_name: 'Tony Hoài Vũ',
    author_avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
    seo_title: 'Xây dựng Custom CMS bằng PHP 8 - TonyHoaiVu',
    seo_description: 'Hướng dẫn chi tiết xây dựng hệ thống CMS độc lập chuẩn bảo mật với PHP 8 và MySQL.'
  },
  {
    id: 2,
    title: 'Chiến lược thiết kế cơ sở dữ liệu MySQL chịu tải cao và tối ưu chỉ mục (Index)',
    slug: 'chien-luoc-thiet-ke-co-so-du-lieu-mysql-chiu-tai-cao',
    excerpt: 'Khám phá các kỹ thuật indexing B-Tree, tối ưu câu lệnh truy vấn và phân vùng dữ liệu để đạt hàng triệu truy vấn mỗi ngày.',
    content: `<p>Khi lưu lượng truy cập của trang web tăng đột biến, nút thắt cổ chai (bottleneck) phổ biến nhất hầu như luôn nằm ở tầng cơ sở dữ liệu.</p>
<h2>Hiểu đúng về cấu trúc B-Tree Index trong InnoDB</h2>
<p>MySQL InnoDB sử dụng cấu trúc cây B-Tree cho hầu hết các chỉ mục. Điều này có nghĩa rằng dữ liệu được sắp xếp tuần tự, cho phép tìm kiếm theo phạm vi và tìm kiếm chính xác cực nhanh.</p>
<h2>Quy tắc tiền tố bên trái nhất (Leftmost Prefix Rule)</h2>
<p>Khi bạn tạo một composite index trên các cột <code>(status, created_at, category_id)</code>, MySQL chỉ có thể tận dụng index này khi câu truy vấn lọc theo thứ tự từ trái qua phải. Nếu bạn chỉ truy vấn theo <code>created_at</code>, toàn bộ index này sẽ bị bỏ qua và dẫn đến Full Table Scan!</p>`,
    category_id: 2,
    category_name: 'Kiến trúc hệ thống',
    category_slug: 'kien-truc-he-thong',
    featured_image: 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=1200',
    tags: ['MySQL', 'Database', 'Performance', 'Indexing'],
    status: 'publish',
    is_featured: true,
    views_count: 890,
    created_at: '2026-10-18 14:15:00',
    author_name: 'Tony Hoài Vũ',
    author_avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'
  },
  {
    id: 3,
    title: 'Triển khai Docker & CI/CD tự động cho ứng dụng PHP trên Cloud',
    slug: 'trien-khai-docker-va-cicd-tu-dong-cho-ung-dung-php-tren-cloud',
    excerpt: 'Quy trình đóng gói container tối ưu dung lượng và thiết lập pipeline tự động hóa kiểm thử mã nguồn trước khi deploy.',
    content: `<p>Sử dụng container hóa giúp giải quyết triệt để vấn đề kinh điển: <em>"Trên máy tôi chạy được mà lên server thì lỗi"</em>.</p>
<h2>Đóng gói với PHP-FPM và Alpine Linux</h2>
<p>Việc chọn base image nhẹ như <code>php:8.2-fpm-alpine</code> kết hợp với Multi-stage build giúp giảm dung lượng image từ gần 1GB xuống chỉ còn dưới 80MB, giúp quá trình pull/push và khởi động container diễn ra chỉ trong vài giây.</p>`,
    category_id: 3,
    category_name: 'DevOps & Cloud',
    category_slug: 'devops-cloud',
    featured_image: 'https://images.unsplash.com/photo-1605379399642-870262d3d051?w=1200',
    tags: ['Docker', 'DevOps', 'CI/CD', 'Cloud'],
    status: 'publish',
    is_featured: false,
    views_count: 650,
    created_at: '2026-10-22 10:00:00',
    author_name: 'Tony Hoài Vũ',
    author_avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'
  },
  {
    id: 4,
    title: 'Bảo mật ứng dụng Web: Phòng chống SQL Injection và XSS từ gốc rễ',
    slug: 'bao-mat-ung-dung-web-phong-chong-sql-injection-va-xss',
    excerpt: 'Những lỗ hổng bảo mật kinh điển nhưng vẫn phổ biến, cách phòng tránh triệt để bằng chuẩn lập trình an toàn với PDO.',
    content: `<p>Bảo mật không phải là tính năng bổ sung sau cùng, mà là tư duy lập trình cần thấm nhuần trong từng câu lệnh.</p>
<h2>Tại sao chỉ dùng Prepared Statements là chưa đủ?</h2>
<p>Nhiều người nghĩ chỉ cần dùng <code>PDO::prepare()</code> là an toàn tuyệt đối. Tuy nhiên, nếu bạn nối chuỗi tên bảng hoặc thứ tự sắp xếp <code>ORDER BY :sort</code>, parameter binding sẽ không bảo vệ bạn khỏi SQL Injection ở vị trí identifier!</p>`,
    category_id: 1,
    category_name: 'Lập trình',
    category_slug: 'lap-trinh',
    featured_image: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200',
    tags: ['Security', 'SQL Injection', 'XSS', 'PHP'],
    status: 'publish',
    is_featured: false,
    views_count: 1120,
    created_at: '2026-10-25 16:45:00',
    author_name: 'Tony Hoài Vũ',
    author_avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150'
  }
];

export const initialComments: Comment[] = [
  {
    id: 1,
    post_id: 1,
    post_title: 'Xây dựng Custom CMS bằng PHP 8 thuần theo phong cách WordPress',
    post_slug: 'xay-dung-custom-cms-bang-php-8-thuan-theo-phong-cach-wordpress',
    author_name: 'Nguyễn Văn Minh',
    author_email: 'minh.nguyen@example.com',
    author_website: 'https://minhdev.vn',
    content: 'Bài viết rất chất lượng anh Vũ ơi! Kiến trúc phân tầng rõ ràng giúp code dễ bảo trì hơn hẳn.',
    status: 'approved',
    created_at: '2026-10-16 11:20:00'
  },
  {
    id: 2,
    post_id: 1,
    post_title: 'Xây dựng Custom CMS bằng PHP 8 thuần theo phong cách WordPress',
    post_slug: 'xay-dung-custom-cms-bang-php-8-thuan-theo-phong-cach-wordpress',
    author_name: 'Trần Thu Hằng',
    author_email: 'hang.tran@example.com',
    content: 'Em rất thích giao diện tối giản của blog. Tốc độ load trang cực mượt!',
    status: 'approved',
    created_at: '2026-10-17 08:45:00'
  },
  {
    id: 3,
    post_id: 2,
    post_title: 'Chiến lược thiết kế cơ sở dữ liệu MySQL chịu tải cao và tối ưu chỉ mục (Index)',
    post_slug: 'chien-luoc-thiet-ke-co-so-du-lieu-mysql-chiu-tai-cao',
    author_name: 'Lê Hoàng Nam',
    author_email: 'nam.le@techcorp.vn',
    content: 'Phần giải thích về Leftmost Prefix Rule rất súc tích và trực quan, cảm ơn anh đã chia sẻ!',
    status: 'approved',
    created_at: '2026-10-19 16:30:00'
  }
];

export const initialPages: Page[] = [
  {
    id: 1,
    title: 'Giới thiệu về Tony Hoài Vũ',
    slug: 'gioi-thieu',
    content: `<h2>Xin chào, tôi là Tony Hoài Vũ 👋</h2>
<p>Chào mừng bạn đến với blog cá nhân của tôi! Tôi là một <strong>Senior Software Engineer</strong> và <strong>Solutions Architect</strong> với hơn 8 năm kinh nghiệm trong lĩnh vực phát triển phần mềm, kiến trúc phân tán chịu tải cao và giải pháp đám mây.</p>
<p>Tại không gian này, tôi ghi lại những trải nghiệm thực chiến, đúc kết các bài học kỹ thuật từ những dự án quy mô lớn, và chia sẻ các nguyên lý thiết kế hệ thống tối ưu.</p>
<h3>Chuyên môn cốt lõi:</h3>
<ul>
  <li><strong>Backend &amp; CMS:</strong> PHP (Clean Architecture, Custom WordPress CMS, Laravel), Go, Node.js</li>
  <li><strong>Databases:</strong> MySQL Optimization, PostgreSQL, Redis Caching, Elasticsearch</li>
  <li><strong>DevOps &amp; Infrastructure:</strong> Docker, Kubernetes, CI/CD pipelines, AWS, Google Cloud Platform</li>
  <li><strong>Frontend:</strong> Modern JavaScript/TypeScript, Tailwind CSS, Responsive Web Design</li>
</ul>
<p>Hãy kết nối cùng tôi qua các kênh mạng xã hội bên dưới để cùng nhau trao đổi về công nghệ!</p>`,
    status: 'publish',
    created_at: '2026-10-10 08:00:00'
  },
  {
    id: 2,
    title: 'Chính sách bảo mật',
    slug: 'chinh-sach-bao-mat',
    content: `<h2>Chính sách bảo mật thông tin</h2>
<p>Website TonyHoaiVu tôn trọng tuyệt đối quyền riêng tư của độc giả. Mọi thông tin như email khi bạn để lại bình luận hoặc liên hệ sẽ chỉ được dùng để phản hồi và bảo mật an toàn theo tiêu chuẩn cao nhất.</p>`,
    status: 'publish',
    created_at: '2026-10-10 08:00:00'
  }
];

export const initialMedia: MediaItem[] = [
  {
    id: 1,
    filename: '20261015_php_cms_architecture.jpg',
    original_name: 'php-cms-clean-architecture.jpg',
    url: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200',
    file_size: 482000,
    file_type: 'image/jpeg',
    created_at: '2026-10-15 09:00:00'
  },
  {
    id: 2,
    filename: '20261018_mysql_indexing_btree.jpg',
    original_name: 'mysql-btree-index-strategy.jpg',
    url: 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=1200',
    file_size: 320000,
    file_type: 'image/jpeg',
    created_at: '2026-10-18 13:40:00'
  },
  {
    id: 3,
    filename: '20261022_docker_container_cicd.jpg',
    original_name: 'docker-alpine-multistage.jpg',
    url: 'https://images.unsplash.com/photo-1605379399642-870262d3d051?w=1200',
    file_size: 512000,
    file_type: 'image/jpeg',
    created_at: '2026-10-22 09:20:00'
  },
  {
    id: 4,
    filename: '20261025_web_security_csrf.jpg',
    original_name: 'cybersecurity-encryption.jpg',
    url: 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=1200',
    file_size: 610000,
    file_type: 'image/jpeg',
    created_at: '2026-10-25 15:30:00'
  }
];

export const initialMenus: MenuItem[] = [
  { id: 1, title: 'Trang chủ', url: '/', target: '_self', position: 'header', sort_order: 1 },
  { id: 2, title: 'Lập trình', url: '/category/lap-trinh', target: '_self', position: 'header', sort_order: 2 },
  { id: 3, title: 'Kiến trúc', url: '/category/kien-truc-he-thong', target: '_self', position: 'header', sort_order: 3 },
  { id: 4, title: 'DevOps', url: '/category/devops-cloud', target: '_self', position: 'header', sort_order: 4 },
  { id: 5, title: 'Giới thiệu', url: '/page/gioi-thieu', target: '_self', position: 'header', sort_order: 5 },
  { id: 6, title: 'Liên hệ', url: '/contact', target: '_self', position: 'header', sort_order: 6 }
];

export const defaultSettings = {
  site_title: 'TonyHoaiVu',
  site_tagline: 'Kỹ thuật phần mềm & Kiến trúc hệ thống',
  site_description: 'Blog cá nhân của Tony Hoài Vũ - Chia sẻ kinh nghiệm thực chiến về phát triển phần mềm, kiến trúc hệ thống và giải pháp số.',
  admin_email: 'contact@tonyhoaivu.com',
  posts_per_page: 6,
  github_url: 'https://github.com/tonyhoaivu',
  linkedin_url: 'https://linkedin.com/in/tonyhoaivu',
  facebook_url: 'https://facebook.com/tonyhoaivu',
  twitter_url: 'https://twitter.com/tonyhoaivu',
  footer_copyright: '© 2026 TonyHoaiVu. Bản quyền thuộc về tác giả.'
};

export const defaultUser: User = {
  id: 1,
  username: 'admin',
  email: 'admin@tonyhoaivu.com',
  display_name: 'Tony Hoài Vũ',
  role: 'administrator',
  avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200',
  bio: 'Senior Software Engineer & Solutions Architect. Đam mê chia sẻ kiến thức về lập trình, kiến trúc hệ thống và cuộc sống công nghệ.'
};
