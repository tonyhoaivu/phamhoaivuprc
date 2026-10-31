import React, { useState, useMemo } from 'react';
import { Search, Moon, Sun, Clock, Eye, ArrowRight, Tag, BookOpen, Send, CheckCircle2, MessageSquare, ChevronRight, X } from 'lucide-react';
import { Post, Category, Comment, Page, User } from '../data/blogData';

interface FrontendBlogProps {
  posts: Post[];
  categories: Category[];
  comments: Comment[];
  pages: Page[];
  author: User;
  onAddComment: (comment: Omit<Comment, 'id' | 'created_at' | 'status'>) => void;
  onNavigateToAdmin: () => void;
  isDark: boolean;
  onToggleTheme: () => void;
}

export const FrontendBlog: React.FC<FrontendBlogProps> = ({
  posts,
  categories,
  comments,
  pages,
  author,
  onAddComment,
  onNavigateToAdmin,
  isDark,
  onToggleTheme
}) => {
  // Navigation states: 'home' | 'post' | 'page' | 'contact'
  const [currentView, setCurrentView] = useState<'home' | 'post' | 'page' | 'contact'>('home');
  const [activeSlug, setActiveSlug] = useState<string>('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [isSearchOpen, setIsSearchOpen] = useState<boolean>(false);

  // Comment Form State
  const [commentForm, setCommentForm] = useState({
    name: '',
    email: '',
    website: '',
    content: ''
  });
  const [commentSuccess, setCommentSuccess] = useState(false);

  // Contact Form State
  const [contactForm, setContactForm] = useState({
    name: '',
    email: '',
    subject: '',
    message: ''
  });
  const [contactSuccess, setContactSuccess] = useState(false);

  // Current active post
  const currentPost = useMemo(() => {
    if (currentView === 'post') {
      return posts.find(p => p.slug === activeSlug) || posts[0];
    }
    return null;
  }, [currentView, activeSlug, posts]);

  // Current active page
  const currentPage = useMemo(() => {
    if (currentView === 'page') {
      return pages.find(p => p.slug === activeSlug) || pages[0];
    }
    return null;
  }, [currentView, activeSlug, pages]);

  // Filtered posts for home
  const filteredPosts = useMemo(() => {
    return posts.filter(post => {
      if (post.status !== 'publish') return false;
      const matchesCategory = selectedCategory === 'all' || post.category_slug === selectedCategory;
      const matchesSearch = !searchQuery || 
        post.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        post.excerpt.toLowerCase().includes(searchQuery.toLowerCase()) ||
        post.tags.some(t => t.toLowerCase().includes(searchQuery.toLowerCase()));
      return matchesCategory && matchesSearch;
    });
  }, [posts, selectedCategory, searchQuery]);

  // Popular posts for sidebar
  const popularPosts = useMemo(() => {
    return [...posts]
      .filter(p => p.status === 'publish')
      .sort((a, b) => b.views_count - a.views_count)
      .slice(0, 4);
  }, [posts]);

  // Approved comments for current post
  const postComments = useMemo(() => {
    if (!currentPost) return [];
    return comments.filter(c => c.post_id === currentPost.id && c.status === 'approved');
  }, [currentPost, comments]);

  // Related posts
  const relatedPosts = useMemo(() => {
    if (!currentPost) return [];
    return posts.filter(p => p.category_id === currentPost.category_id && p.id !== currentPost.id && p.status === 'publish').slice(0, 3);
  }, [currentPost, posts]);

  const handleOpenPost = (slug: string) => {
    setActiveSlug(slug);
    setCurrentView('post');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleOpenPage = (slug: string) => {
    setActiveSlug(slug);
    setCurrentView('page');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleSubmitComment = (e: React.FormEvent) => {
    e.preventDefault();
    if (!currentPost || !commentForm.name || !commentForm.email || !commentForm.content) return;
    
    onAddComment({
      post_id: currentPost.id,
      post_title: currentPost.title,
      post_slug: currentPost.slug,
      author_name: commentForm.name,
      author_email: commentForm.email,
      author_website: commentForm.website,
      content: commentForm.content
    });

    setCommentForm({ name: '', email: '', website: '', content: '' });
    setCommentSuccess(true);
    setTimeout(() => setCommentSuccess(false), 4000);
  };

  const handleSubmitContact = (e: React.FormEvent) => {
    e.preventDefault();
    setContactSuccess(true);
    setContactForm({ name: '', email: '', subject: '', message: '' });
    setTimeout(() => setContactSuccess(false), 5000);
  };

  return (
    <div className="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-200">
      {/* Top Bar Navigation (Clean 3-Zone Contract) */}
      <header className="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-8">
          {/* Zone 1: Single text element wordmark */}
          <button
            onClick={() => { setCurrentView('home'); setSelectedCategory('all'); }}
            className="flex items-center gap-2.5 whitespace-nowrap shrink-0 group text-left"
          >
            <div className="w-9 h-9 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-extrabold text-base shadow-sm group-hover:scale-105 transition-transform">
              T
            </div>
            <div className="flex flex-col">
              <span className="text-lg font-bold tracking-tight text-slate-900 dark:text-white leading-tight">TonyHoaiVu</span>
              <span className="text-[10px] text-slate-500 dark:text-slate-400 font-medium tracking-wide">Software Engineer &amp; Blog</span>
            </div>
          </button>

          {/* Zone 2: 4-5 concise single-line nav links */}
          <nav className="hidden md:flex items-center gap-6 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300">
            <button
              onClick={() => { setCurrentView('home'); setSelectedCategory('all'); }}
              className={`hover:text-slate-900 dark:hover:text-white transition-colors whitespace-nowrap shrink-0 ${currentView === 'home' && selectedCategory === 'all' ? 'text-blue-600 dark:text-blue-400 font-bold' : ''}`}
            >
              Trang chủ
            </button>
            <button
              onClick={() => { setCurrentView('home'); setSelectedCategory('lap-trinh'); }}
              className={`hover:text-slate-900 dark:hover:text-white transition-colors whitespace-nowrap shrink-0 ${currentView === 'home' && selectedCategory === 'lap-trinh' ? 'text-blue-600 dark:text-blue-400 font-bold' : ''}`}
            >
              Lập trình
            </button>
            <button
              onClick={() => { setCurrentView('home'); setSelectedCategory('kien-truc-he-thong'); }}
              className={`hover:text-slate-900 dark:hover:text-white transition-colors whitespace-nowrap shrink-0 ${currentView === 'home' && selectedCategory === 'kien-truc-he-thong' ? 'text-blue-600 dark:text-blue-400 font-bold' : ''}`}
            >
              Kiến trúc
            </button>
            <button
              onClick={() => { setCurrentView('home'); setSelectedCategory('devops-cloud'); }}
              className={`hover:text-slate-900 dark:hover:text-white transition-colors whitespace-nowrap shrink-0 ${currentView === 'home' && selectedCategory === 'devops-cloud' ? 'text-blue-600 dark:text-blue-400 font-bold' : ''}`}
            >
              DevOps
            </button>
            <button
              onClick={() => handleOpenPage('gioi-thieu')}
              className={`hover:text-slate-900 dark:hover:text-white transition-colors whitespace-nowrap shrink-0 ${currentView === 'page' && activeSlug === 'gioi-thieu' ? 'text-blue-600 dark:text-blue-400 font-bold' : ''}`}
            >
              Giới thiệu
            </button>
            <button
              onClick={() => setCurrentView('contact')}
              className={`hover:text-slate-900 dark:hover:text-white transition-colors whitespace-nowrap shrink-0 ${currentView === 'contact' ? 'text-blue-600 dark:text-blue-400 font-bold' : ''}`}
            >
              Liên hệ
            </button>
          </nav>

          {/* Zone 3: 1 primary action & utility toggles */}
          <div className="flex items-center gap-2.5 shrink-0">
            <button
              onClick={() => setIsSearchOpen(true)}
              className="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              title="Tìm kiếm bài viết"
            >
              <Search className="w-4 h-4" />
            </button>

            <button
              onClick={onToggleTheme}
              className="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              title="Đổi giao diện Sáng / Tối"
            >
              {isDark ? <Sun className="w-4 h-4 text-amber-400" /> : <Moon className="w-4 h-4 text-slate-600" />}
            </button>

            <button
              onClick={onNavigateToAdmin}
              className="hidden sm:flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl hover:opacity-90 transition-opacity whitespace-nowrap shrink-0 shadow-sm"
            >
              <span>Vào Admin CMS</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </header>

      {/* Search Modal */}
      {isSearchOpen && (
        <div className="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-start justify-center pt-20 px-4">
          <div className="bg-white dark:bg-slate-900 w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div className="p-4 flex items-center border-b border-slate-100 dark:border-slate-800">
              <Search className="w-5 h-5 text-slate-400 mr-3 shrink-0" />
              <input
                type="text"
                value={searchQuery}
                onChange={e => setSearchQuery(e.target.value)}
                placeholder="Tìm bài viết: PHP 8, MySQL, Docker, Security..."
                className="w-full bg-transparent text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none text-sm"
                autoFocus
              />
              <button onClick={() => setIsSearchOpen(false)} className="p-1 text-slate-400 hover:text-slate-600">
                <X className="w-5 h-5" />
              </button>
            </div>
            {searchQuery && (
              <div className="p-3 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-950 flex justify-between">
                <span>Tìm thấy {filteredPosts.length} kết quả</span>
                <button
                  onClick={() => { setIsSearchOpen(false); setCurrentView('home'); }}
                  className="font-bold text-blue-600 hover:underline"
                >
                  Xem tất cả ngoài danh sách
                </button>
              </div>
            )}
          </div>
        </div>
      )}

      {/* Main Content Area */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        {/* VIEW 1: HOME PAGE */}
        {currentView === 'home' && (
          <div>
            {/* Hero Banner */}
            <div className="mb-10 p-8 sm:p-12 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl text-white shadow-xl relative overflow-hidden border border-slate-800">
              <div className="relative z-10 max-w-2xl">
                <span className="text-xs uppercase tracking-widest text-sky-400 font-semibold mb-2 block">
                  Blog Kỹ thuật &amp; Kiến trúc phần mềm
                </span>
                <h1 className="text-3xl sm:text-5xl font-extrabold tracking-tight mb-4 text-balance leading-tight">
                  TonyHoaiVu Blog
                </h1>
                <p className="text-slate-300 text-xs sm:text-sm leading-relaxed mb-6 font-normal">
                  Chia sẻ kinh nghiệm thực chiến về xây dựng hệ thống phần mềm, kiến trúc chịu tải cao, tối ưu cơ sở dữ liệu MySQL và giải pháp đám mây.
                </p>
                <div className="flex flex-wrap items-center gap-3">
                  <button
                    onClick={() => setSelectedCategory('lap-trinh')}
                    className="px-4 py-2 text-xs font-semibold bg-white text-slate-900 rounded-xl hover:bg-slate-100 transition-colors shadow-sm"
                  >
                    Khám phá bài viết &rarr;
                  </button>
                  <button
                    onClick={() => handleOpenPage('gioi-thieu')}
                    className="px-4 py-2 text-xs font-semibold bg-slate-800/80 text-white rounded-xl hover:bg-slate-700 transition-colors border border-slate-700"
                  >
                    Về tác giả Tony Hoài Vũ
                  </button>
                </div>
              </div>
              <div className="absolute -right-12 -bottom-12 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl pointer-events-none" />
            </div>

            {/* Filter segmented buttons */}
            <div className="flex flex-wrap items-center gap-2 mb-8">
              <button
                onClick={() => setSelectedCategory('all')}
                className={`px-3.5 py-1.5 text-xs font-semibold rounded-xl transition-all ${
                  selectedCategory === 'all'
                    ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm'
                    : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100'
                }`}
              >
                Tất cả bài viết ({posts.filter(p => p.status === 'publish').length})
              </button>
              {categories.map(cat => (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.slug)}
                  className={`px-3.5 py-1.5 text-xs font-semibold rounded-xl transition-all ${
                    selectedCategory === cat.slug
                      ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm'
                      : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-100'
                  }`}
                >
                  {cat.name} ({posts.filter(p => p.category_id === cat.id && p.status === 'publish').length})
                </button>
              ))}
            </div>

            {/* 2-Column Split: Posts Grid (Left) + Sidebar (Right) */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
              {/* Posts Column */}
              <div className="lg:col-span-8">
                {filteredPosts.length === 0 ? (
                  <div className="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-500 text-sm">
                    Không tìm thấy bài viết nào phù hợp với bộ lọc hiện tại.
                  </div>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {filteredPosts.map(post => (
                      <article
                        key={post.id}
                        className="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
                      >
                        {/* Featured Image */}
                        <div
                          onClick={() => handleOpenPost(post.slug)}
                          className="aspect-video w-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative cursor-pointer"
                        >
                          <img
                            src={post.featured_image}
                            alt={post.title}
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy"
                            referrerPolicy="no-referrer"
                          />
                          {post.is_featured && (
                            <span className="absolute top-3 left-3 bg-blue-600 text-white text-[11px] font-semibold px-2.5 py-0.5 rounded-lg shadow-sm">
                              Nổi bật
                            </span>
                          )}
                        </div>

                        {/* Card Content */}
                        <div className="p-5 flex-1 flex flex-col justify-between">
                          <div>
                            {/* Unboxed metadata with typographic separators */}
                            <div className="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-2">
                              <span className="font-semibold text-blue-600 dark:text-blue-400">{post.category_name}</span>
                              <span aria-hidden="true">·</span>
                              <span>{post.created_at.split(' ')[0]}</span>
                              <span aria-hidden="true">·</span>
                              <span className="flex items-center gap-1 font-mono text-[11px]">
                                <Eye className="w-3 h-3" /> {post.views_count}
                              </span>
                            </div>

                            {/* Headline */}
                            <h3
                              onClick={() => handleOpenPost(post.slug)}
                              className="text-base font-bold text-slate-900 dark:text-white leading-snug mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2 cursor-pointer"
                            >
                              {post.title}
                            </h3>

                            {/* Excerpt */}
                            <p className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3 mb-4">
                              {post.excerpt}
                            </p>
                          </div>

                          {/* Footer */}
                          <div className="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <div className="flex items-center gap-2">
                              <img
                                src={post.author_avatar}
                                alt={post.author_name}
                                className="w-6 h-6 rounded-full object-cover"
                                referrerPolicy="no-referrer"
                              />
                              <span className="font-medium text-slate-700 dark:text-slate-300">{post.author_name}</span>
                            </div>
                            <button
                              onClick={() => handleOpenPost(post.slug)}
                              className="text-blue-600 dark:text-blue-400 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-1"
                            >
                              Đọc bài &rarr;
                            </button>
                          </div>
                        </div>
                      </article>
                    ))}
                  </div>
                )}
              </div>

              {/* Sidebar Column */}
              <div className="lg:col-span-4 space-y-6">
                {/* Author Card */}
                <div className="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                  <div className="flex items-center gap-4 mb-4">
                    <img
                      src={author.avatar}
                      alt={author.display_name}
                      className="w-14 h-14 rounded-full object-cover ring-2 ring-blue-500/20"
                      referrerPolicy="no-referrer"
                    />
                    <div>
                      <h4 className="font-bold text-slate-900 dark:text-white text-base leading-snug">{author.display_name}</h4>
                      <span className="text-xs text-slate-500 dark:text-slate-400">Senior Full-stack Engineer</span>
                    </div>
                  </div>
                  <p className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                    {author.bio}
                  </p>
                  <button
                    onClick={() => handleOpenPage('gioi-thieu')}
                    className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1"
                  >
                    Xem hồ sơ chi tiết &rarr;
                  </button>
                </div>

                {/* Popular Posts */}
                <div className="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                  <h4 className="font-bold text-sm text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                    <span>Bài viết nổi bật</span>
                    <span className="text-[11px] text-slate-400 font-normal">Lượt xem</span>
                  </h4>
                  <div className="space-y-4">
                    {popularPosts.map(p => (
                      <div
                        key={p.id}
                        onClick={() => handleOpenPost(p.slug)}
                        className="group flex gap-3 items-start cursor-pointer"
                      >
                        <img
                          src={p.featured_image}
                          alt={p.title}
                          className="w-16 h-14 rounded-lg object-cover shrink-0 group-hover:opacity-90"
                          referrerPolicy="no-referrer"
                        />
                        <div className="min-w-0">
                          <h5 className="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 line-clamp-2 leading-snug mb-1">
                            {p.title}
                          </h5>
                          <div className="flex items-center gap-2 text-[11px] text-slate-400">
                            <span>{p.created_at.split(' ')[0]}</span>
                            <span>·</span>
                            <span className="font-mono">{p.views_count.toLocaleString()} xem</span>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>

                {/* Categories Widget */}
                <div className="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                  <h4 className="font-bold text-sm text-slate-900 dark:text-white mb-3">Chuyên mục</h4>
                  <div className="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    {categories.map(cat => (
                      <button
                        key={cat.id}
                        onClick={() => setSelectedCategory(cat.slug)}
                        className="w-full py-2.5 flex justify-between items-center text-slate-600 dark:text-slate-300 hover:text-blue-600 transition-colors text-left"
                      >
                        <span>{cat.name}</span>
                        <span className="px-2 py-0.5 rounded-full text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-500 font-mono">
                          {posts.filter(p => p.category_id === cat.id && p.status === 'publish').length}
                        </span>
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* VIEW 2: SINGLE POST DETAIL */}
        {currentView === 'post' && currentPost && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <article className="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-sm">
              {/* Back to Home Breadcrumb */}
              <button
                onClick={() => setCurrentView('home')}
                className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline mb-6 flex items-center gap-1.5"
              >
                &larr; Quay lại danh sách bài viết
              </button>

              <h1 className="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-6">
                {currentPost.title}
              </h1>

              {/* Meta bar */}
              <div className="flex flex-wrap items-center justify-between pb-6 mb-8 border-b border-slate-100 dark:border-slate-800 text-xs text-slate-500 gap-4">
                <div className="flex items-center gap-3">
                  <img
                    src={currentPost.author_avatar}
                    alt={currentPost.author_name}
                    className="w-9 h-9 rounded-full object-cover"
                    referrerPolicy="no-referrer"
                  />
                  <div>
                    <div className="font-bold text-slate-800 dark:text-slate-200">{currentPost.author_name}</div>
                    <div className="flex items-center gap-2 text-[11px] text-slate-400">
                      <span>Đăng ngày {currentPost.created_at.split(' ')[0]}</span>
                      <span>·</span>
                      <span className="font-medium text-blue-600">{currentPost.category_name}</span>
                    </div>
                  </div>
                </div>
                <div className="flex items-center gap-3 font-mono text-xs text-slate-400">
                  <span>{currentPost.views_count.toLocaleString()} lượt xem</span>
                </div>
              </div>

              {/* Featured Image */}
              <div className="mb-8 rounded-2xl overflow-hidden aspect-video bg-slate-100 dark:bg-slate-800">
                <img
                  src={currentPost.featured_image}
                  alt={currentPost.title}
                  className="w-full h-full object-cover"
                  referrerPolicy="no-referrer"
                />
              </div>

              {/* Article Content Rendered */}
              <div
                className="article-content max-w-none text-slate-700 dark:text-slate-200 text-base leading-relaxed space-y-4"
                dangerouslySetInnerHTML={{ __html: currentPost.content }}
              />

              {/* Tags */}
              <div className="mt-10 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 flex-wrap">
                <span className="text-xs font-semibold text-slate-400">Từ khóa:</span>
                {currentPost.tags.map(tag => (
                  <span
                    key={tag}
                    className="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300"
                  >
                    #{tag}
                  </span>
                ))}
              </div>

              {/* Author Box */}
              <div className="mt-12 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <img
                  src={currentPost.author_avatar}
                  alt={currentPost.author_name}
                  className="w-16 h-16 rounded-full object-cover shrink-0"
                  referrerPolicy="no-referrer"
                />
                <div>
                  <h4 className="font-bold text-slate-900 dark:text-white text-sm mb-1">{currentPost.author_name}</h4>
                  <p className="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-3">
                    {author.bio}
                  </p>
                  <button
                    onClick={() => handleOpenPage('gioi-thieu')}
                    className="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline"
                  >
                    Đọc thêm về tác giả &rarr;
                  </button>
                </div>
              </div>

              {/* Related Posts */}
              {relatedPosts.length > 0 && (
                <div className="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800">
                  <h3 className="font-bold text-slate-900 dark:text-white text-base mb-4">Bài viết cùng chuyên mục</h3>
                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {relatedPosts.map(rel => (
                      <div
                        key={rel.id}
                        onClick={() => handleOpenPost(rel.slug)}
                        className="group block p-3 rounded-xl border border-slate-100 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-all cursor-pointer"
                      >
                        <img
                          src={rel.featured_image}
                          alt={rel.title}
                          className="w-full h-24 object-cover rounded-lg mb-2"
                          referrerPolicy="no-referrer"
                        />
                        <h4 className="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 line-clamp-2 leading-snug">
                          {rel.title}
                        </h4>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Comments Section */}
              <section className="mt-14 pt-8 border-t border-slate-100 dark:border-slate-800">
                <h3 className="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                  <MessageSquare className="w-5 h-5 text-blue-600" />
                  <span>Bình luận độc giả ({postComments.length})</span>
                </h3>

                {commentSuccess && (
                  <div className="p-3.5 mb-6 bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs flex items-center gap-2">
                    <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                    <span>Bình luận của bạn đã được ghi nhận và hiển thị thành công!</span>
                  </div>
                )}

                {/* Comment Form */}
                <form onSubmit={handleSubmitComment} className="mb-10 p-6 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                  <h4 className="font-bold text-xs uppercase tracking-wider text-slate-700 dark:text-slate-300">Gửi bình luận của bạn</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Họ và tên *</label>
                      <input
                        type="text"
                        value={commentForm.name}
                        onChange={e => setCommentForm({ ...commentForm, name: e.target.value })}
                        required
                        placeholder="Nguyễn Văn A"
                        className="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Email * (bảo mật)</label>
                      <input
                        type="email"
                        value={commentForm.email}
                        onChange={e => setCommentForm({ ...commentForm, email: e.target.value })}
                        required
                        placeholder="email@example.com"
                        className="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nội dung bình luận *</label>
                    <textarea
                      rows={3}
                      value={commentForm.content}
                      onChange={e => setCommentForm({ ...commentForm, content: e.target.value })}
                      required
                      placeholder="Chia sẻ quan điểm hoặc đặt câu hỏi kỹ thuật..."
                      className="w-full px-3 py-2 text-xs rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <button
                    type="submit"
                    className="px-5 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-xl hover:opacity-90 transition-opacity"
                  >
                    Đăng bình luận
                  </button>
                </form>

                {/* Comments List */}
                <div className="space-y-4">
                  {postComments.length === 0 ? (
                    <p className="text-xs text-slate-500 italic">Chưa có bình luận nào cho bài viết này.</p>
                  ) : (
                    postComments.map(cm => (
                      <div key={cm.id} className="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-xs">
                        <div className="flex items-center justify-between mb-2">
                          <span className="font-bold text-slate-900 dark:text-white">{cm.author_name}</span>
                          <span className="text-[11px] text-slate-400">{cm.created_at.split(' ')[0]}</span>
                        </div>
                        <p className="text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{cm.content}</p>
                      </div>
                    ))
                  )}
                </div>
              </section>
            </article>

            {/* Sidebar */}
            <div className="lg:col-span-4 space-y-6">
              {/* Popular Posts */}
              <div className="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                <h4 className="font-bold text-sm text-slate-900 dark:text-white mb-4">Bài viết phổ biến</h4>
                <div className="space-y-4">
                  {popularPosts.map(p => (
                    <div
                      key={p.id}
                      onClick={() => handleOpenPost(p.slug)}
                      className="group flex gap-3 items-start cursor-pointer"
                    >
                      <img
                        src={p.featured_image}
                        alt={p.title}
                        className="w-14 h-12 rounded-lg object-cover shrink-0"
                        referrerPolicy="no-referrer"
                      />
                      <div className="min-w-0">
                        <h5 className="text-xs font-semibold text-slate-800 dark:text-slate-200 group-hover:text-blue-600 line-clamp-2 leading-snug mb-1">
                          {p.title}
                        </h5>
                        <div className="text-[11px] text-slate-400 font-mono">{p.views_count.toLocaleString()} lượt xem</div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* VIEW 3: STATIC PAGE (ABOUT) */}
        {currentView === 'page' && currentPage && (
          <div className="max-w-4xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-14 shadow-sm">
            <button
              onClick={() => setCurrentView('home')}
              className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline mb-6 flex items-center gap-1.5"
            >
              &larr; Về trang chủ
            </button>
            <h1 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-8 pb-4 border-b border-slate-100 dark:border-slate-800">
              {currentPage.title}
            </h1>
            <div
              className="article-content max-w-none text-slate-700 dark:text-slate-200 text-base leading-relaxed space-y-4"
              dangerouslySetInnerHTML={{ __html: currentPage.content }}
            />
          </div>
        )}

        {/* VIEW 4: CONTACT PAGE */}
        {currentView === 'contact' && (
          <div className="max-w-4xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-14 shadow-sm">
            <button
              onClick={() => setCurrentView('home')}
              className="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline mb-6 flex items-center gap-1.5"
            >
              &larr; Về trang chủ
            </button>
            <div className="mb-8 text-center max-w-xl mx-auto">
              <span className="text-xs uppercase tracking-widest text-blue-600 dark:text-blue-400 font-semibold block mb-1">Kết nối</span>
              <h1 className="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">Liên hệ hợp tác</h1>
              <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                Bạn có câu hỏi kỹ thuật hoặc muốn trao đổi về dự án kiến trúc hệ thống? Gửi tin nhắn qua biểu mẫu bên dưới.
              </p>
            </div>

            {contactSuccess && (
              <div className="p-4 mb-6 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-800 rounded-2xl text-xs sm:text-sm flex items-center gap-2">
                <CheckCircle2 className="w-5 h-5 text-emerald-500 shrink-0" />
                <span>Cảm ơn bạn! Tin nhắn đã được gửi thành công đến Tony Hoài Vũ. Tôi sẽ phản hồi sớm nhất qua email.</span>
              </div>
            )}

            <form onSubmit={handleSubmitContact} className="space-y-5">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Họ và tên *</label>
                  <input
                    type="text"
                    value={contactForm.name}
                    onChange={e => setContactForm({ ...contactForm, name: e.target.value })}
                    required
                    placeholder="Nguyễn Văn A"
                    className="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Địa chỉ Email *</label>
                  <input
                    type="email"
                    value={contactForm.email}
                    onChange={e => setContactForm({ ...contactForm, email: e.target.value })}
                    required
                    placeholder="email@example.com"
                    className="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Chủ đề tin nhắn *</label>
                <input
                  type="text"
                  value={contactForm.subject}
                  onChange={e => setContactForm({ ...contactForm, subject: e.target.value })}
                  required
                  placeholder="Hợp tác kỹ thuật / Tư vấn giải pháp..."
                  className="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div>
                <label className="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nội dung chi tiết *</label>
                <textarea
                  rows={5}
                  value={contactForm.message}
                  onChange={e => setContactForm({ ...contactForm, message: e.target.value })}
                  required
                  placeholder="Viết tin nhắn của bạn..."
                  className="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
              </div>
              <div className="text-right">
                <button
                  type="submit"
                  className="px-7 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs sm:text-sm rounded-xl hover:opacity-90 transition-opacity shadow-md"
                >
                  Gửi tin nhắn liên hệ
                </button>
              </div>
            </form>
          </div>
        )}
      </div>

      {/* Footer */}
      <footer className="bg-slate-900 text-slate-300 border-t border-slate-800 mt-20 pt-14 pb-12 text-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
            <div className="md:col-span-2 space-y-4">
              <div className="flex items-center gap-2">
                <span className="w-8 h-8 rounded-lg bg-white text-slate-900 flex items-center justify-center font-bold text-sm">T</span>
                <span className="font-bold text-lg text-white">TonyHoaiVu</span>
              </div>
              <p className="text-slate-400 text-xs sm:text-sm leading-relaxed max-w-md">
                Blog cá nhân chia sẻ kiến thức công nghệ, tối ưu hóa cơ sở dữ liệu và hệ thống quản trị CMS độc lập viết bằng PHP &amp; MySQL.
              </p>
            </div>

            <div>
              <h4 className="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Chuyên mục</h4>
              <ul className="space-y-2 text-slate-400 text-xs">
                <li><button onClick={() => { setCurrentView('home'); setSelectedCategory('lap-trinh'); }} className="hover:text-white">Lập trình Web &amp; PHP</button></li>
                <li><button onClick={() => { setCurrentView('home'); setSelectedCategory('kien-truc-he-thong'); }} className="hover:text-white">Kiến trúc hệ thống</button></li>
                <li><button onClick={() => { setCurrentView('home'); setSelectedCategory('devops-cloud'); }} className="hover:text-white">DevOps &amp; Cloud</button></li>
                <li><button onClick={() => { setCurrentView('home'); setSelectedCategory('goc-suy-ngam'); }} className="hover:text-white">Góc suy ngẫm</button></li>
              </ul>
            </div>

            <div>
              <h4 className="font-semibold text-white mb-3 text-xs tracking-wider uppercase">Liên kết</h4>
              <ul className="space-y-2 text-slate-400 text-xs">
                <li><button onClick={() => handleOpenPage('gioi-thieu')} className="hover:text-white">Về tác giả Tony Hoài Vũ</button></li>
                <li><button onClick={() => setCurrentView('contact')} className="hover:text-white">Liên hệ hợp tác</button></li>
                <li><button onClick={onNavigateToAdmin} className="text-sky-400 font-semibold hover:underline">Vào trang Quản trị (CMS) &rarr;</button></li>
              </ul>
            </div>
          </div>

          <div className="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>© 2026 TonyHoaiVu. Bản quyền thuộc về tác giả.</p>
            <p className="text-slate-400">Kiến trúc PHP &amp; MySQL độc lập phong cách WordPress.</p>
          </div>
        </div>
      </footer>
    </div>
  );
};
