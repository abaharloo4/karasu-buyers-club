import React, { useState, useEffect } from 'react';
import { Send, Save } from 'lucide-react';

export default function NotificationSettings() {
  const [smsSettings, setSmsSettings] = useState({
    sms_enabled: false,
    sms_gateway: 'kavenegar',
    sms_api_key: '',
    email_enabled: true,
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      await fetch(`${restUrl}/admin/settings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
        body: JSON.stringify(smsSettings),
      });
      alert('تنظیمات پیامک و ایمیل ذخیره شد.');
    } catch (e) {}
  };

  return (
    <form onSubmit={handleSubmit} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-2xl space-y-6">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">تنظیمات درگاه‌های پیامکی و اطلاع‌رسانی</h3>

      <div className="flex items-center gap-3">
        <input
          type="checkbox"
          id="sms_enabled"
          checked={smsSettings.sms_enabled}
          onChange={(e) => setSmsSettings({ ...smsSettings, sms_enabled: e.target.checked })}
          className="w-5 h-5 text-sky-600 rounded"
        />
        <label htmlFor="sms_enabled" className="text-sm font-semibold text-slate-800">فعال‌سازی اطلاع‌رسانی پیامکی</label>
      </div>

      {smsSettings.sms_enabled && (
        <>
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
            <label className="block text-xs font-semibold text-slate-700 mb-1">کلید API (API Key / AccessKey)</label>
            <input
              type="password"
              value={smsSettings.sms_api_key}
              onChange={(e) => setSmsSettings({ ...smsSettings, sms_api_key: e.target.value })}
              className="w-full p-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 font-mono"
            />
          </div>
        </>
      )}

      <button
        type="submit"
        className="px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-md"
      >
        <Save className="w-4 h-4" /> ذخیره تنظیمات درگاه
      </button>
    </form>
  );
}
