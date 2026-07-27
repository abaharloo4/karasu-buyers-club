import React, { useState } from 'react';
import { Users, Save, CheckCircle2 } from 'lucide-react';

export default function ReferralSettings() {
  const [settings, setSettings] = useState({
    referral_enabled: true,
    referrer_points: 50,
    referee_points: 25,
    trigger_event: 'first_order', // signup or first_order
  });
  const [saved, setSaved] = useState(false);
  const [loading, setLoading] = useState(false);

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
    <form onSubmit={handleSubmit} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-2xl space-y-6">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
        <Users className="w-5 h-5 text-sky-600" /> تنظیمات معرفی دوست (Referral)
      </h3>

      {saved && (
        <div className="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5" /> تنظیمات سیستم معرفی با موفقیت ذخیره شد.
        </div>
      )}

      <div className="flex items-center gap-3">
        <input
          type="checkbox"
          id="referral_enabled"
          checked={settings.referral_enabled}
          onChange={(e) => setSettings({ ...settings, referral_enabled: e.target.checked })}
          className="w-5 h-5 text-sky-600 rounded cursor-pointer"
        />
        <label htmlFor="referral_enabled" className="text-sm font-semibold text-slate-800 cursor-pointer">
          فعال‌سازی سیستم معرفی دوست
        </label>
      </div>

      {settings.referral_enabled && (
        <>
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">امتیاز اعطایی به معرف (کارت دعوت کننده)</label>
            <input
              type="number"
              value={settings.referrer_points}
              onChange={(e) => setSettings({ ...settings, referrer_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">امتیاز اعطایی به معرفی‌شونده (کاربر جدید)</label>
            <input
              type="number"
              value={settings.referee_points}
              onChange={(e) => setSettings({ ...settings, referee_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">شرط اعطای پاداش (ضد سوءاستفاده)</label>
            <select
              value={settings.trigger_event}
              onChange={(e) => setSettings({ ...settings, trigger_event: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-semibold"
            >
              <option value="first_order">پس از ثبت اولین خرید موفق کاربر جدید (پیش‌فرض امن)</option>
              <option value="signup">بلافاصله پس از ثبت‌نام کاربر جدید</option>
            </select>
          </div>
        </>
      )}

      <button
        type="submit"
        disabled={loading}
        className="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-md"
      >
        <Save className="w-4 h-4" /> {loading ? 'در حال ذخیره...' : 'ذخیره تنظیمات سیستم معرفی'}
      </button>
    </form>
  );
}
