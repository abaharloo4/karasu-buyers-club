import React, { useState, useEffect } from 'react';
import { Save, CheckCircle2, Award, Zap } from 'lucide-react';

export default function PointsSettings() {
  const [settings, setSettings] = useState({
    purchase_earn_rate: 10000,
    redemption_rate: 1000,
    min_redemption_points: 50,
    signup_points: 20,
    review_points: 10,
    first_order_points: 50,
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
        if (json.success) setSettings((prev) => ({ ...prev, ...json.data }));
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
    <form onSubmit={handleSubmit} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6 max-w-3xl">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
        <Award className="w-5 h-5 text-amber-500" /> تنظیمات جامع موتور امتیازدهی و پاداش‌ها
      </h3>

      {saved && (
        <div className="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5" /> تنظیمات با موفقیت ذخیره شد.
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">نرخ امتیاز خرید (تومان ازای ۱ امتیاز)</label>
          <input
            type="number"
            value={settings.purchase_earn_rate}
            onChange={(e) => setSettings({ ...settings, purchase_earn_rate: e.target.value })}
            className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
          />
          <p className="text-[10px] text-slate-400 mt-1">مثال: ۱۰,۰۰۰ تومان خرید = ۱ امتیاز.</p>
        </div>

        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">نرخ ارزش امتیاز (ارزش ۱ امتیاز به تومان)</label>
          <input
            type="number"
            value={settings.redemption_rate}
            onChange={(e) => setSettings({ ...settings, redemption_rate: e.target.value })}
            className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
          />
          <p className="text-[10px] text-slate-400 mt-1">مثال: ۱,۰۰۰ تومان ارزش برای هر ۱ امتیاز.</p>
        </div>
      </div>

      <div className="pt-4 border-t border-slate-100 space-y-4">
        <h4 className="text-xs font-bold text-slate-900 flex items-center gap-1.5">
          <Zap className="w-4 h-4 text-sky-500" /> امتیازات پاداش اکشن‌های خاص
        </h4>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">پاداش ثبت‌نام جدید</label>
            <input
              type="number"
              value={settings.signup_points}
              onChange={(e) => setSettings({ ...settings, signup_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 font-bold"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">پاداش ثبت دیدگاه/دیدگاه محصول</label>
            <input
              type="number"
              value={settings.review_points}
              onChange={(e) => setSettings({ ...settings, review_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 font-bold"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">پاداش اولین خرید سفارش</label>
            <input
              type="number"
              value={settings.first_order_points}
              onChange={(e) => setSettings({ ...settings, first_order_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 font-bold"
            />
          </div>
        </div>
      </div>

      <div className="pt-4 border-t border-slate-100 space-y-4">
        <div className="flex items-center gap-3">
          <input
            type="checkbox"
            id="expiry_enabled"
            checked={settings.expiry_enabled}
            onChange={(e) => setSettings({ ...settings, expiry_enabled: e.target.checked })}
            className="w-5 h-5 text-sky-600 rounded cursor-pointer"
          />
          <label htmlFor="expiry_enabled" className="text-sm font-semibold text-slate-800 cursor-pointer">
            فعال‌سازی سیستم انقضای FIFO امتیازات
          </label>
        </div>

        {settings.expiry_enabled && (
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">مهلت انقضا (به ماه)</label>
            <input
              type="number"
              value={settings.expiry_months}
              onChange={(e) => setSettings({ ...settings, expiry_months: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 font-bold max-w-xs"
            />
          </div>
        )}
      </div>

      <button
        type="submit"
        disabled={loading}
        className="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-md"
      >
        <Save className="w-4 h-4" /> {loading ? 'در حال ذخیره...' : 'ذخیره تنظیمات امتیازات'}
      </button>
    </form>
  );
}
