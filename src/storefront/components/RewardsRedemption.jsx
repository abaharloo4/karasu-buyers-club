import React, { useState } from 'react';
import { Gift, Wallet, CheckCircle2, AlertCircle } from 'lucide-react';

export default function RewardsRedemption({ pointsBalance, onRedeemSuccess }) {
  const [points, setPoints] = useState(50);
  const [rewardType, setRewardType] = useState('coupon');
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState(null);

  const handleRedeem = async (e) => {
    e.preventDefault();
    setLoading(true);
    setMessage(null);

    const restUrl = window.kbcData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/points/redeem`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
        body: JSON.stringify({ points: parseFloat(points), reward_type: rewardType }),
      });

      const data = await res.json();

      if (data.success) {
        setMessage({ type: 'success', text: data.message || 'پاداش با موفقیت دریافت شد.' });
        if (onRedeemSuccess) onRedeemSuccess();
      } else {
        setMessage({ type: 'error', text: data.message || 'خطا در فرآیند تبدیل امتیاز.' });
      }
    } catch (err) {
      setMessage({ type: 'error', text: 'ارتباط با سرور برقرار نشد.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
      <h3 className="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
        <Gift className="w-5 h-5 text-amber-500" /> تبدیل امتیاز به پاداش
      </h3>

      {message && (
        <div className={`p-4 rounded-xl mb-4 text-sm font-medium flex items-center gap-2 ${message.type === 'success' ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800'}`}>
          {message.type === 'success' ? <CheckCircle2 className="w-5 h-5" /> : <AlertCircle className="w-5 h-5" />}
          {message.text}
        </div>
      )}

      <form onSubmit={handleRedeem} className="space-y-4">
        <div>
          <label className="block text-xs font-semibold text-slate-600 mb-1">نوع پاداش</label>
          <div className="grid grid-cols-2 gap-3">
            <button
              type="button"
              onClick={() => setRewardType('coupon')}
              className={`p-3 rounded-xl border text-sm font-semibold flex items-center justify-center gap-2 cursor-pointer transition-all ${rewardType === 'coupon' ? 'border-amber-500 bg-amber-50 text-amber-900 shadow-sm' : 'border-slate-200 text-slate-600'}`}
            >
              <Gift className="w-4 h-4" /> کد تخفیف سفارش
            </button>
            <button
              type="button"
              onClick={() => setRewardType('wallet')}
              className={`p-3 rounded-xl border text-sm font-semibold flex items-center justify-center gap-2 cursor-pointer transition-all ${rewardType === 'wallet' ? 'border-emerald-500 bg-emerald-50 text-emerald-900 shadow-sm' : 'border-slate-200 text-slate-600'}`}
            >
              <Wallet className="w-4 h-4" /> شارژ کیف‌پول
            </button>
          </div>
        </div>

        <div>
          <label className="block text-xs font-semibold text-slate-600 mb-1">تعداد امتیاز مصرفی</label>
          <input
            type="number"
            min="1"
            max={pointsBalance}
            value={points}
            onChange={(e) => setPoints(e.target.value)}
            className="w-full p-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-900 font-bold"
          />
        </div>

        <button
          type="submit"
          disabled={loading || pointsBalance < points}
          className="w-full p-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition-all disabled:opacity-50 cursor-pointer shadow-md"
        >
          {loading ? 'در حال ثبت...' : 'دریافت پاداش'}
        </button>
      </form>
    </div>
  );
}
