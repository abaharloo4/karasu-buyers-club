import React from 'react';
import { ShieldCheck, Award, Wallet, Users } from 'lucide-react';

export default function App() {
  return (
    <div className="p-6 bg-slate-50 min-h-screen text-slate-800 dir-rtl" dir="rtl">
      <header className="mb-8 flex items-center justify-between border-b pb-4 bg-white p-4 rounded-xl shadow-sm border-slate-200">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-karasu-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
            K
          </div>
          <div>
            <h1 className="text-2xl font-bold text-slate-900">باشگاه مشتریان Karasu (پنل مدیریت)</h1>
            <p className="text-sm text-slate-500">نسخه ۰.۱.۰ — اسکلت اولیه افزونه</p>
          </div>
        </div>
        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
          <ShieldCheck className="w-4 h-4" /> فعال و اماده توسعه
        </span>
      </header>

      <main className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="p-3 bg-amber-100 text-amber-700 rounded-lg">
            <Award className="w-8 h-8" />
          </div>
          <div>
            <div className="text-sm text-slate-500 font-medium">موتور امتیازدهی</div>
            <div className="text-lg font-bold text-slate-900 mt-1">آماده‌سازی لایه دیتابیس</div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="p-3 bg-blue-100 text-blue-700 rounded-lg">
            <Wallet className="w-8 h-8" />
          </div>
          <div>
            <div className="text-sm text-slate-500 font-medium">کیف‌پول و تخفیف‌ها</div>
            <div className="text-lg font-bold text-slate-900 mt-1">آماده‌سازی ساختار کیف‌پول</div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="p-3 bg-purple-100 text-purple-700 rounded-lg">
            <Users className="w-8 h-8" />
          </div>
          <div>
            <div className="text-sm text-slate-500 font-medium">سطوح مشتریان (Tiers)</div>
            <div className="text-lg font-bold text-slate-900 mt-1">ساختار ارتقا بر اساس LTV</div>
          </div>
        </div>
      </main>
    </div>
  );
}
