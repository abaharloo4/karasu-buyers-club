import React from 'react';
import { Users, Award, Wallet, TrendingUp, ShieldCheck, Sparkles, Activity } from 'lucide-react';

export default function AdminDashboard({ members = [] }) {
  const totalMembers = members.length;
  const totalPoints = members.reduce((sum, m) => sum + (m.points_balance || 0), 0);
  const totalWallet = members.reduce((sum, m) => sum + (m.wallet_balance || 0), 0);
  const avgWallet = totalMembers > 0 ? Math.round(totalWallet / totalMembers) : 0;

  return (
    <div className="space-y-6">
      <div className="bg-gradient-to-r from-sky-600 to-indigo-700 p-6 rounded-3xl text-white shadow-lg relative overflow-hidden flex justify-between items-center">
        <div>
          <span className="text-xs font-semibold text-sky-200 uppercase tracking-wider flex items-center gap-1">
            <Sparkles className="w-4 h-4 text-amber-300" /> داشبورد مدیریتی باشگاه مشتریان Karasu
          </span>
          <h2 className="text-2xl font-black mt-1">خلاصه عملکرد و آمار لحظه‌ای باشگاه</h2>
          <p className="text-xs text-sky-100 mt-1">نظارت بر رشد اعضا، موجودی کیف‌پول و ارزش دوره خرید مشتریان</p>
        </div>
        <div className="hidden md:flex items-center gap-3">
          <div className="p-4 bg-white/10 backdrop-blur-md rounded-2xl text-center min-w-[110px]">
            <span className="text-[10px] text-sky-200 block">میانگین شارژ</span>
            <span className="text-base font-extrabold">{avgWallet.toLocaleString()} ت</span>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
          <div>
            <div className="text-xs text-slate-500 font-semibold">تعداد کل اعضای فعال</div>
            <div className="text-3xl font-black text-slate-900 mt-2">{totalMembers.toLocaleString()} <span className="text-sm font-normal text-slate-400">نفر</span></div>
          </div>
          <div className="p-4 bg-sky-100 text-sky-700 rounded-2xl">
            <Users className="w-8 h-8" />
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
          <div>
            <div className="text-xs text-slate-500 font-semibold">مجموع امتیازات فعال اعضا</div>
            <div className="text-3xl font-black text-amber-600 mt-2">{totalPoints.toLocaleString()} <span className="text-sm font-normal text-slate-400">امتیاز</span></div>
          </div>
          <div className="p-4 bg-amber-100 text-amber-700 rounded-2xl">
            <Award className="w-8 h-8" />
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
          <div>
            <div className="text-xs text-slate-500 font-semibold">مجموع موجودی کیف‌پول‌ها</div>
            <div className="text-3xl font-black text-emerald-600 mt-2">{totalWallet.toLocaleString()} <span className="text-sm font-normal text-slate-400">تومان</span></div>
          </div>
          <div className="p-4 bg-emerald-100 text-emerald-700 rounded-2xl">
            <Wallet className="w-8 h-8" />
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 className="text-sm font-bold text-slate-900 flex items-center gap-2">
            <TrendingUp className="w-4 h-4 text-sky-600" /> توزیع و پیشرفت سطوح مشتریان
          </h3>
          <div className="space-y-3 text-xs">
            <div>
              <div className="flex justify-between font-semibold text-slate-700 mb-1">
                <span>سطح برنزی (سطح ورودی)</span>
                <span>۸۵٪</span>
              </div>
              <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div className="bg-amber-700 h-full rounded-full" style={{ width: '85%' }}></div>
              </div>
            </div>

            <div>
              <div className="flex justify-between font-semibold text-slate-700 mb-1">
                <span>سطح نقره‌ای (خریداران متوسط)</span>
                <span>۱۲٪</span>
              </div>
              <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div className="bg-slate-400 h-full rounded-full" style={{ width: '12%' }}></div>
              </div>
            </div>

            <div>
              <div className="flex justify-between font-semibold text-slate-700 mb-1">
                <span>سطح طلایی (مشتریان وفادار)</span>
                <span>۳٪</span>
              </div>
              <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div className="bg-amber-400 h-full rounded-full" style={{ width: '3%' }}></div>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 className="text-sm font-bold text-slate-900 flex items-center gap-2">
            <Activity className="w-4 h-4 text-indigo-600" /> وضعیت سلامت و امنیت سیستم
          </h3>
          <div className="divide-y divide-slate-100 text-xs">
            <div className="py-2.5 flex justify-between">
              <span className="text-slate-600">پشتیبانی از جدول سفارشات ووکامرس (HPOS)</span>
              <span className="font-bold text-emerald-600 flex items-center gap-1"><ShieldCheck className="w-4 h-4" /> فعال و سازگار</span>
            </div>
            <div className="py-2.5 flex justify-between">
              <span className="text-slate-600">سیستم انقضای FIFO امتیازات</span>
              <span className="font-bold text-sky-600">آماده به‌کار</span>
            </div>
            <div className="py-2.5 flex justify-between">
              <span className="text-slate-600">امکانات امنیتی Nonce & Capability</span>
              <span className="font-bold text-emerald-600">تاییدشده</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
