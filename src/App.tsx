/**
 * TonyHoaiVu Blog & PHP CMS - Master Application Root
 */

import React, { useState, useEffect } from 'react';
import { 
  Globe, 
  ShieldCheck, 
  Code2, 
  Download, 
  Wrench, 
  Sparkles,
  ExternalLink 
} from 'lucide-react';
import { FrontendBlog } from './components/FrontendBlog';
import { AdminPanel } from './components/AdminPanel';
import { SourceCodeViewer } from './components/SourceCodeViewer';
import { InstallerSimulator } from './components/InstallerSimulator';
import { 
  initialPosts, 
  initialCategories, 
  initialComments, 
  initialPages, 
  initialMedia, 
  initialMenus, 
  defaultUser, 
  Post, 
  Category, 
  Comment, 
  Page, 
  MediaItem, 
  MenuItem, 
  User 
} from './data/blogData';

export default function App() {
  // App Navigation: 'blog' | 'admin' | 'source' | 'installer'
  const [appMode, setAppMode] = useState<'blog' | 'admin' | 'source' | 'installer'>('blog');

  // Shared reactive state
  const [posts, setPosts] = useState<Post[]>(() => {
    const saved = localStorage.getItem('tony_posts');
    return saved ? JSON.parse(saved) : initialPosts;
  });

  const [categories, setCategories] = useState<Category[]>(() => {
    const saved = localStorage.getItem('tony_categories');
    return saved ? JSON.parse(saved) : initialCategories;
  });

  const [comments, setComments] = useState<Comment[]>(() => {
    const saved = localStorage.getItem('tony_comments');
    return saved ? JSON.parse(saved) : initialComments;
  });

  const [pages, setPages] = useState<Page[]>(() => {
    const saved = localStorage.getItem('tony_pages');
    return saved ? JSON.parse(saved) : initialPages;
  });

  const [media, setMedia] = useState<MediaItem[]>(() => {
    const saved = localStorage.getItem('tony_media');
    return saved ? JSON.parse(saved) : initialMedia;
  });

  const [menus, setMenus] = useState<MenuItem[]>(() => {
    const saved = localStorage.getItem('tony_menus');
    return saved ? JSON.parse(saved) : initialMenus;
  });

  const [author, setAuthor] = useState<User>(() => {
    const saved = localStorage.getItem('tony_user');
    return saved ? JSON.parse(saved) : defaultUser;
  });

  // Dark Mode
  const [isDark, setIsDark] = useState<boolean>(() => {
    if (typeof window !== 'undefined') {
      return document.documentElement.classList.contains('dark') || 
             localStorage.getItem('theme') === 'dark';
    }
    return false;
  });

  useEffect(() => {
    if (isDark) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  }, [isDark]);

  // Persist to local storage
  useEffect(() => {
    localStorage.setItem('tony_posts', JSON.stringify(posts));
  }, [posts]);

  useEffect(() => {
    localStorage.setItem('tony_categories', JSON.stringify(categories));
  }, [categories]);

  useEffect(() => {
    localStorage.setItem('tony_comments', JSON.stringify(comments));
  }, [comments]);

  useEffect(() => {
    localStorage.setItem('tony_pages', JSON.stringify(pages));
  }, [pages]);

  useEffect(() => {
    localStorage.setItem('tony_media', JSON.stringify(media));
  }, [media]);

  useEffect(() => {
    localStorage.setItem('tony_menus', JSON.stringify(menus));
  }, [menus]);

  useEffect(() => {
    localStorage.setItem('tony_user', JSON.stringify(author));
  }, [author]);

  const handleAddComment = (newComment: Omit<Comment, 'id' | 'created_at' | 'status'>) => {
    const commentItem: Comment = {
      ...newComment,
      id: Date.now(),
      status: 'approved',
      created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
    };
    setComments(prev => [commentItem, ...prev]);
  };

  return (
    <div className="min-h-screen flex flex-col font-sans">
      {/* Top Experience Switcher Bar */}
      <div className="bg-slate-900 text-white border-b border-slate-800 text-xs px-4 py-2 sticky top-0 z-50 flex flex-wrap items-center justify-between gap-3 shadow-md">
        <div className="flex items-center gap-2">
          <span className="font-bold text-sky-400">⚡ TonyHoaiVu Studio:</span>
          <span className="hidden sm:inline text-slate-300">Hệ thống Blog cá nhân &amp; CMS PHP độc lập chuẩn WordPress</span>
        </div>

        {/* Mode Switcher Tabs */}
        <div className="flex items-center gap-1.5 p-1 bg-slate-800/90 rounded-xl border border-slate-700/80">
          <button
            onClick={() => setAppMode('blog')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
              appMode === 'blog'
                ? 'bg-blue-600 text-white shadow-sm'
                : 'text-slate-300 hover:text-white'
            }`}
          >
            <Globe className="w-3.5 h-3.5" />
            <span>Website Blog</span>
          </button>

          <button
            onClick={() => setAppMode('admin')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
              appMode === 'admin'
                ? 'bg-blue-600 text-white shadow-sm'
                : 'text-slate-300 hover:text-white'
            }`}
          >
            <ShieldCheck className="w-3.5 h-3.5" />
            <span>Admin CMS (/admin)</span>
          </button>

          <button
            onClick={() => setAppMode('source')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
              appMode === 'source'
                ? 'bg-blue-600 text-white shadow-sm'
                : 'text-slate-300 hover:text-white'
            }`}
          >
            <Code2 className="w-3.5 h-3.5" />
            <span>Mã nguồn &amp; Tải ZIP</span>
          </button>

          <button
            onClick={() => setAppMode('installer')}
            className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
              appMode === 'installer'
                ? 'bg-blue-600 text-white shadow-sm'
                : 'text-slate-300 hover:text-white'
            }`}
          >
            <Wrench className="w-3.5 h-3.5" />
            <span>Cài đặt (install.php)</span>
          </button>
        </div>
      </div>

      {/* Main View Display */}
      <div className="flex-1">
        {appMode === 'blog' && (
          <FrontendBlog
            posts={posts}
            categories={categories}
            comments={comments}
            pages={pages}
            author={author}
            onAddComment={handleAddComment}
            onNavigateToAdmin={() => setAppMode('admin')}
            isDark={isDark}
            onToggleTheme={() => setIsDark(!isDark)}
          />
        )}

        {appMode === 'admin' && (
          <AdminPanel
            posts={posts}
            categories={categories}
            comments={comments}
            pages={pages}
            media={media}
            menus={menus}
            author={author}
            onUpdatePosts={setPosts}
            onUpdateCategories={setCategories}
            onUpdateComments={setComments}
            onUpdatePages={setPages}
            onUpdateMedia={setMedia}
            onUpdateMenus={setMenus}
            onUpdateAuthor={setAuthor}
            onReturnToBlog={() => setAppMode('blog')}
          />
        )}

        {appMode === 'source' && (
          <SourceCodeViewer />
        )}

        {appMode === 'installer' && (
          <InstallerSimulator
            onComplete={() => setAppMode('admin')}
          />
        )}
      </div>
    </div>
  );
}
