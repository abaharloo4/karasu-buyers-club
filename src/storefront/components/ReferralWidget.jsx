import React, { useState, useEffect } from 'react';
import { Users, Copy, Check } from 'lucide-react';

export default function ReferralWidget() {
  const [refData, setRefData] = useState(null);
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    const fetchRef = async () => {
      const restUrl = window.kbcData?.restUrl || '/wp-json/karasu-buyers-club/v1';
      const nonce = window.kbcData?.nonce || '';

      try {
        const res = await fetch(`${restUrl}/referral/my-code`, {
          headers: { 'X-WP-Nonce': nonce },
        });
        const json = await res.json();
        if (json.success) setRefData(json.data);
      } catch (e) {}
    };
    fetchRef();
  }, []);

  const handleCopy = () => {
    if (refData?.referral_link) {
      navigator.clipboard.writeText(refData.referral_link);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  return (
    <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
      <h3 className="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
        <Users className="w-5 h-5 text-sky-500" /> معرفی دوستان و دریافت امتیاز
      </h3>
      <p className="text-xs text-slate-500 mb-4">
        با ارسال کد یا لینک اختصاصی خود به دوستانتان، پس از اولین خرید آن‌ها هر دو پاداش دریافت می‌کنید!
      </p>

      {refData && (
        <div className="flex flex-col md:flex-row items-center gap-3">
          <div className="flex-1 w-full bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs font-mono text-slate-700 truncate">
            {refData.referral_link}
          </div>
          <button
            onClick={handleCopy}
            className="w-full md:w-auto px-4 py-3 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 cursor-pointer transition-all shadow-sm"
          >
            {copied ? <Check className="w-4 h-4" /> : <Copy className="w-4 h-4" />}
            {copied ? 'کپی شد' : 'کپی لینک'}
          </button>
        </div>
      )}
    </div>
  );
}
