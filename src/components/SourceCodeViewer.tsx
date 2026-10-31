import React, { useState } from 'react';
import JSZip from 'jszip';
import { Download, FileCode, Folder, Copy, Check, ExternalLink, ShieldCheck, Terminal } from 'lucide-react';
import { sourceFiles, SourceFile } from '../data/sourceCode';

export const SourceCodeViewer: React.FC = () => {
  const [selectedFile, setSelectedFile] = useState<SourceFile>(sourceFiles[0]);
  const [copied, setCopied] = useState(false);
  const [isZipping, setIsZipping] = useState(false);
  const [zipSuccess, setZipSuccess] = useState(false);

  // Group files by folder
  const folders = Array.from(new Set(sourceFiles.map(f => f.folder)));

  const handleCopy = () => {
    navigator.clipboard.writeText(selectedFile.content);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleDownloadSingle = (file: SourceFile) => {
    const blob = new Blob([file.content], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = file.name;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  };

  const handleDownloadZip = async () => {
    setIsZipping(true);
    try {
      const zip = new JSZip();
      const rootFolder = zip.folder('tonyhoaivu');

      sourceFiles.forEach(file => {
        if (rootFolder) {
          rootFolder.file(file.path, file.content);
        }
      });

      // Add a helpful deployment note
      rootFolder?.file('HUONG_DAN_CAI_DAT.txt', `HƯỚNG DẪN CÀI ĐẶT TONYHOAIVU CMS (PHP 8 + MYSQL)
=====================================================
1. Giải nén toàn bộ thư mục 'tonyhoaivu' lên Hosting (cPanel / DirectAdmin) hoặc localhost (XAMPP htdocs / Laragon www).
2. Tạo 1 cơ sở dữ liệu trống trong phpMyAdmin (ví dụ: tonyhoaivu_db).
3. Mở trình duyệt và truy cập: http://ten-mien-cua-ban/install.php
4. Điền thông tin kết nối Database và tạo tài khoản Admin.
5. Sau khi hoàn tất, đăng nhập vào /admin/login.php với thông tin:
   - Tài khoản: admin
   - Mật khẩu: admin123
=====================================================
Bản quyền: TonyHoaiVu CMS`);

      const content = await zip.generateAsync({ type: 'blob' });
      const url = URL.createObjectURL(content);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'TonyHoaiVu-CMS-PHP-MySQL.zip';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);

      setZipSuccess(true);
      setTimeout(() => setZipSuccess(false), 3500);
    } catch (err) {
      console.error('Error generating zip:', err);
    } finally {
      setIsZipping(false);
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      {/* Top Banner with One-Click ZIP Download */}
      <div className="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border border-slate-700 rounded-3xl p-6 sm:p-8 text-white mb-8 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
          <div className="flex items-center gap-2 text-xs uppercase tracking-wider text-sky-400 font-semibold mb-2">
            <ShieldCheck className="w-4 h-4" />
            <span>Mã nguồn chuẩn kiến trúc PHP 8 + MySQL</span>
          </div>
          <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">
            Trọn bộ mã nguồn TonyHoaiVu CMS
          </h2>
          <p className="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed">
            Hệ thống độc lập tự phát triển 100%, không sử dụng lõi WordPress cồng kềnh nhưng mang lại giao diện và trải nghiệm quản trị chuẩn WordPress. Đầy đủ bảo mật PDO Prepared Statements, CSRF Token, Password Hash và trình cài đặt tự động.
          </p>
        </div>

        <div className="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
          <button
            onClick={handleDownloadZip}
            disabled={isZipping}
            className="flex items-center justify-center gap-2.5 px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50"
          >
            <Download className="w-4 h-4" />
            <span>{isZipping ? 'Đang nén file...' : 'Tải về trọn bộ ZIP (.zip)'}</span>
          </button>
        </div>
      </div>

      {zipSuccess && (
        <div className="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs sm:text-sm flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Check className="w-5 h-5 text-emerald-500" />
            <span>Đã tạo file <strong>TonyHoaiVu-CMS-PHP-MySQL.zip</strong> và bắt đầu tải xuống thành công!</span>
          </div>
        </div>
      )}

      {/* Code Inspector Workspace */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
        {/* File Tree Left Column */}
        <div className="lg:col-span-4 border-r border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 p-4">
          <div className="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
            <span className="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
              <Folder className="w-4 h-4 text-blue-500" />
              <span>Cấu trúc thư mục ({sourceFiles.length} files)</span>
            </span>
            <span className="text-[11px] font-mono text-slate-400">/tonyhoaivu/</span>
          </div>

          <div className="space-y-4 max-h-[600px] overflow-y-auto pr-1">
            {folders.map(folder => {
              const filesInFolder = sourceFiles.filter(f => f.folder === folder);
              return (
                <div key={folder} className="space-y-1">
                  <div className="text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5 px-2 py-1">
                    <Folder className="w-3.5 h-3.5 text-amber-500" />
                    <span>{folder === '/' ? 'Thư mục gốc (Root)' : `/${folder}/`}</span>
                  </div>
                  <div className="space-y-0.5 pl-2">
                    {filesInFolder.map(file => (
                      <button
                        key={file.path}
                        onClick={() => setSelectedFile(file)}
                        className={`w-full text-left px-2.5 py-1.5 rounded-lg text-xs font-mono flex items-center justify-between transition-colors ${
                          selectedFile.path === file.path
                            ? 'bg-blue-600 text-white font-semibold shadow-sm'
                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200/60 dark:hover:bg-slate-800/60'
                        }`}
                      >
                        <span className="flex items-center gap-1.5 truncate">
                          <FileCode className="w-3.5 h-3.5 shrink-0 opacity-80" />
                          <span className="truncate">{file.name}</span>
                        </span>
                        <span className="text-[10px] uppercase opacity-70 ml-2">{file.language}</span>
                      </button>
                    ))}
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Code Content Right Column */}
        <div className="lg:col-span-8 flex flex-col h-[650px]">
          {/* File Header */}
          <div className="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900">
            <div>
              <div className="flex items-center gap-2">
                <span className="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">/tonyhoaivu/{selectedFile.path}</span>
                <span className="text-[10px] px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase font-mono font-semibold">
                  {selectedFile.language}
                </span>
              </div>
              <p className="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{selectedFile.description}</p>
            </div>

            <div className="flex items-center gap-2">
              <button
                onClick={handleCopy}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors text-slate-700 dark:text-slate-300"
              >
                {copied ? <Check className="w-3.5 h-3.5 text-emerald-500" /> : <Copy className="w-3.5 h-3.5" />}
                <span>{copied ? 'Đã sao chép' : 'Sao chép mã'}</span>
              </button>
              <button
                onClick={() => handleDownloadSingle(selectedFile)}
                className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:opacity-90 transition-opacity"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Tải tệp</span>
              </button>
            </div>
          </div>

          {/* Code Viewer Surface */}
          <div className="flex-1 overflow-auto bg-slate-950 text-slate-200 p-4 font-mono text-xs leading-relaxed">
            <pre className="m-0 whitespace-pre">
              <code>{selectedFile.content}</code>
            </pre>
          </div>
        </div>
      </div>
    </div>
  );
};
