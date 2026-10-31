import React, { useState } from 'react';
import { 
  LayoutDashboard, FileText, FolderTree, Image, Layers, MessageSquare, 
  Menu as MenuIcon, Users, Settings, Plus, ExternalLink, Trash2, Edit3, 
  Eye, CheckCircle2, AlertCircle, Search, Upload, Copy, Check, Save, ArrowLeft, RefreshCw
} from 'lucide-react';
import { Post, Category, Comment, Page, MediaItem, MenuItem, User } from '../data/blogData';

interface AdminPanelProps {
  posts: Post[];
  categories: Category[];
  comments: Comment[];
  pages: Page[];
  media: MediaItem[];
  menus: MenuItem[];
  author: User;
  onUpdatePosts: (posts: Post[]) => void;
  onUpdateCategories: (categories: Category[]) => void;
  onUpdateComments: (comments: Comment[]) => void;
  onUpdatePages: (pages: Page[]) => void;
  onUpdateMedia: (media: MediaItem[]) => void;
  onUpdateMenus: (menus: MenuItem[]) => void;
  onUpdateAuthor: (author: User) => void;
  onReturnToBlog: () => void;
}

export const AdminPanel: React.FC<AdminPanelProps> = ({
  posts,
  categories,
  comments,
  pages,
  media,
  menus,
  author,
  onUpdatePosts,
  onUpdateCategories,
  onUpdateComments,
  onUpdatePages,
  onUpdateMedia,
  onUpdateMenus,
  onUpdateAuthor,
  onReturnToBlog
}) => {
  // Navigation tabs inside admin
  const [activeTab, setActiveTab] = useState<
    'dashboard' | 'posts' | 'post-new' | 'categories' | 'media' | 'pages' | 'comments' | 'menus' | 'users' | 'settings'
  >('dashboard');

  // Currently editing post (if any)
  const [editingPostId, setEditingPostId] = useState<number | null>(null);

  // Success alert message
  const [alertMsg, setAlertMsg] = useState<string | null>(null);

  const showAlert = (msg: string) => {
    setAlertMsg(msg);
    setTimeout(() => setAlertMsg(null), 3500);
  };

  // Status and category filter for posts tab
  const [postStatusFilter, setPostStatusFilter] = useState<'all' | 'publish' | 'draft'>('all');
  const [postCatFilter, setPostCatFilter] = useState<number>(0);
  const [postSearch, setPostSearch] = useState<string>('');

  // Quick draft form in dashboard
  const [quickDraft, setQuickDraft] = useState({ title: '', content: '' });

  // Post Editor Form State
  const [postForm, setPostForm] = useState({
    title: '',
    slug: '',
    content: '',
    excerpt: '',
    category_id: categories[0]?.id || 1,
    featured_image: '',
    tags: '',
    status: 'publish' as 'publish' | 'draft' | 'private',
    is_featured: false,
    seo_title: '',
    seo_description: ''
  });
  const [editorMode, setEditorMode] = useState<'visual' | 'html'>('visual');
  const [previewModalOpen, setPreviewModalOpen] = useState(false);

  // Categories Form State
  const [newCat, setNewCat] = useState({ name: '', slug: '', description: '' });

  // Media upload simulation
  const [uploadUrlInput, setUploadUrlInput] = useState('');
  const [uploadNameInput, setUploadNameInput] = useState('');

  // Menu Form State
  const [newMenu, setNewMenu] = useState({ title: '', url: '', target: '_self', position: 'header' as 'header' | 'footer', sort_order: 1 });

  // Settings State
  const [settingsForm, setSettingsForm] = useState({
    siteTitle: 'TonyHoaiVu',
    siteTagline: 'Kỹ thuật phần mềm & Kiến trúc hệ thống',
    adminEmail: 'admin@tonyhoaivu.com',
    postsPerPage: 6,
    seoDesc: 'Blog cá nhân chia sẻ kiến thức công nghệ và lập trình Full-stack.',
    githubUrl: 'https://github.com/tonyhoaivu',
    linkedinUrl: 'https://linkedin.com/in/tonyhoaivu'
  });

  // Calculate dashboard metrics
  const totalViews = posts.reduce((sum, p) => sum + p.views_count, 0);
  const pendingCommentsCount = comments.filter(c => c.status === 'pending').length;

  // Slug generator helper
  const slugify = (text: string) => {
    return text
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^\w\s-]/g, '')
      .trim()
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '');
  };

  // Open Post for editing
  const handleEditPost = (post: Post) => {
    setEditingPostId(post.id);
    setPostForm({
      title: post.title,
      slug: post.slug,
      content: post.content,
      excerpt: post.excerpt,
      category_id: post.category_id,
      featured_image: post.featured_image,
      tags: post.tags.join(', '),
      status: post.status,
      is_featured: post.is_featured,
      seo_title: post.seo_title || '',
      seo_description: post.seo_description || ''
    });
    setActiveTab('post-new');
  };

  // Save Post (Create or Update)
  const handleSavePost = (e: React.FormEvent) => {
    e.preventDefault();
    if (!postForm.title) return;

    const catObj = categories.find(c => c.id === Number(postForm.category_id)) || categories[0];
    const tagsArray = postForm.tags.split(',').map(t => t.trim()).filter(Boolean);
    const finalSlug = postForm.slug || slugify(postForm.title);

    if (editingPostId) {
      // Update
      const updated = posts.map(p => {
        if (p.id === editingPostId) {
          return {
            ...p,
            title: postForm.title,
            slug: finalSlug,
            content: postForm.content,
            excerpt: postForm.excerpt || postForm.content.replace(/<[^>]*>/g, '').substring(0, 160),
            category_id: catObj.id,
            category_name: catObj.name,
            category_slug: catObj.slug,
            featured_image: postForm.featured_image || 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200',
            tags: tagsArray,
            status: postForm.status,
            is_featured: postForm.is_featured,
            seo_title: postForm.seo_title,
            seo_description: postForm.seo_description
          };
        }
        return p;
      });
      onUpdatePosts(updated);
      showAlert(`Đã cập nhật bài viết "${postForm.title}" thành công!`);
    } else {
      // Create
      const newPost: Post = {
        id: Date.now(),
        title: postForm.title,
        slug: finalSlug,
        content: postForm.content,
        excerpt: postForm.excerpt || postForm.content.replace(/<[^>]*>/g, '').substring(0, 160),
        category_id: catObj.id,
        category_name: catObj.name,
        category_slug: catObj.slug,
        featured_image: postForm.featured_image || 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200',
        tags: tagsArray,
        status: postForm.status,
        is_featured: postForm.is_featured,
        views_count: 0,
        created_at: new Date().toISOString().replace('T', ' ').substring(0, 19),
        author_name: author.display_name,
        author_avatar: author.avatar,
        seo_title: postForm.seo_title,
        seo_description: postForm.seo_description
      };
      onUpdatePosts([newPost, ...posts]);
      showAlert(`Đã đăng bài viết mới "${postForm.title}" thành công!`);
    }

    setEditingPostId(null);
    setActiveTab('posts');
  };

  // Delete Post
  const handleDeletePost = (id: number) => {
    if (confirm('Bạn có chắc chắn muốn xóa bài viết này không?')) {
      onUpdatePosts(posts.filter(p => p.id !== id));
      showAlert('Đã xóa bài viết thành công.');
    }
  };

  // Quick Draft Submit
  const handleQuickDraft = (e: React.FormEvent) => {
    e.preventDefault();
    if (!quickDraft.title) return;
    const newDraft: Post = {
      id: Date.now(),
      title: quickDraft.title,
      slug: slugify(quickDraft.title) + '-' + Date.now(),
      content: `<p>${quickDraft.content}</p>`,
      excerpt: quickDraft.content.substring(0, 150),
      category_id: categories[0]?.id || 1,
      category_name: categories[0]?.name || 'Lập trình',
      category_slug: categories[0]?.slug || 'lap-trinh',
      featured_image: 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200',
      tags: ['Bản nháp'],
      status: 'draft',
      is_featured: false,
      views_count: 0,
      created_at: new Date().toISOString().replace('T', ' ').substring(0, 19),
      author_name: author.display_name,
      author_avatar: author.avatar
    };
    onUpdatePosts([newDraft, ...posts]);
    setQuickDraft({ title: '', content: '' });
    showAlert('Đã lưu bản nháp nhanh thành công!');
  };

  // Add Category
  const handleAddCategory = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newCat.name) return;
    const catItem: Category = {
      id: Date.now(),
      name: newCat.name,
      slug: newCat.slug || slugify(newCat.name),
      description: newCat.description,
      post_count: 0
    };
    onUpdateCategories([...categories, catItem]);
    setNewCat({ name: '', slug: '', description: '' });
    showAlert(`Đã tạo chuyên mục "${catItem.name}" thành công!`);
  };

  // Delete Category
  const handleDeleteCategory = (id: number) => {
    if (confirm('Xóa chuyên mục này? Các bài viết sẽ không bị xóa.')) {
      onUpdateCategories(categories.filter(c => c.id !== id));
      showAlert('Đã xóa chuyên mục.');
    }
  };

  // Add Media
  const handleAddMedia = (e: React.FormEvent) => {
    e.preventDefault();
    if (!uploadUrlInput) return;
    const newMed: MediaItem = {
      id: Date.now(),
      filename: `img_${Date.now()}.jpg`,
      original_name: uploadNameInput || 'uploaded_image.jpg',
      url: uploadUrlInput,
      file_size: 450000,
      file_type: 'image/jpeg',
      created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
    };
    onUpdateMedia([newMed, ...media]);
    setUploadUrlInput('');
    setUploadNameInput('');
    showAlert('Đã thêm ảnh vào thư viện Media thành công!');
  };

  // Comment Actions
  const handleCommentStatus = (id: number, status: 'approved' | 'pending' | 'spam' | 'trash') => {
    if (status === 'trash') {
      onUpdateComments(comments.filter(c => c.id !== id));
      showAlert('Đã xóa bình luận.');
    } else {
      onUpdateComments(comments.map(c => c.id === id ? { ...c, status } : c));
      showAlert(`Đã cập nhật trạng thái bình luận sang "${status}".`);
    }
  };

  return (
    <div className="min-h-screen bg-[#f0f0f1] text-[#2c3338] font-sans antialiased text-xs sm:text-[13px]">
      {/* WordPress Top Admin Bar (#wpadminbar) */}
      <header className="h-8 bg-[#1d2327] text-[#c3c4c7] fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-3 text-xs select-none shadow-sm">
        <div className="flex items-center gap-4">
          <div className="flex items-center gap-1.5 font-semibold text-white">
            <span className="w-5 h-5 rounded-full bg-[#2271b1] text-white flex items-center justify-center text-[10px] font-bold">W</span>
            <span>TonyHoaiVu CMS</span>
          </div>
          <button
            onClick={onReturnToBlog}
            className="flex items-center gap-1 text-[#c3c4c7] hover:text-[#72aee6] transition-colors"
            title="Xem giao diện ngoài website"
          >
            <ExternalLink className="w-3.5 h-3.5" />
            <span>Xem website ngoài</span>
          </button>
          <button
            onClick={() => {
              setEditingPostId(null);
              setPostForm({
                title: '',
                slug: '',
                content: '',
                excerpt: '',
                category_id: categories[0]?.id || 1,
                featured_image: '',
                tags: '',
                status: 'publish',
                is_featured: false,
                seo_title: '',
                seo_description: ''
              });
              setActiveTab('post-new');
            }}
            className="flex items-center gap-1 text-[#c3c4c7] hover:text-[#72aee6] transition-colors"
          >
            <Plus className="w-3.5 h-3.5" />
            <span>+ Mới</span>
          </button>
        </div>

        <div className="flex items-center gap-3">
          <span className="text-[#c3c4c7]">
            Chào, <strong className="text-white">{author.display_name}</strong>
          </span>
          <img
            src={author.avatar}
            alt=""
            className="w-5 h-5 rounded-full object-cover"
            referrerPolicy="no-referrer"
          />
        </div>
      </header>

      {/* Main Container Layout */}
      <div className="flex pt-8 min-h-screen">
        {/* WordPress Iconic Left Sidebar */}
        <aside className="w-40 sm:w-44 bg-[#1d2327] text-[#c3c4c7] shrink-0 min-h-[calc(100vh-32px)] select-none">
          <nav className="py-2.5 space-y-0.5">
            <button
              onClick={() => setActiveTab('dashboard')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left font-normal text-xs transition-colors ${
                activeTab === 'dashboard' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <LayoutDashboard className="w-4 h-4 opacity-80" />
              <span>Bảng tin</span>
            </button>

            <div className="h-[1px] bg-[#2c3338] my-1" />

            <button
              onClick={() => setActiveTab('posts')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'posts' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <FileText className="w-4 h-4 opacity-80" />
              <span>Bài viết</span>
            </button>

            <button
              onClick={() => {
                setEditingPostId(null);
                setPostForm({
                  title: '',
                  slug: '',
                  content: '',
                  excerpt: '',
                  category_id: categories[0]?.id || 1,
                  featured_image: '',
                  tags: '',
                  status: 'publish',
                  is_featured: false,
                  seo_title: '',
                  seo_description: ''
                });
                setActiveTab('post-new');
              }}
              className={`w-full flex items-center gap-2.5 px-3 py-2 pl-6 text-left text-[11px] transition-colors ${
                activeTab === 'post-new' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:text-[#72aee6]'
              }`}
            >
              <span>+ Viết bài mới</span>
            </button>

            <button
              onClick={() => setActiveTab('categories')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'categories' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <FolderTree className="w-4 h-4 opacity-80" />
              <span>Chuyên mục</span>
            </button>

            <button
              onClick={() => setActiveTab('media')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'media' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <Image className="w-4 h-4 opacity-80" />
              <span>Thư viện Media</span>
            </button>

            <button
              onClick={() => setActiveTab('pages')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'pages' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <Layers className="w-4 h-4 opacity-80" />
              <span>Trang tĩnh</span>
            </button>

            <button
              onClick={() => setActiveTab('comments')}
              className={`w-full flex items-center justify-between px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'comments' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <div className="flex items-center gap-2.5">
                <MessageSquare className="w-4 h-4 opacity-80" />
                <span>Bình luận</span>
              </div>
              {pendingCommentsCount > 0 && (
                <span className="bg-[#d63638] text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">
                  {pendingCommentsCount}
                </span>
              )}
            </button>

            <div className="h-[1px] bg-[#2c3338] my-1" />

            <button
              onClick={() => setActiveTab('menus')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'menus' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <MenuIcon className="w-4 h-4 opacity-80" />
              <span>Menu điều hướng</span>
            </button>

            <button
              onClick={() => setActiveTab('users')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'users' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <Users className="w-4 h-4 opacity-80" />
              <span>Thành viên</span>
            </button>

            <button
              onClick={() => setActiveTab('settings')}
              className={`w-full flex items-center gap-2.5 px-3 py-2 text-left text-xs transition-colors ${
                activeTab === 'settings' ? 'bg-[#2271b1] text-white font-semibold' : 'hover:bg-[#2271b1] hover:text-white'
              }`}
            >
              <Settings className="w-4 h-4 opacity-80" />
              <span>Cài đặt</span>
            </button>
          </nav>
        </aside>

        {/* Content Workspace */}
        <main className="flex-1 p-5 sm:p-6 max-w-6xl overflow-x-auto">
          {/* Flash Notice */}
          {alertMsg && (
            <div className="mb-4 p-3 bg-white border-l-4 border-[#00a32a] shadow-sm flex items-center justify-between text-xs">
              <span className="font-medium text-[#1d2327]">{alertMsg}</span>
              <button onClick={() => setAlertMsg(null)} className="text-slate-400 hover:text-slate-600">×</button>
            </div>
          )}

          {/* TAB 1: DASHBOARD */}
          {activeTab === 'dashboard' && (
            <div className="space-y-6">
              <div className="flex items-center justify-between pb-3 border-b border-[#c3c4c7]">
                <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327]">Bảng tin</h1>
                <button
                  onClick={() => {
                    setEditingPostId(null);
                    setActiveTab('post-new');
                  }}
                  className="px-3 py-1 border border-[#2271b1] bg-[#f6f7f7] text-[#2271b1] rounded text-xs font-semibold hover:bg-slate-200"
                >
                  Viết bài mới
                </button>
              </div>

              {/* Welcome Banner */}
              <div className="bg-white border border-[#c3c4c7] p-5 shadow-sm rounded-sm">
                <h2 className="text-lg font-normal mb-1">Chào mừng bạn đến với TonyHoaiVu CMS!</h2>
                <p className="text-[#646970] text-xs leading-relaxed mb-4">
                  Hệ thống CMS độc lập tự phát triển bằng PHP 8 + MySQL với kiến trúc phân tầng bảo mật và giao diện quản trị phong cách WordPress.
                </p>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-2">
                  <div>
                    <h3 className="font-bold text-xs uppercase tracking-wider text-slate-700 mb-2">Bắt đầu nhanh</h3>
                    <button
                      onClick={() => setActiveTab('post-new')}
                      className="px-4 py-2 bg-[#2271b1] text-white rounded font-bold text-xs shadow-sm hover:bg-[#135e96]"
                    >
                      Viết bài mới cho Blog
                    </button>
                  </div>
                  <div>
                    <h3 className="font-bold text-xs uppercase tracking-wider text-slate-700 mb-2">Thao tác dữ liệu</h3>
                    <ul className="space-y-1.5 text-xs text-[#2271b1]">
                      <li><button onClick={() => setActiveTab('categories')} className="hover:underline">Quản lý chuyên mục bài viết</button></li>
                      <li><button onClick={() => setActiveTab('media')} className="hover:underline">Tải ảnh lên thư viện Media</button></li>
                      <li><button onClick={() => setActiveTab('comments')} className="hover:underline">Kiểm duyệt bình luận</button></li>
                    </ul>
                  </div>
                  <div>
                    <h3 className="font-bold text-xs uppercase tracking-wider text-slate-700 mb-2">Thông tin hệ thống</h3>
                    <div className="space-y-1 text-xs text-[#646970] font-mono">
                      <div>PHP 8.2+ · PDO MySQL</div>
                      <div>Engine: InnoDB UTF-8</div>
                      <div>Bảo mật: CSRF + Bcrypt</div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Statistics Grid */}
              <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div className="bg-white border border-[#c3c4c7] p-4 flex items-center gap-3 shadow-sm rounded-sm">
                  <div className="w-10 h-10 rounded bg-blue-600 text-white flex items-center justify-center font-bold">
                    <FileText className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="text-xl font-bold text-[#1d2327]">{posts.length}</div>
                    <div className="text-[11px] text-slate-500">Bài viết ({posts.filter(p => p.status === 'publish').length} xuất bản)</div>
                  </div>
                </div>

                <div className="bg-white border border-[#c3c4c7] p-4 flex items-center gap-3 shadow-sm rounded-sm">
                  <div className="w-10 h-10 rounded bg-purple-600 text-white flex items-center justify-center font-bold">
                    <Eye className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="text-xl font-bold text-[#1d2327]">{totalViews.toLocaleString()}</div>
                    <div className="text-[11px] text-slate-500">Tổng lượt xem bài</div>
                  </div>
                </div>

                <div className="bg-white border border-[#c3c4c7] p-4 flex items-center gap-3 shadow-sm rounded-sm">
                  <div className="w-10 h-10 rounded bg-emerald-600 text-white flex items-center justify-center font-bold">
                    <FolderTree className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="text-xl font-bold text-[#1d2327]">{categories.length}</div>
                    <div className="text-[11px] text-slate-500">Chuyên mục &amp; {pages.length} Trang</div>
                  </div>
                </div>

                <div className="bg-white border border-[#c3c4c7] p-4 flex items-center gap-3 shadow-sm rounded-sm">
                  <div className="w-10 h-10 rounded bg-amber-500 text-white flex items-center justify-center font-bold">
                    <MessageSquare className="w-5 h-5" />
                  </div>
                  <div>
                    <div className="text-xl font-bold text-[#1d2327]">{comments.length}</div>
                    <div className="text-[11px] text-slate-500">Bình luận ({pendingCommentsCount} chờ duyệt)</div>
                  </div>
                </div>
              </div>

              {/* 2 Columns: Quick Draft & Recent Posts */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {/* Quick Draft Box */}
                <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                  <div className="px-4 py-2.5 border-b border-[#c3c4c7] font-semibold text-xs text-[#1d2327]">
                    Soạn nháp nhanh
                  </div>
                  <form onSubmit={handleQuickDraft} className="p-4 space-y-3">
                    <div>
                      <input
                        type="text"
                        value={quickDraft.title}
                        onChange={e => setQuickDraft({ ...quickDraft, title: e.target.value })}
                        placeholder="Tiêu đề ý tưởng bài viết..."
                        required
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs focus:border-[#2271b1] focus:outline-none"
                      />
                    </div>
                    <div>
                      <textarea
                        rows={3}
                        value={quickDraft.content}
                        onChange={e => setQuickDraft({ ...quickDraft, content: e.target.value })}
                        placeholder="Ghi chú nhanh nội dung..."
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs focus:border-[#2271b1] focus:outline-none"
                      />
                    </div>
                    <button
                      type="submit"
                      className="px-3.5 py-1.5 bg-[#2271b1] text-white rounded font-bold text-xs hover:bg-[#135e96]"
                    >
                      Lưu bản nháp
                    </button>
                  </form>
                </div>

                {/* Recent Posts List */}
                <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                  <div className="px-4 py-2.5 border-b border-[#c3c4c7] font-semibold text-xs text-[#1d2327]">
                    Bài viết gần đây
                  </div>
                  <div className="divide-y divide-[#f0f0f1] text-xs">
                    {posts.slice(0, 5).map(p => (
                      <div key={p.id} className="p-3 flex items-center justify-between hover:bg-slate-50">
                        <div className="min-w-0 pr-2">
                          <button
                            onClick={() => handleEditPost(p)}
                            className="font-bold text-[#2271b1] hover:underline truncate block text-left"
                          >
                            {p.title}
                          </button>
                          <span className="text-[11px] text-slate-500">{p.created_at.split(' ')[0]} · {p.category_name}</span>
                        </div>
                        <span className={`px-2 py-0.5 rounded text-[10px] font-semibold shrink-0 ${
                          p.status === 'publish' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'
                        }`}>
                          {p.status === 'publish' ? 'Đã xuất bản' : 'Bản nháp'}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* TAB 2: POSTS LIST */}
          {activeTab === 'posts' && (
            <div className="space-y-4">
              <div className="flex items-center justify-between pb-3 border-b border-[#c3c4c7]">
                <div className="flex items-center gap-3">
                  <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327]">Tất cả bài viết</h1>
                  <button
                    onClick={() => {
                      setEditingPostId(null);
                      setPostForm({
                        title: '',
                        slug: '',
                        content: '',
                        excerpt: '',
                        category_id: categories[0]?.id || 1,
                        featured_image: '',
                        tags: '',
                        status: 'publish',
                        is_featured: false,
                        seo_title: '',
                        seo_description: ''
                      });
                      setActiveTab('post-new');
                    }}
                    className="px-2.5 py-1 border border-[#2271b1] bg-[#f6f7f7] text-[#2271b1] rounded text-xs font-semibold hover:bg-slate-200"
                  >
                    Viết bài mới
                  </button>
                </div>
              </div>

              {/* Status Tabs */}
              <div className="flex items-center gap-2 text-xs text-[#646970]">
                <button
                  onClick={() => setPostStatusFilter('all')}
                  className={`${postStatusFilter === 'all' ? 'font-bold text-black' : 'text-[#2271b1] hover:underline'}`}
                >
                  Tất cả ({posts.length})
                </button>
                <span>|</span>
                <button
                  onClick={() => setPostStatusFilter('publish')}
                  className={`${postStatusFilter === 'publish' ? 'font-bold text-black' : 'text-[#2271b1] hover:underline'}`}
                >
                  Đã xuất bản ({posts.filter(p => p.status === 'publish').length})
                </button>
                <span>|</span>
                <button
                  onClick={() => setPostStatusFilter('draft')}
                  className={`${postStatusFilter === 'draft' ? 'font-bold text-black' : 'text-[#2271b1] hover:underline'}`}
                >
                  Bản nháp ({posts.filter(p => p.status === 'draft').length})
                </button>
              </div>

              {/* Filter bar */}
              <div className="flex flex-col sm:flex-row items-center justify-between gap-3 bg-white p-3 border border-[#c3c4c7] rounded-sm">
                <div className="flex items-center gap-2 w-full sm:w-auto">
                  <select
                    value={postCatFilter}
                    onChange={e => setPostCatFilter(Number(e.target.value))}
                    className="px-2.5 py-1 border border-[#8c8f94] rounded text-xs bg-white"
                  >
                    <option value={0}>Tất cả chuyên mục</option>
                    {categories.map(c => (
                      <option key={c.id} value={c.id}>{c.name}</option>
                    ))}
                  </select>
                </div>
                <div className="flex items-center gap-2 w-full sm:w-auto">
                  <input
                    type="search"
                    placeholder="Tìm bài viết..."
                    value={postSearch}
                    onChange={e => setPostSearch(e.target.value)}
                    className="px-2.5 py-1 border border-[#8c8f94] rounded text-xs w-full sm:w-48"
                  />
                </div>
              </div>

              {/* WordPress widefat Table */}
              <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm overflow-x-auto">
                <table className="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr className="bg-[#f6f7f7] border-b border-[#c3c4c7] text-[#2c3338] font-semibold">
                      <th className="p-3">Tiêu đề bài viết</th>
                      <th className="p-3">Tác giả</th>
                      <th className="p-3">Chuyên mục</th>
                      <th className="p-3">Thẻ</th>
                      <th className="p-3">Lượt xem</th>
                      <th className="p-3">Ngày đăng</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#f0f0f1]">
                    {posts
                      .filter(p => {
                        if (postStatusFilter !== 'all' && p.status !== postStatusFilter) return false;
                        if (postCatFilter > 0 && p.category_id !== postCatFilter) return false;
                        if (postSearch && !p.title.toLowerCase().includes(postSearch.toLowerCase())) return false;
                        return true;
                      })
                      .map(p => (
                        <tr key={p.id} className="hover:bg-[#fcfcfc] group">
                          <td className="p-3">
                            <div className="font-bold text-[#2271b1] hover:underline cursor-pointer" onClick={() => handleEditPost(p)}>
                              {p.title}
                              {p.status !== 'publish' && <span className="ml-2 text-slate-500 font-normal">({p.status})</span>}
                              {p.is_featured && <span className="ml-2 text-amber-500 text-[11px]">★ Nổi bật</span>}
                            </div>
                            <div className="flex items-center gap-2 mt-1 text-[11px] text-[#2271b1]">
                              <button onClick={() => handleEditPost(p)} className="hover:underline">Chỉnh sửa</button>
                              <span>|</span>
                              <button onClick={() => handleDeletePost(p.id)} className="text-[#b32d2e] hover:underline">Xóa</button>
                            </div>
                          </td>
                          <td className="p-3 text-slate-600">{p.author_name}</td>
                          <td className="p-3 text-slate-600">{p.category_name}</td>
                          <td className="p-3 text-slate-500">{p.tags.join(', ') || '—'}</td>
                          <td className="p-3 font-mono">{p.views_count.toLocaleString()}</td>
                          <td className="p-3 text-slate-500">{p.created_at.split(' ')[0]}</td>
                        </tr>
                      ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* TAB 3: POST EDITOR (WYSIWYG + METABOXES) */}
          {activeTab === 'post-new' && (
            <form onSubmit={handleSavePost} className="space-y-4">
              <div className="flex items-center justify-between pb-3 border-b border-[#c3c4c7]">
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => setActiveTab('posts')}
                    className="p-1 text-slate-500 hover:text-black"
                  >
                    <ArrowLeft className="w-4 h-4" />
                  </button>
                  <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327]">
                    {editingPostId ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới'}
                  </h1>
                </div>
                <div className="flex items-center gap-2">
                  <button
                    type="button"
                    onClick={() => setPreviewModalOpen(true)}
                    className="px-3 py-1.5 border border-[#8c8f94] bg-[#f6f7f7] rounded font-semibold text-xs hover:bg-slate-200"
                  >
                    Xem trước
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-1.5 bg-[#2271b1] text-white rounded font-bold text-xs hover:bg-[#135e96] shadow-sm"
                  >
                    {editingPostId ? 'Cập nhật bài viết' : 'Xuất bản bài viết'}
                  </button>
                </div>
              </div>

              {/* 2 Columns: Main Editor (Left) & Settings Sidebar (Right) */}
              <div className="grid grid-cols-1 lg:grid-cols-12 gap-5">
                {/* Main Content Pane */}
                <div className="lg:col-span-8 space-y-4">
                  {/* Title & Slug */}
                  <div className="bg-white p-4 border border-[#c3c4c7] shadow-sm rounded-sm space-y-2">
                    <input
                      type="text"
                      value={postForm.title}
                      onChange={e => {
                        const val = e.target.value;
                        setPostForm({
                          ...postForm,
                          title: val,
                          slug: !editingPostId ? slugify(val) : postForm.slug
                        });
                      }}
                      placeholder="Thêm tiêu đề bài viết tại đây..."
                      required
                      className="w-full px-3 py-2 text-base font-bold border border-[#8c8f94] rounded focus:border-[#2271b1] focus:outline-none"
                    />
                    <div className="flex items-center gap-1.5 text-[11px] text-slate-500">
                      <span className="font-semibold">Đường dẫn tĩnh (Slug):</span>
                      <span className="text-slate-400">https://tonyhoaivu.com/post/</span>
                      <input
                        type="text"
                        value={postForm.slug}
                        onChange={e => setPostForm({ ...postForm, slug: slugify(e.target.value) })}
                        className="px-1.5 py-0.5 border border-slate-300 rounded font-mono text-[11px] text-blue-600 focus:outline-none"
                      />
                    </div>
                  </div>

                  {/* WYSIWYG Editor */}
                  <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                    <div className="px-4 py-2 border-b border-[#c3c4c7] bg-[#f6f7f7] flex items-center justify-between">
                      <div className="font-semibold text-xs text-[#1d2327]">Nội dung bài viết</div>
                      <div className="flex gap-1">
                        <button
                          type="button"
                          onClick={() => setEditorMode('visual')}
                          className={`px-2.5 py-1 text-xs rounded ${editorMode === 'visual' ? 'bg-[#2271b1] text-white font-bold' : 'bg-white border border-slate-300'}`}
                        >
                          Trực quan (Visual)
                        </button>
                        <button
                          type="button"
                          onClick={() => setEditorMode('html')}
                          className={`px-2.5 py-1 text-xs rounded ${editorMode === 'html' ? 'bg-[#2271b1] text-white font-bold' : 'bg-white border border-slate-300'}`}
                        >
                          Văn bản HTML
                        </button>
                      </div>
                    </div>

                    {/* Toolbar */}
                    {editorMode === 'visual' && (
                      <div className="p-2 border-b border-[#c3c4c7] bg-[#fafafa] flex flex-wrap gap-1 text-xs">
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<strong>In đậm</strong>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 font-bold"
                          title="In đậm"
                        >
                          B
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<em>In nghiêng</em>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 italic"
                          title="In nghiêng"
                        >
                          I
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<h2>Tiêu đề H2</h2>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 font-semibold"
                        >
                          H2
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<h3>Tiêu đề H3</h3>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 font-semibold"
                        >
                          H3
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<blockquote>Trích dẫn khối</blockquote>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100"
                        >
                          "Quote"
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<pre><code>// Code snippet</code></pre>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 font-mono"
                        >
                          &lt;/&gt;
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setPostForm({ ...postForm, content: postForm.content + '<ul><li>Mục 1</li><li>Mục 2</li></ul>' });
                          }}
                          className="px-2 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100"
                        >
                          • Danh sách
                        </button>
                      </div>
                    )}

                    {/* Content Area */}
                    <div className="p-3">
                      <textarea
                        rows={16}
                        value={postForm.content}
                        onChange={e => setPostForm({ ...postForm, content: e.target.value })}
                        placeholder="Viết nội dung bài viết bằng HTML hoặc văn bản phong phú..."
                        className="w-full p-3 font-sans text-xs leading-relaxed focus:outline-none"
                      />
                    </div>
                  </div>

                  {/* Excerpt Box */}
                  <div className="bg-white border border-[#c3c4c7] p-4 shadow-sm rounded-sm">
                    <label className="block font-semibold text-xs text-[#1d2327] mb-1">Tóm tắt ngắn (Excerpt)</label>
                    <textarea
                      rows={2}
                      value={postForm.excerpt}
                      onChange={e => setPostForm({ ...postForm, excerpt: e.target.value })}
                      placeholder="Đoạn văn ngắn hiển thị ngoài danh sách bài viết..."
                      className="w-full px-3 py-2 border border-[#8c8f94] rounded text-xs focus:border-[#2271b1] focus:outline-none"
                    />
                  </div>

                  {/* SEO Box */}
                  <div className="bg-white border border-[#c3c4c7] p-4 shadow-sm rounded-sm space-y-3">
                    <div className="font-semibold text-xs text-[#1d2327]">Tối ưu hóa SEO (Google Search)</div>
                    <div>
                      <label className="block text-[11px] text-slate-600 mb-1">Tiêu đề SEO</label>
                      <input
                        type="text"
                        value={postForm.seo_title}
                        onChange={e => setPostForm({ ...postForm, seo_title: e.target.value })}
                        placeholder="Tiêu đề hiển thị trên công cụ tìm kiếm..."
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs focus:border-[#2271b1] focus:outline-none"
                      />
                    </div>
                    <div>
                      <label className="block text-[11px] text-slate-600 mb-1">Mô tả SEO (Meta Description)</label>
                      <textarea
                        rows={2}
                        value={postForm.seo_description}
                        onChange={e => setPostForm({ ...postForm, seo_description: e.target.value })}
                        placeholder="Đoạn mô tả ngắn dưới kết quả tìm kiếm..."
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs focus:border-[#2271b1] focus:outline-none"
                      />
                    </div>
                  </div>
                </div>

                {/* Settings Sidebar Column */}
                <div className="lg:col-span-4 space-y-4">
                  {/* Publish Status Box */}
                  <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                    <div className="px-4 py-2.5 border-b border-[#c3c4c7] font-semibold text-xs text-[#1d2327]">
                      Xuất bản
                    </div>
                    <div className="p-4 space-y-3">
                      <div>
                        <label className="block font-semibold text-xs mb-1">Trạng thái:</label>
                        <select
                          value={postForm.status}
                          onChange={e => setPostForm({ ...postForm, status: e.target.value as any })}
                          className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs bg-white"
                        >
                          <option value="publish">Công khai (Published)</option>
                          <option value="draft">Bản nháp (Draft)</option>
                          <option value="private">Riêng tư (Private)</option>
                        </select>
                      </div>
                      <div className="pt-2">
                        <label className="flex items-center gap-2 cursor-pointer text-xs">
                          <input
                            type="checkbox"
                            checked={postForm.is_featured}
                            onChange={e => setPostForm({ ...postForm, is_featured: e.target.checked })}
                          />
                          <span>Đặt làm bài viết nổi bật (Featured)</span>
                        </label>
                      </div>
                    </div>
                  </div>

                  {/* Categories Box */}
                  <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                    <div className="px-4 py-2.5 border-b border-[#c3c4c7] font-semibold text-xs text-[#1d2327]">
                      Chuyên mục
                    </div>
                    <div className="p-4 space-y-2 max-h-48 overflow-y-auto">
                      {categories.map(c => (
                        <label key={c.id} className="flex items-center gap-2 cursor-pointer text-xs">
                          <input
                            type="radio"
                            name="category_select"
                            value={c.id}
                            checked={Number(postForm.category_id) === c.id}
                            onChange={() => setPostForm({ ...postForm, category_id: c.id })}
                          />
                          <span>{c.name}</span>
                        </label>
                      ))}
                    </div>
                  </div>

                  {/* Featured Image Box */}
                  <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                    <div className="px-4 py-2.5 border-b border-[#c3c4c7] font-semibold text-xs text-[#1d2327]">
                      Ảnh đại diện
                    </div>
                    <div className="p-4 space-y-3">
                      {postForm.featured_image ? (
                        <img
                          src={postForm.featured_image}
                          alt=""
                          className="w-full h-32 object-cover rounded border"
                          referrerPolicy="no-referrer"
                        />
                      ) : (
                        <div className="w-full h-24 bg-slate-100 flex items-center justify-center text-slate-400 text-xs border rounded">
                          Chưa có ảnh đại diện
                        </div>
                      )}
                      <input
                        type="url"
                        value={postForm.featured_image}
                        onChange={e => setPostForm({ ...postForm, featured_image: e.target.value })}
                        placeholder="Dán link ảnh (https://...)"
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs"
                      />
                    </div>
                  </div>

                  {/* Tags Box */}
                  <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm">
                    <div className="px-4 py-2.5 border-b border-[#c3c4c7] font-semibold text-xs text-[#1d2327]">
                      Thẻ từ khóa (Tags)
                    </div>
                    <div className="p-4">
                      <input
                        type="text"
                        value={postForm.tags}
                        onChange={e => setPostForm({ ...postForm, tags: e.target.value })}
                        placeholder="PHP, MySQL, Cloud, Architecture..."
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs"
                      />
                      <p className="text-[11px] text-slate-400 mt-1">Phân cách các thẻ bằng dấu phẩy</p>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          )}

          {/* TAB 4: CATEGORIES */}
          {activeTab === 'categories' && (
            <div className="space-y-4">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Chuyên mục bài viết
              </h1>

              {/* 2-Column WordPress Layout */}
              <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
                {/* Add Category Form (Left) */}
                <div className="md:col-span-4 bg-white p-4 border border-[#c3c4c7] shadow-sm rounded-sm">
                  <h3 className="font-bold text-xs uppercase tracking-wider text-[#1d2327] mb-3">Thêm chuyên mục mới</h3>
                  <form onSubmit={handleAddCategory} className="space-y-3">
                    <div>
                      <label className="block text-xs font-semibold mb-1">Tên chuyên mục *</label>
                      <input
                        type="text"
                        value={newCat.name}
                        onChange={e => setNewCat({ ...newCat, name: e.target.value })}
                        required
                        placeholder="Ví dụ: AI &amp; Machine Learning"
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs"
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-semibold mb-1">Đường dẫn (Slug)</label>
                      <input
                        type="text"
                        value={newCat.slug}
                        onChange={e => setNewCat({ ...newCat, slug: slugify(e.target.value) })}
                        placeholder="ai-machine-learning"
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-semibold mb-1">Mô tả</label>
                      <textarea
                        rows={3}
                        value={newCat.description}
                        onChange={e => setNewCat({ ...newCat, description: e.target.value })}
                        placeholder="Mô tả tóm tắt nội dung..."
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs"
                      />
                    </div>
                    <button
                      type="submit"
                      className="px-4 py-2 bg-[#2271b1] text-white font-bold text-xs rounded hover:bg-[#135e96]"
                    >
                      Thêm chuyên mục mới
                    </button>
                  </form>
                </div>

                {/* Categories Table (Right) */}
                <div className="md:col-span-8 bg-white border border-[#c3c4c7] shadow-sm rounded-sm overflow-x-auto">
                  <table className="w-full text-left border-collapse text-xs">
                    <thead>
                      <tr className="bg-[#f6f7f7] border-b border-[#c3c4c7] font-semibold text-[#2c3338]">
                        <th className="p-3">Tên</th>
                        <th className="p-3">Mô tả</th>
                        <th className="p-3">Đường dẫn</th>
                        <th className="p-3">Số bài</th>
                        <th className="p-3">Thao tác</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-[#f0f0f1]">
                      {categories.map(c => (
                        <tr key={c.id} className="hover:bg-[#fcfcfc]">
                          <td className="p-3 font-bold text-[#2271b1]">{c.name}</td>
                          <td className="p-3 text-slate-500 max-w-xs truncate">{c.description || '—'}</td>
                          <td className="p-3 font-mono text-[11px] text-slate-500">{c.slug}</td>
                          <td className="p-3 font-mono">
                            {posts.filter(p => p.category_id === c.id).length}
                          </td>
                          <td className="p-3">
                            <button
                              onClick={() => handleDeleteCategory(c.id)}
                              className="text-[#b32d2e] hover:underline"
                            >
                              Xóa
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* TAB 5: MEDIA LIBRARY */}
          {activeTab === 'media' && (
            <div className="space-y-6">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Thư viện Media
              </h1>

              {/* Upload Box */}
              <div className="bg-white p-5 border border-[#c3c4c7] shadow-sm rounded-sm">
                <h3 className="font-bold text-xs uppercase tracking-wider mb-3">Thêm tập tin hình ảnh mới</h3>
                <form onSubmit={handleAddMedia} className="flex flex-col sm:flex-row gap-3">
                  <input
                    type="url"
                    value={uploadUrlInput}
                    onChange={e => setUploadUrlInput(e.target.value)}
                    required
                    placeholder="URL hình ảnh (https://...)"
                    className="flex-1 px-3 py-2 border border-[#8c8f94] rounded text-xs"
                  />
                  <input
                    type="text"
                    value={uploadNameInput}
                    onChange={e => setUploadNameInput(e.target.value)}
                    placeholder="Tên tệp (ví dụ: hero-architecture.jpg)"
                    className="px-3 py-2 border border-[#8c8f94] rounded text-xs w-full sm:w-64"
                  />
                  <button
                    type="submit"
                    className="px-5 py-2 bg-[#2271b1] text-white font-bold text-xs rounded hover:bg-[#135e96] whitespace-nowrap"
                  >
                    Tải lên thư viện
                  </button>
                </form>
              </div>

              {/* Media Grid */}
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {media.map(m => (
                  <div key={m.id} className="bg-white border border-[#c3c4c7] rounded overflow-hidden shadow-sm flex flex-col justify-between">
                    <div className="aspect-video bg-slate-100 overflow-hidden">
                      <img src={m.url} alt={m.original_name} className="w-full h-full object-cover" referrerPolicy="no-referrer" />
                    </div>
                    <div className="p-3 text-xs">
                      <div className="font-semibold text-slate-800 truncate" title={m.original_name}>{m.original_name}</div>
                      <div className="text-[11px] text-slate-400 font-mono mt-0.5">{Math.round(m.file_size / 1024)} KB</div>
                      <div className="flex items-center gap-2 mt-2 pt-2 border-t border-slate-100">
                        <button
                          onClick={() => {
                            navigator.clipboard.writeText(m.url);
                            showAlert('Đã sao chép link ảnh vào clipboard!');
                          }}
                          className="text-[11px] text-[#2271b1] font-semibold hover:underline"
                        >
                          Sao chép URL
                        </button>
                        <span>·</span>
                        <button
                          onClick={() => {
                            onUpdateMedia(media.filter(item => item.id !== m.id));
                            showAlert('Đã xóa tệp media.');
                          }}
                          className="text-[11px] text-[#b32d2e] hover:underline"
                        >
                          Xóa
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 6: STATIC PAGES */}
          {activeTab === 'pages' && (
            <div className="space-y-4">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Quản lý Trang tĩnh
              </h1>
              <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm overflow-x-auto">
                <table className="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr className="bg-[#f6f7f7] border-b border-[#c3c4c7] font-semibold text-[#2c3338]">
                      <th className="p-3">Tiêu đề trang</th>
                      <th className="p-3">Đường dẫn</th>
                      <th className="p-3">Trạng thái</th>
                      <th className="p-3">Ngày tạo</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#f0f0f1]">
                    {pages.map(p => (
                      <tr key={p.id} className="hover:bg-[#fcfcfc]">
                        <td className="p-3 font-bold text-[#2271b1]">{p.title}</td>
                        <td className="p-3 font-mono text-slate-500">/page/{p.slug}</td>
                        <td className="p-3">
                          <span className="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-semibold text-[10px]">
                            {p.status}
                          </span>
                        </td>
                        <td className="p-3 text-slate-500">{p.created_at.split(' ')[0]}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* TAB 7: COMMENTS MODERATION */}
          {activeTab === 'comments' && (
            <div className="space-y-4">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Bình luận độc giả
              </h1>
              <div className="bg-white border border-[#c3c4c7] shadow-sm rounded-sm overflow-x-auto">
                <table className="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr className="bg-[#f6f7f7] border-b border-[#c3c4c7] font-semibold text-[#2c3338]">
                      <th className="p-3">Tác giả</th>
                      <th className="p-3">Nội dung bình luận</th>
                      <th className="p-3">Trong bài viết</th>
                      <th className="p-3">Trạng thái</th>
                      <th className="p-3">Hành động</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#f0f0f1]">
                    {comments.map(c => (
                      <tr key={c.id} className="hover:bg-[#fcfcfc]">
                        <td className="p-3">
                          <div className="font-bold">{c.author_name}</div>
                          <div className="text-[11px] text-slate-400">{c.author_email}</div>
                        </td>
                        <td className="p-3 max-w-xs">{c.content}</td>
                        <td className="p-3 font-medium text-[#2271b1]">{c.post_title}</td>
                        <td className="p-3">
                          <span className={`px-2 py-0.5 rounded text-[10px] font-semibold ${
                            c.status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'
                          }`}>
                            {c.status === 'approved' ? 'Đã duyệt' : 'Chờ duyệt'}
                          </span>
                        </td>
                        <td className="p-3 space-x-2">
                          {c.status === 'pending' ? (
                            <button
                              onClick={() => handleCommentStatus(c.id, 'approved')}
                              className="text-emerald-700 font-semibold hover:underline"
                            >
                              Duyệt
                            </button>
                          ) : (
                            <button
                              onClick={() => handleCommentStatus(c.id, 'pending')}
                              className="text-slate-500 hover:underline"
                            >
                              Bỏ duyệt
                            </button>
                          )}
                          <button
                            onClick={() => handleCommentStatus(c.id, 'trash')}
                            className="text-[#b32d2e] hover:underline"
                          >
                            Xóa
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* TAB 8: MENUS */}
          {activeTab === 'menus' && (
            <div className="space-y-6">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Menu điều hướng
              </h1>
              <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div className="md:col-span-5 bg-white p-4 border border-[#c3c4c7] shadow-sm rounded-sm">
                  <h3 className="font-bold text-xs uppercase tracking-wider mb-3">Thêm liên kết vào Menu</h3>
                  <form
                    onSubmit={e => {
                      e.preventDefault();
                      if (!newMenu.title || !newMenu.url) return;
                      onUpdateMenus([...menus, { ...newMenu, id: Date.now() }]);
                      setNewMenu({ title: '', url: '', target: '_self', position: 'header', sort_order: menus.length + 1 });
                      showAlert('Đã thêm liên kết vào menu!');
                    }}
                    className="space-y-3"
                  >
                    <div>
                      <label className="block text-xs font-semibold mb-1">Tên nhãn *</label>
                      <input
                        type="text"
                        value={newMenu.title}
                        onChange={e => setNewMenu({ ...newMenu, title: e.target.value })}
                        required
                        placeholder="Ví dụ: Khoá học"
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs"
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-semibold mb-1">Đường dẫn URL *</label>
                      <input
                        type="text"
                        value={newMenu.url}
                        onChange={e => setNewMenu({ ...newMenu, url: e.target.value })}
                        required
                        placeholder="/category/lap-trinh hoặc https://..."
                        className="w-full px-2.5 py-1.5 border border-[#8c8f94] rounded text-xs"
                      />
                    </div>
                    <button
                      type="submit"
                      className="px-4 py-2 bg-[#2271b1] text-white font-bold text-xs rounded hover:bg-[#135e96]"
                    >
                      Thêm vào Menu
                    </button>
                  </form>
                </div>

                <div className="md:col-span-7 bg-white border border-[#c3c4c7] shadow-sm rounded-sm overflow-x-auto">
                  <table className="w-full text-left border-collapse text-xs">
                    <thead>
                      <tr className="bg-[#f6f7f7] border-b border-[#c3c4c7] font-semibold text-[#2c3338]">
                        <th className="p-3">Nhãn</th>
                        <th className="p-3">Đường dẫn</th>
                        <th className="p-3">Thao tác</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-[#f0f0f1]">
                      {menus.map(m => (
                        <tr key={m.id} className="hover:bg-[#fcfcfc]">
                          <td className="p-3 font-bold text-slate-800">{m.title}</td>
                          <td className="p-3 font-mono text-slate-500">{m.url}</td>
                          <td className="p-3">
                            <button
                              onClick={() => {
                                onUpdateMenus(menus.filter(item => item.id !== m.id));
                                showAlert('Đã xóa mục menu.');
                              }}
                              className="text-[#b32d2e] hover:underline"
                            >
                              Xóa
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* TAB 9: USERS & PROFILE */}
          {activeTab === 'users' && (
            <div className="space-y-6">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Hồ sơ thành viên
              </h1>
              <div className="bg-white p-6 border border-[#c3c4c7] shadow-sm rounded-sm max-w-2xl space-y-4">
                <div className="flex items-center gap-4 pb-4 border-b">
                  <img
                    src={author.avatar}
                    alt=""
                    className="w-16 h-16 rounded-full object-cover"
                    referrerPolicy="no-referrer"
                  />
                  <div>
                    <h3 className="font-bold text-sm text-[#1d2327]">{author.display_name}</h3>
                    <p className="text-xs text-slate-500">Vai trò: Quản trị viên (Administrator)</p>
                  </div>
                </div>

                <form
                  onSubmit={e => {
                    e.preventDefault();
                    showAlert('Đã lưu thông tin hồ sơ thành công!');
                  }}
                  className="space-y-3"
                >
                  <div>
                    <label className="block text-xs font-semibold mb-1">Tên hiển thị</label>
                    <input
                      type="text"
                      value={author.display_name}
                      onChange={e => onUpdateAuthor({ ...author, display_name: e.target.value })}
                      className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-semibold mb-1">Địa chỉ Email</label>
                    <input
                      type="email"
                      value={author.email}
                      onChange={e => onUpdateAuthor({ ...author, email: e.target.value })}
                      className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-semibold mb-1">Tiểu sử tác giả (Bio)</label>
                    <textarea
                      rows={3}
                      value={author.bio}
                      onChange={e => onUpdateAuthor({ ...author, bio: e.target.value })}
                      className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs"
                    />
                  </div>
                  <button
                    type="submit"
                    className="px-4 py-2 bg-[#2271b1] text-white font-bold text-xs rounded hover:bg-[#135e96]"
                  >
                    Cập nhật hồ sơ
                  </button>
                </form>
              </div>
            </div>
          )}

          {/* TAB 10: SETTINGS */}
          {activeTab === 'settings' && (
            <div className="space-y-6">
              <h1 className="text-xl sm:text-2xl font-normal text-[#1d2327] pb-3 border-b border-[#c3c4c7]">
                Cài đặt tổng quan
              </h1>
              <form
                onSubmit={e => {
                  e.preventDefault();
                  showAlert('Đã lưu cấu hình website thành công!');
                }}
                className="bg-white p-6 border border-[#c3c4c7] shadow-sm rounded-sm max-w-2xl space-y-4"
              >
                <div>
                  <label className="block text-xs font-semibold mb-1">Tên Website</label>
                  <input
                    type="text"
                    value={settingsForm.siteTitle}
                    onChange={e => setSettingsForm({ ...settingsForm, siteTitle: e.target.value })}
                    className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs font-bold"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold mb-1">Khẩu hiệu (Tagline)</label>
                  <input
                    type="text"
                    value={settingsForm.siteTagline}
                    onChange={e => setSettingsForm({ ...settingsForm, siteTagline: e.target.value })}
                    className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold mb-1">Email quản trị</label>
                  <input
                    type="email"
                    value={settingsForm.adminEmail}
                    onChange={e => setSettingsForm({ ...settingsForm, adminEmail: e.target.value })}
                    className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold mb-1">Mô tả SEO mặc định (Meta Description)</label>
                  <textarea
                    rows={3}
                    value={settingsForm.seoDesc}
                    onChange={e => setSettingsForm({ ...settingsForm, seoDesc: e.target.value })}
                    className="w-full px-3 py-1.5 border border-[#8c8f94] rounded text-xs"
                  />
                </div>
                <button
                  type="submit"
                  className="px-5 py-2 bg-[#2271b1] text-white font-bold text-xs rounded hover:bg-[#135e96]"
                >
                  Lưu thay đổi
                </button>
              </form>
            </div>
          )}
        </main>
      </div>

      {/* Live Preview Modal */}
      {previewModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
          <div className="bg-white max-w-3xl w-full max-h-[85vh] overflow-y-auto rounded-xl p-6 shadow-2xl space-y-4">
            <div className="flex justify-between items-center border-b pb-3">
              <h3 className="font-bold text-sm text-[#1d2327]">Xem trước bài viết</h3>
              <button onClick={() => setPreviewModalOpen(false)} className="text-xl font-bold p-1">×</button>
            </div>
            <h1 className="text-2xl font-bold text-slate-900">{postForm.title || 'Tiêu đề xem trước'}</h1>
            {postForm.featured_image && (
              <img src={postForm.featured_image} alt="" className="w-full aspect-video object-cover rounded-lg" referrerPolicy="no-referrer" />
            )}
            <div
              className="article-content leading-relaxed text-slate-700"
              dangerouslySetInnerHTML={{ __html: postForm.content || '<p>Nội dung bài viết...</p>' }}
            />
          </div>
        </div>
      )}
    </div>
  );
};
