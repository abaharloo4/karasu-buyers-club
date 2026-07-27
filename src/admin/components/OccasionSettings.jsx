import React, { useState, useEffect } from 'react';
import { Calendar, Save, CheckCircle2 } from 'lucide-react';

export default function OccasionSettings() {
  const [settings, setSettings] = useState({
    birthday_reward_enabled: true,
    birthday_points: 100,
    anniversary_reward_enabled: true,
    anniversary_points: 150,
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
        <Calendar className="w-5 h-5 text-sky-600" /> تنظیمات مناسبت‌های خاص (تولد و سالگرد عضویت)
      </h3>

      {saved && (
        <div className="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5" /> تنظیمات مناسبت‌ها با موفقیت ذخیره شد.
        </div>
      )}

      <div className="space-y-4">
        <div className="flex items-center gap-3">
          <input
            type="checkbox"
            id="birthday_reward_enabled"
            checked={settings.birthday_reward_enabled}
            onChange={(e) => setSettings({ ...settings, birthday_reward_enabled: e.target.checked })}
            className="w-5 h-5 text-sky-600 rounded cursor-pointer"
          />
          <label htmlFor="birthday_reward_enabled" className="text-sm font-semibold text-slate-800 cursor-pointer">
            فعال‌سازی پاداش روز تولد
          </label>
        </div>

        {settings.birthday_reward_enabled && (
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">تعداد امتیاز هدیه تولد</label>
            <input
              type="number"
              value={settings.birthday_points}
              onChange={(e) => setSettings({ ...settings, birthday_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
            />
          </div>
        )}
      </div>

      <div className="space-y-4 pt-4 border-t border-slate-100">
        <div className="flex items-center gap-3">
          <input
            type="checkbox"
            id="anniversary_reward_enabled"
            checked={settings.anniversary_reward_enabled}
            onChange={(e) => setSettings({ ...settings, anniversary_reward_enabled: e.target.checked })}
            className="w-5 h-5 text-sky-600 rounded cursor-pointer"
          />
          <label htmlFor="anniversary_reward_enabled" className="text-sm font-semibold text-slate-800 cursor-pointer">
            فعال‌سازی پاداش سالگرد عضویت
          </label>
        </div>

        {settings.anniversary_reward_enabled && (
          <div>
            <label className="block text-xs font-semibold text-slate-700 mb-1">تعداد امتیاز هدیه سالگرد عضویت</label>
            <input
              type="number"
              value={settings.anniversary_points}
              onChange={(e) => setSettings({ ...settings, anniversary_points: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-bold"
            />
          </div>
        )}
      </div>

      <button
        type="submit"
        disabled={loading}
        className="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-md"
      >
        <Save className="w-4 h-4" /> {loading ? 'در حال ذخیره...' : 'ذخیره تنظیمات مناسبت‌ها'}
      </button>
    </form>
  );
}
