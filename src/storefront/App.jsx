import React from 'react';
import { Gift, Star, Wallet, UserCheck } from 'lucide-react';

export default function App() {
  return (
    <div className="kbc-storefront-wrap p-4 bg-slate-50 rounded-2xl border border-slate-200 dir-rtl text-slate-800" dir="rtl">
      <div className="flex items-center justify-between border-b border-slate-200 pb-4 mb-4">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold shadow-md">
            <Star className="w-5 h-5 fill-current" />
          </div>
          <div>
            <h2 className="text-lg font-bold text-slate-900">باشگاه مشتریان Karasu</h2>
            <p className="text-xs text-slate-500">امتیازهای شما و پاداش‌های عضویت</p>
          </div>
        </div>
        <span className="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
          سطح برنزی
        </span>
      </div>

      <div className="grid grid-cols-2 md:grid-cols-3 gap-3 text-center">
        <div className="p-3 bg-white rounded-xl border border-slate-200">
          <div className="text-xs text-slate-500">امتیاز کل</div>
          <div className="text-lg font-bold text-amber-600 mt-0.5">۰ امتیاز</div>
        </div>
        <div className="p-3 bg-white rounded-xl border border-slate-200">
          <div className="text-xs text-slate-500">موجودی کیف‌پول</div>
          <div className="text-lg font-bold text-emerald-600 mt-0.5">۰ تومان</div>
        </div>
        <div className="p-3 bg-white rounded-xl border border-slate-200 col-span-2 md:col-span-1">
          <div className="text-xs text-slate-500">معرفی دوست</div>
          <div className="text-sm font-semibold text-slate-700 mt-1 flex items-center justify-center gap-1">
            <UserCheck className="w-4 h-4 text-blue-500" /> کد اختصاصی
          </div>
        </div>
      </div>
    </div>
  );
}
