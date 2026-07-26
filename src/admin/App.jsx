import React, { useState, useEffect } from 'react';
import AdminDashboard from './components/AdminDashboard';
import PointsSettings from './components/PointsSettings';
import TiersManager from './components/TiersManager';
import NotificationSettings from './components/NotificationSettings';
import MemberManagement from './components/MemberManagement';
import { ShieldCheck, LayoutDashboard, Settings, Award, Bell, Users } from 'lucide-react';

export default function App() {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [members, setMembers] = useState([]);

  const fetchMembers = async () => {
    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/admin/members`, {
        headers: { 'X-WP-Nonce': nonce },
      });
      const json = await res.json();
      if (json.success) setMembers(json.data);
    } catch (e) {}
  };

  useEffect(() => {
    fetchMembers();
  }, []);

  return (
    <div className="p-6 bg-slate-50 min-h-screen text-slate-800 dir-rtl" dir="rtl">
      <header className="mb-8 flex items-center justify-between border-b border-slate-200 pb-4 bg-white p-5 rounded-2xl shadow-sm">
        <div className="flex items-center gap-4">
          <div className="w-12 h-12 rounded-2xl bg-sky-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md">
            K
          </div>
          <div>
            <h1 className="text-2xl font-black text-slate-900">باشگاه مشتریان Karasu</h1>
            <p className="text-xs text-slate-500 mt-0.5">مدیریت کامل تنظیمات، اعضا، پاداش‌ها و اطلاع‌رسانی</p>
          </div>
        </div>
        <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
          <ShieldCheck className="w-4 h-4" /> نسخه ۰.۸.۰
        </span>
      </header>

      <div className="flex border-b border-slate-200 mb-6 gap-2 overflow-x-auto pb-1">
        <button
          onClick={() => setActiveTab('dashboard')}
          className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'dashboard' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <LayoutDashboard className="w-4 h-4" /> داشبورد آماری
        </button>
        <button
          onClick={() => setActiveTab('points')}
          className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'points' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Settings className="w-4 h-4" /> تنظیمات امتیازدهی
        </button>
        <button
          onClick={() => setActiveTab('tiers')}
          className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'tiers' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Award className="w-4 h-4" /> مدیریت سطوح (Tiers)
        </button>
        <button
          onClick={() => setActiveTab('notifications')}
          className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'notifications' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Bell className="w-4 h-4" /> تنظیمات درگاه پیامک
        </button>
        <button
          onClick={() => setActiveTab('members')}
          className={`flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer whitespace-nowrap ${activeTab === 'members' ? 'bg-sky-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'}`}
        >
          <Users className="w-4 h-4" /> مدیریت اعضا
        </button>
      </div>

      <main>
        {activeTab === 'dashboard' && <AdminDashboard members={members} />}
        {activeTab === 'points' && <PointsSettings />}
        {activeTab === 'tiers' && <TiersManager />}
        {activeTab === 'notifications' && <NotificationSettings />}
        {activeTab === 'members' && <MemberManagement members={members} onRefresh={fetchMembers} />}
      </main>
    </div>
  );
}
