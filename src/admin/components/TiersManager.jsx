import React, { useState, useEffect } from 'react';
import { Award, Shield } from 'lucide-react';

export default function TiersManager() {
  const [tiers, setTiers] = useState([]);

  useEffect(() => {
    const fetchTiers = async () => {
      const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
      try {
        const res = await fetch(`${restUrl}/tiers`);
        const json = await res.json();
        if (json.success) setTiers(json.data);
      } catch (e) {}
    };
    fetchTiers();
  }, []);

  return (
    <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-3xl">
      <h3 className="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">مدیریت سطوح مشتریان (Tiers)</h3>
      {tiers.length === 0 ? (
        <div className="text-sm text-slate-500 py-4 text-center">هنوز سطحی تعریف نشده است. (سطوح پیش‌فرض سیستم فعال است).</div>
      ) : (
        <div className="divide-y divide-slate-100">
          {tiers.map((t) => (
            <div key={t.id} className="py-3 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <Shield className="w-5 h-5 text-amber-500" />
                <span className="font-bold text-slate-900">{t.name}</span>
              </div>
              <span className="text-xs font-semibold text-slate-600">آستانه: {parseFloat(t.threshold).toLocaleString()} تومان</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
