import React from 'react';
import { Download, FileSpreadsheet, Users, History } from 'lucide-react';

export default function ReportsExport({ members = [] }) {
  const handleExportMembersCSV = () => {
    if (!members || members.length === 0) {
      alert('عضوی برای خروجی‌گرفتن وجود ندارد.');
      return;
    }

    const headers = ['شناسه کاربر', 'نام', 'ایمیل', 'موجودی امتیاز', 'موجودی کیف‌پول (تومان)'];
    const rows = members.map((m) => [
      m.id,
      `"${m.display_name}"`,
      `"${m.user_email}"`,
      m.points_balance || 0,
      m.wallet_balance || 0,
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + [headers.join(','), ...rows.map((e) => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `kbc_members_export_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-2xl space-y-6">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
        <FileSpreadsheet className="w-5 h-5 text-emerald-600" /> خروجی گزارش‌ها و داده‌های اعضا (CSV/Excel)
      </h3>

      <div className="space-y-4">
        <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <Users className="w-6 h-6 text-sky-600" />
            <div>
              <div className="font-bold text-slate-900 text-sm">خروجی کامل اعضای باشگاه</div>
              <div className="text-xs text-slate-500 mt-0.5">شامل نام، ایمیل، موجودی امتیاز و کیف‌پول اعضا</div>
            </div>
          </div>
          <button
            onClick={handleExportMembersCSV}
            className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-sm"
          >
            <Download className="w-4 h-4" /> دانلود CSV
          </button>
        </div>
      </div>
    </div>
  );
}
