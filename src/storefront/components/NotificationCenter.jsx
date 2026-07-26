import React, { useState, useEffect } from 'react';
import { Bell } from 'lucide-react';

export default function NotificationCenter() {
  const [notifications, setNotifications] = useState([]);

  useEffect(() => {
    const fetchNotifs = async () => {
      const restUrl = window.kbcData?.restUrl || '/wp-json/karasu-buyers-club/v1';
      const nonce = window.kbcData?.nonce || '';

      try {
        const res = await fetch(`${restUrl}/notifications`, {
          headers: { 'X-WP-Nonce': nonce },
        });
        const json = await res.json();
        if (json.success) setNotifications(json.data);
      } catch (e) {}
    };
    fetchNotifs();
  }, []);

  return (
    <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
      <h3 className="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
        <Bell className="w-5 h-5 text-indigo-500" /> مرکز اعلان‌ها
      </h3>

      {notifications.length === 0 ? (
        <div className="text-xs text-slate-400 text-center py-4">اعلانی وجود ندارد.</div>
      ) : (
        <div className="divide-y divide-slate-100">
          {notifications.map((n) => (
            <div key={n.id} className="py-3 flex items-center justify-between text-xs">
              <span className="text-slate-800 font-medium">{n.template_key}</span>
              <span className="text-slate-400">{n.sent_at}</span>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
