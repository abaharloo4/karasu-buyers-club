import React, { useState, useEffect } from 'react';
import { Send, Save, Bell, MessageSquare, CheckCircle2 } from 'lucide-react';

export default function NotificationSettings() {
  const [smsSettings, setSmsSettings] = useState({
    sms_enabled: false,
    sms_gateway: 'kavenegar',
    sms_api_key: '',
    sms_line_number: '',
    email_enabled: true,
    tpl_points_earned: '{name} عزیز، شما {points} امتیاز جدید دریافت کردید.',
    tpl_tier_upgraded: '{name} عزیز، تبریک! شما به سطح {tier} ارتقا یافتید.',
    tpl_birthday: '{name} عزیز، تولدت مبارک! {points} امتیاز هدیه به شما تعلق گرفت.',
    tpl_referral_earned: '{name} عزیز، بابت دعوت از دوست خود {points} امتیاز دریافت کردید.',
  });
  const [saved, setSaved] = useState(false);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchSettings = async () => {
      const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
      const nonce = window.kbcAdminData?.nonce || '';

      try {
        const res = await fetch(`${restUrl}/admin/settings`, {
          headers: { 'X-WP-Nonce': nonce },
        });
        const json = await res.json();
        if (json.success) setSmsSettings((prev) => ({ ...prev, ...json.data }));
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
        body: JSON.stringify(smsSettings),
      });
      const json = await res.json();
      if (json.success) setSaved(true);
    } catch (e) {} finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-3xl space-y-6">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
        <Bell className="w-5 h-5 text-indigo-500" /> تنظیمات اطلاع‌رسانی چندکاناله و قالب متن پیام‌ها
      </h3>

      {saved && (
        <div className="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
          <CheckCircle2 className="w-5 h-5" /> تنظیمات اطلاع‌رسانی و قالب‌های پیام ذخیره شد.
        </div>
      )}

      <div className="flex items-center gap-6">
        <div className="flex items-center gap-3">
          <input
            type="checkbox"
            id="sms_enabled"
            checked={smsSettings.sms_enabled}
            onChange={(e) => setSmsSettings({ ...smsSettings, sms_enabled: e.target.checked })}
            className="w-5 h-5 text-sky-600 rounded cursor-pointer"
          />
          <label htmlFor="sms_enabled" className="text-sm font-semibold text-slate-800 cursor-pointer">
            اطلاع‌رسانی پیامکی (SMS)
          </label>
        </div>

        <div className="flex items-center gap-3">
          <input
            type="checkbox"
            id="email_enabled"
            checked={smsSettings.email_enabled}
            onChange={(e) => setSmsSettings({ ...smsSettings, email_enabled: e.target.checked })}
            className="w-5 h-5 text-sky-600 rounded cursor-pointer"
          />
          <label htmlFor="email_enabled" className="text-sm font-semibold text-slate-800 cursor-pointer">
            اطلاع‌رسانی ایمیلی
          </label>
        </div>
      </div>

      {smsSettings.sms_enabled && (
        <div className="space-y-4 pt-4 border-t border-slate-100">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1">انتخاب درگاه پیامک ایرانی</label>
              <select
                value={smsSettings.sms_gateway}
                onChange={(e) => setSmsSettings({ ...smsSettings, sms_gateway: e.target.value })}
                className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-semibold"
              >
                <option value="kavenegar">کاوه‌نگار (Kavenegar)</option>
                <option value="mellipayamak">ملی‌پيامک (Mellipayamak)</option>
                <option value="farazsms">فرازاس‌ام‌اس / IPPanel</option>
              </select>
            </div>

            <div>
              <label className="block text-xs font-semibold text-slate-700 mb-1">کلید API (API Key)</label>
              <input
                type="password"
                value={smsSettings.sms_api_key}
                onChange={(e) => setSmsSettings({ ...smsSettings, sms_api_key: e.target.value })}
                className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-mono"
              />
            </div>
          </div>
        </div>
      )}

      <div className="pt-4 border-t border-slate-100 space-y-4">
        <h4 className="text-xs font-bold text-slate-900 flex items-center gap-1.5">
          <MessageSquare className="w-4 h-4 text-indigo-500" /> ویراستار قالب پیام‌های رویدادها
        </h4>

        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">متن پیام دریافت امتیاز جدید</label>
          <textarea
            rows="2"
            value={smsSettings.tpl_points_earned}
            onChange={(e) => setSmsSettings({ ...smsSettings, tpl_points_earned: e.target.value })}
            className="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium"
          />
        </div>

        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">متن پیام ارتقای سطح عضویت</label>
          <textarea
            rows="2"
            value={smsSettings.tpl_tier_upgraded}
            onChange={(e) => setSmsSettings({ ...smsSettings, tpl_tier_upgraded: e.target.value })}
            className="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium"
          />
        </div>

        <div>
          <label className="block text-xs font-semibold text-slate-700 mb-1">متن پیام هدیه روز تولد</label>
          <textarea
            rows="2"
            value={smsSettings.tpl_birthday}
            onChange={(e) => setSmsSettings({ ...smsSettings, tpl_birthday: e.target.value })}
            className="w-full p-3 rounded-xl border border-slate-200 text-xs font-medium"
          />
        </div>
      </div>

      <button
        type="submit"
        disabled={loading}
        className="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-md"
      >
        <Save className="w-4 h-4" /> {loading ? 'در حال ذخیره...' : 'ذخیره تنظیمات اطلاع‌رسانی'}
      </button>
    </form>
  );
}
