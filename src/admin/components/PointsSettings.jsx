import React, { useState, useEffect } from 'react';
import { Save, CheckCircle2 } from 'lucide-react';

export default function PointsSettings() {
  const [settings, setSettings] = useState({
    purchase_earn_rate: 10000,
    redemption_rate: 1000,
    expiry_enabled: false,
    expiry_months: 6,
  });
  const [loading, setLoading] = useState(false);
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    const fetchSettings = async () => {
      const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
      const nonce = window.kbcAdminData?.nonce || '';

      try {
        const res = await fetch(`${restUrl}/admin/settings`, {
          headers: { 'X-WP-Nonce': nonce },
        });
        const json = await res.json();
        if (json.success) setSettings(json.data);
      } catch (e) {}
    };
    fetchSettings();
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setSaved(false);

    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/admin/settings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
        body: JSON.stringify(settings),
      });
      const json = await res.json();
      if (json.success) setSaved(true);
    } catch (e) {} finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6 max-w-2xl">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">تنظیمات موتور امتیازدهی</h3>

      {saved && (
        <div className="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5" /> تنظیمات با موفقیت ذخیره شد.
        </div>
      )}

      <div>
        <label className="block text-xs font-semibold text-slate-700 mb-1">نرخ دریافت امتیاز خرید (تومان/ریال ازای ۱ امتیاز)</label>
        <input
          type="number"
          value={settings.purchase_earn_rate}
          onChange={(e) => setSettings({ ...settings, purchase_earn_rate: e.target.value })}
          className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
        />
        <p className="text-xs text-slate-400 mt-1">مثال: ۱۰,۰۰۰ یعنی ازای هر ۱۰ هزار تومان خرید، ۱ امتیاز تعلق می‌گیرد.</p>
      </div>

      <div>
        <label className="block text-xs font-semibold text-slate-700 mb-1">نرخ تبدیل پاداش (ارزش هر ۱ امتیاز به تومان)</label>
        <input
          type="number"
          value={settings.redemption_rate}
          onChange={(e) => setSettings({ ...settings, redemption_rate: e.target.value })}
          className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
        />
        <p className="text-xs text-slate-400 mt-1">مثال: ۱,۰۰۰ یعنی هر ۱ امتیاز هنگام تبدیل ۵۰ امتیاز ارزش ۵۰ هزار تومانی دارد.</p>
      </div>

      <div className="flex items-center gap-3 pt-2">
        <input
          type="checkbox"
          id="expiry_enabled"
          checked={settings.expiry_enabled}
          onChange={(e) => setSettings({ ...settings, expiry_enabled: e.target.checked })}
          className="w-5 h-5 text-sky-600 rounded"
        />
        <label htmlFor="expiry_enabled" className="text-sm font-semibold text-slate-800">فعال‌سازی سیستم انقضای امتیازات (FIFO)</label>
      </div>

      {settings.expiry_enabled && (
        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">مهلت انقضا (به ماه)</label>
          <input
            type="number"
            value={settings.expiry_months}
            onChange={(e) => setSettings({ ...settings, expiry_months: e.target.value })}
            className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
          />
        </div>
      )}

      <button
        type="submit"
        disabled={loading}
        className="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-md"
      >
        <Save className="w-4 h-4" /> {loading ? 'در حال ذخیره...' : 'ذخیره تنظیمات'}
      </button>
    </form>
  );
}
