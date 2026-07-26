import React from 'react';
import { Users, Award, Wallet, TrendingUp } from 'lucide-react';

export default function AdminDashboard({ members = [] }) {
  const totalMembers = members.length;
  const totalPoints = members.reduce((sum, m) => sum + (m.points_balance || 0), 0);
  const totalWallet = members.reduce((sum, m) => sum + (m.wallet_balance || 0), 0);

  return (
    <div className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="p-4 bg-sky-100 text-sky-700 rounded-2xl">
            <Users className="w-8 h-8" />
          </div>
          <div>
            <div className="text-xs text-slate-500 font-semibold">تعداد کل اعضای باشگاه</div>
            <div className="text-2xl font-extrabold text-slate-900 mt-1">{totalMembers.toLocaleString()} نفر</div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="p-4 bg-amber-100 text-amber-700 rounded-2xl">
            <Award className="w-8 h-8" />
          </div>
          <div>
            <div className="text-xs text-slate-500 font-semibold">کل امتیازات فعال کاربران</div>
            <div className="text-2xl font-extrabold text-amber-600 mt-1">{totalPoints.toLocaleString()} امتیاز</div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
          <div className="p-4 bg-emerald-100 text-emerald-700 rounded-2xl">
            <Wallet className="w-8 h-8" />
          </div>
          <div>
            <div className="text-xs text-slate-500 font-semibold">کل موجودی کیف‌پول اعضا</div>
            <div className="text-2xl font-extrabold text-emerald-600 mt-1">{totalWallet.toLocaleString()} تومان</div>
          </div>
        </div>
      </div>
    </div>
  );
}
