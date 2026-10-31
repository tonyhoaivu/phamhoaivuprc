import React, { useState } from 'react';
import { CheckCircle2, AlertCircle, Database, Lock, Server, ArrowRight, ShieldCheck, Check } from 'lucide-react';

interface InstallerSimulatorProps {
  onComplete?: () => void;
}

export const InstallerSimulator: React.FC<InstallerSimulatorProps> = ({ onComplete }) => {
  const [step, setStep] = useState<1 | 2 | 3>(1);
  const [dbConfig, setDbConfig] = useState({
    dbHost: 'localhost',
    dbPort: '3306',
    dbName: 'tonyhoaivu_db',
    dbUser: 'root',
    dbPass: '',
    siteName: 'TonyHoaiVu',
    siteUrl: 'http://localhost/tonyhoaivu',
    adminName: 'Tony Hoài Vũ',
    adminUser: 'admin',
    adminEmail: 'admin@tonyhoaivu.com',
    adminPass: 'admin123'
  });
  const [isInstalling, setIsInstalling] = useState(false);
  const [installedLock, setInstalledLock] = useState(false);

  const handleRunInstall = (e: React.FormEvent) => {
    e.preventDefault();
    setIsInstalling(true);
    setTimeout(() => {
      setIsInstalling(false);
      setInstalledLock(true);
      setStep(3);
    }, 1500);
  };

  return (
    <div className="max-w-3xl mx-auto px-4 py-12">
      <div className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-12 shadow-xl">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="w-12 h-12 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center font-bold text-xl mx-auto mb-3 shadow-md">
            T
          </div>
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            TonyHoaiVu CMS — Trình cài đặt tự động
          </h2>
          <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
            Mô phỏng trình cài đặt <code>install.php</code> độc lập cho môi trường hosting cPanel / Localhost
          </p>
        </div>

        {/* Steps Breadcrumbs */}
        <div className="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5 mb-8 text-xs font-semibold">
          <div className={`flex items-center gap-2 ${step >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400'}`}>
            <span className={`w-6 h-6 rounded-full flex items-center justify-center text-[11px] ${step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'}`}>
              1
            </span>
            <span>1. Kiểm tra môi trường</span>
          </div>
          <div className={`flex items-center gap-2 ${step >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400'}`}>
            <span className={`w-6 h-6 rounded-full flex items-center justify-center text-[11px] ${step >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'}`}>
              2
            </span>
            <span>2. Cơ sở dữ liệu &amp; Quản trị</span>
          </div>
          <div className={`flex items-center gap-2 ${step >= 3 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'}`}>
            <span className={`w-6 h-6 rounded-full flex items-center justify-center text-[11px] ${step >= 3 ? 'bg-emerald-600 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-500'}`}>
              3
            </span>
            <span>3. Hoàn tất cài đặt</span>
          </div>
        </div>

        {/* Step 1: Environment Checks */}
        {step === 1 && (
          <div className="space-y-6">
            <h3 className="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Server className="w-4 h-4 text-blue-600" />
              <span>Yêu cầu hệ thống máy chủ PHP</span>
            </h3>

            <div className="divide-y divide-slate-100 dark:divide-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden text-xs sm:text-sm">
              <div className="p-4 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30">
                <div>
                  <div className="font-semibold text-slate-800 dark:text-slate-200">Phiên bản PHP (Yêu cầu &ge; 8.1.0)</div>
                  <div className="text-xs text-slate-500">Môi trường máy chủ hiện tại đạt chuẩn PHP 8.2+</div>
                </div>
                <span className="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                  <CheckCircle2 className="w-4 h-4" /> Đạt yêu cầu
                </span>
              </div>

              <div className="p-4 flex items-center justify-between">
                <div>
                  <div className="font-semibold text-slate-800 dark:text-slate-200">Extension PDO &amp; PDO_MySQL</div>
                  <div className="text-xs text-slate-500">Hỗ trợ Prepared Statements chống tấn công SQL Injection</div>
                </div>
                <span className="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                  <CheckCircle2 className="w-4 h-4" /> Đã kích hoạt
                </span>
              </div>

              <div className="p-4 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/30">
                <div>
                  <div className="font-semibold text-slate-800 dark:text-slate-200">Quyền ghi cấu hình (config/config.php)</div>
                  <div className="text-xs text-slate-500">Cho phép trình cài đặt tự động lưu trữ thông số kết nối</div>
                </div>
                <span className="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                  <CheckCircle2 className="w-4 h-4" /> Sẵn sàng
                </span>
              </div>

              <div className="p-4 flex items-center justify-between">
                <div>
                  <div className="font-semibold text-slate-800 dark:text-slate-200">Quyền ghi thư mục uploads/</div>
                  <div className="text-xs text-slate-500">Thư mục tải hình ảnh đã được bảo vệ với file .htaccess</div>
                </div>
                <span className="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                  <CheckCircle2 className="w-4 h-4" /> Sẵn sàng
                </span>
              </div>
            </div>

            <div className="flex justify-end pt-4">
              <button
                type="button"
                onClick={() => setStep(2)}
                className="flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs sm:text-sm rounded-xl hover:opacity-90 transition-opacity"
              >
                <span>Tiếp tục cấu hình cơ sở dữ liệu</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        )}

        {/* Step 2: Database & Admin Configuration Form */}
        {step === 2 && (
          <form onSubmit={handleRunInstall} className="space-y-6">
            <div>
              <h3 className="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <Database className="w-4 h-4 text-blue-600" />
                <span>1. Cấu hình Cơ sở dữ liệu MySQL</span>
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Database Host</label>
                  <input
                    type="text"
                    value={dbConfig.dbHost}
                    onChange={e => setDbConfig({ ...dbConfig, dbHost: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Database Port</label>
                  <input
                    type="text"
                    value={dbConfig.dbPort}
                    onChange={e => setDbConfig({ ...dbConfig, dbPort: e.target.value })}
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tên Database (Database Name)</label>
                  <input
                    type="text"
                    value={dbConfig.dbName}
                    onChange={e => setDbConfig({ ...dbConfig, dbName: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tên người dùng MySQL (Username)</label>
                  <input
                    type="text"
                    value={dbConfig.dbUser}
                    onChange={e => setDbConfig({ ...dbConfig, dbUser: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div className="sm:col-span-2">
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Mật khẩu MySQL (Password)</label>
                  <input
                    type="password"
                    value={dbConfig.dbPass}
                    onChange={e => setDbConfig({ ...dbConfig, dbPass: e.target.value })}
                    placeholder="Để trống nếu là localhost mặc định"
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
              </div>
            </div>

            <div className="pt-4 border-t border-slate-200 dark:border-slate-800">
              <h3 className="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-4">
                <Lock className="w-4 h-4 text-blue-600" />
                <span>2. Tài khoản Quản trị viên (Admin)</span>
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tên Website</label>
                  <input
                    type="text"
                    value={dbConfig.siteName}
                    onChange={e => setDbConfig({ ...dbConfig, siteName: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tên hiển thị Tác giả</label>
                  <input
                    type="text"
                    value={dbConfig.adminName}
                    onChange={e => setDbConfig({ ...dbConfig, adminName: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Tên đăng nhập Admin</label>
                  <input
                    type="text"
                    value={dbConfig.adminUser}
                    onChange={e => setDbConfig({ ...dbConfig, adminUser: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label className="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Mật khẩu Admin</label>
                  <input
                    type="password"
                    value={dbConfig.adminPass}
                    onChange={e => setDbConfig({ ...dbConfig, adminPass: e.target.value })}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white"
                  />
                </div>
              </div>
            </div>

            <div className="flex items-center justify-between pt-4">
              <button
                type="button"
                onClick={() => setStep(1)}
                className="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800"
              >
                &larr; Quay lại bước 1
              </button>
              <button
                type="submit"
                disabled={isInstalling}
                className="flex items-center gap-2 px-7 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md transition-all disabled:opacity-50"
              >
                <span>{isInstalling ? 'Đang khởi tạo CSDL...' : 'Cài đặt CMS ngay bây giờ'}</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </form>
        )}

        {/* Step 3: Success Screen */}
        {step === 3 && (
          <div className="text-center py-6 space-y-6">
            <div className="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-3xl font-bold">
              ✓
            </div>
            <div>
              <h3 className="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">
                Chúc mừng! Cài đặt thành công!
              </h3>
              <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                Hệ thống đã nạp xong 9 bảng cơ sở dữ liệu từ <code>database.sql</code>, tạo tài khoản Admin và tạo file bảo vệ <code>install.lock</code> chống truy cập trái phép.
              </p>
            </div>

            <div className="max-w-md mx-auto p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-left text-xs space-y-2">
              <div className="font-bold text-slate-800 dark:text-slate-200 mb-1">Thông tin quản trị đã thiết lập:</div>
              <div className="flex justify-between">
                <span className="text-slate-500">Tên đăng nhập:</span>
                <code className="font-bold text-blue-600 dark:text-blue-400">{dbConfig.adminUser}</code>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-500">Mật khẩu:</span>
                <code className="font-bold text-slate-700 dark:text-slate-300">•••••••• (đã mã hóa bcrypt)</code>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-500">Trạng thái khóa bảo vệ:</span>
                <span className="text-emerald-600 font-semibold flex items-center gap-1">
                  <ShieldCheck className="w-3.5 h-3.5" /> Đã tạo install.lock
                </span>
              </div>
            </div>

            <div className="flex flex-col sm:flex-row justify-center gap-3 pt-2">
              <button
                type="button"
                onClick={onComplete}
                className="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md transition-all"
              >
                Mở Bảng điều khiển Quản trị (Admin) &rarr;
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
