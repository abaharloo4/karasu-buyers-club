import React from 'react';
import { Award, Wallet, Star, ShieldCheck } from 'lucide-react';

export default function StatusSummary({ summary }) {
  const points = summary?.points_balance || 0;
  const wallet = summary?.wallet_balance || 0;
  const tier = summary?.current_tier || { name: 'برنزی', threshold: 0 };

  return (
    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div className="bg-gradient-to-br from-amber-500 to-amber-600 text-white p-5 rounded-2xl shadow-sm relative overflow-hidden">
        <div className="flex justify-between items-start">
          <div>
            <span className="text-xs text-amber-100 font-medium">موجودی امتیاز</span>
            <div className="text-3xl font-extrabold mt-1">{points.toLocaleString()} <span className="text-sm font-normal">امتیاز</span></div>
          </div>
          <div className="p-3 bg-white/20 backdrop-blur-md rounded-xl">
            <Star className="w-6 h-6 fill-current text-white" />
          </div>
        </div>
      </div>

      <div className="bg-gradient-to-br from-emerald-600 to-teal-700 text-white p-5 rounded-2xl shadow-sm relative overflow-hidden">
        <div className="flex justify-between items-start">
          <div>
            <span className="text-xs text-emerald-100 font-medium">موجودی کیف‌پول</span>
            <div className="text-3xl font-extrabold mt-1">{wallet.toLocaleString()} <span className="text-sm font-normal">تومان</span></div>
          </div>
          <div className="p-3 bg-white/20 backdrop-blur-md rounded-xl">
            <Wallet className="w-6 h-6 text-white" />
          </div>
        </div>
      </div>

      <div className="bg-gradient-to-br from-sky-600 to-blue-700 text-white p-5 rounded-2xl shadow-sm relative overflow-hidden">
        <div className="flex justify-between items-start">
          <div>
            <span className="text-xs text-sky-100 font-medium">سطح عضویت</span>
            <div className="text-2xl font-extrabold mt-1">{tier.name}</div>
          </div>
          <div className="p-3 bg-white/20 backdrop-blur-md rounded-xl">
            <Award className="w-6 h-6 text-white" />
          </div>
        </div>
      </div>
    </div>
  );
}
