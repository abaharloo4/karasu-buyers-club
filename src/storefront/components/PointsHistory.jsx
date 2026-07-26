import React from 'react';
import { ArrowUpLeft, ArrowDownRight, Clock } from 'lucide-react';

export default function PointsHistory({ history }) {
  if (!history || history.length === 0) {
    return (
      <div className="bg-white rounded-2xl p-8 text-center text-slate-500 border border-slate-200">
        تراکنشی در سوابق امتیازات شما ثبت نشده است.
      </div>
    );
  }

  return (
    <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div className="p-4 border-b border-slate-100 font-bold text-slate-800">
        تاریخچه تراکنش‌های امتیاز
      </div>
      <div className="divide-y divide-slate-100">
        {history.map((tx) => {
          const isEarned = tx.type === 'earned' || (tx.type === 'adjusted' && parseFloat(tx.amount) > 0);
          return (
            <div key={tx.id} className="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
              <div className="flex items-center gap-3">
                <div className={`p-2 rounded-xl ${isEarned ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`}>
                  {isEarned ? <ArrowUpLeft className="w-5 h-5" /> : <ArrowDownRight className="w-5 h-5" />}
                </div>
                <div>
                  <div className="font-semibold text-slate-900 text-sm">
                    {tx.source === 'purchase' ? 'خرید محصول' : tx.source === 'signup' ? 'پاداش ثبت‌نام' : tx.source}
                  </div>
                  <div className="text-xs text-slate-400 mt-0.5">{tx.created_at}</div>
                </div>
              </div>
              <div className={`font-bold text-sm ${isEarned ? 'text-emerald-600' : 'text-rose-600'}`}>
                {isEarned ? '+' : '-'}{Math.abs(parseFloat(tx.amount))} امتیاز
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
