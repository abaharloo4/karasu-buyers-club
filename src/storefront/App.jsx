import React, { useState, useEffect } from 'react';
import StatusSummary from './components/StatusSummary';
import PointsHistory from './components/PointsHistory';
import RewardsRedemption from './components/RewardsRedemption';
import ReferralWidget from './components/ReferralWidget';
import NotificationCenter from './components/NotificationCenter';
import { Star, Gift, Users, Bell, History } from 'lucide-react';

export default function App() {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [summary, setSummary] = useState(null);
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchData = async () => {
    const restUrl = window.kbcData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcData?.nonce || '';

    try {
      const [sumRes, histRes] = await Promise.all([
        fetch(`${restUrl}/points/summary`, { headers: { 'X-WP-Nonce': nonce } }),
        fetch(`${restUrl}/points/history`, { headers: { 'X-WP-Nonce': nonce } }),
      ]);

      const sumJson = await sumRes.json();
      const histJson = await histRes.json();

      if (sumJson.success) setSummary(sumJson.data);
      if (histJson.success) setHistory(histJson.data);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  return (
    <div className="kbc-storefront-wrap dir-rtl max-w-4xl mx-auto p-4" dir="rtl">
      <StatusSummary summary={summary} />

      <div className="flex border-b border-slate-200 mb-6 gap-2 overflow-x-auto pb-1">
        <button
          onClick={() => setActiveTab('dashboard')}
          className={`flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'dashboard' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Star className="w-4 h-4" /> خلاصه وضعیت
        </button>
        <button
          onClick={() => setActiveTab('redeem')}
          className={`flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'redeem' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Gift className="w-4 h-4" /> دریافت پاداش
        </button>
        <button
          onClick={() => setActiveTab('history')}
          className={`flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'history' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <History className="w-4 h-4" /> سوابق امتیاز
        </button>
        <button
          onClick={() => setActiveTab('referral')}
          className={`flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'referral' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Users className="w-4 h-4" /> معرفی دوست
        </button>
        <button
          onClick={() => setActiveTab('notifications')}
          className={`flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'notifications' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Bell className="w-4 h-4" /> مرکز اعلان‌ها
        </button>
      </div>

      {activeTab === 'dashboard' && (
        <>
          <RewardsRedemption pointsBalance={summary?.points_balance || 0} onRedeemSuccess={fetchData} />
          <ReferralWidget />
          <PointsHistory history={history} />
        </>
      )}

      {activeTab === 'redeem' && (
        <RewardsRedemption pointsBalance={summary?.points_balance || 0} onRedeemSuccess={fetchData} />
      )}

      {activeTab === 'history' && <PointsHistory history={history} />}

      {activeTab === 'referral' && <ReferralWidget />}

      {activeTab === 'notifications' && <NotificationCenter />}
    </div>
  );
}
