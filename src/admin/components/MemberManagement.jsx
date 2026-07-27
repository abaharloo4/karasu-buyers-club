import React, { useState } from 'react';
import { Search, Edit3, Award, Wallet, Eye, History, X, CheckCircle2 } from 'lucide-react';

export default function MemberManagement({ members = [], onRefresh }) {
  const [search, setSearch] = useState('');
  const [selectedMember, setSelectedMember] = useState(null);
  const [memberHistory, setMemberHistory] = useState(null);
  const [adjustType, setAdjustType] = useState('points');
  const [adjustAmount, setAdjustAmount] = useState(0);
  const [loadingHistory, setLoadingHistory] = useState(false);

  const filtered = members.filter(
    (m) =>
      m.display_name?.toLowerCase().includes(search.toLowerCase()) ||
      m.user_email?.toLowerCase().includes(search.toLowerCase())
  );

  const handleInspect = async (member) => {
    setSelectedMember(member);
    setLoadingHistory(true);
    setMemberHistory(null);

    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/admin/members/${member.id}/history`, {
        headers: { 'X-WP-Nonce': nonce },
      });
      const json = await res.json();
      if (json.success) setMemberHistory(json.data);
    } catch (e) {} finally {
      setLoadingHistory(false);
    }
  };

  const handleAdjust = async (e) => {
    e.preventDefault();
    if (!selectedMember) return;

    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/admin/members/${selectedMember.id}/adjust`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
        body: JSON.stringify({ type: adjustType, amount: parseFloat(adjustAmount) }),
      });
      const json = await res.json();
      if (json.success) {
        alert('تغییرات اعتباری با موفقیت اعمال شد.');
        handleInspect(selectedMember);
        if (onRefresh) onRefresh();
      }
    } catch (e) {}
  };

  return (
    <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
      <div className="flex justify-between items-center border-b border-slate-100 pb-4">
        <div>
          <h3 className="text-lg font-bold text-slate-900">مدیریت و مانیتورینگ اعضای باشگاه</h3>
          <p className="text-xs text-slate-500 mt-0.5">مشاهده ریز تاریخچه تراکنش‌ها، کیف‌پول و اصلاح دست‌اندرکار اعتبارات</p>
        </div>
        <div className="relative w-64">
          <Search className="w-4 h-4 text-slate-400 absolute left-3 top-3" />
          <input
            type="text"
            placeholder="جستجوی نام یا ایمیل..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500"
          />
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full text-right text-xs">
          <thead className="bg-slate-50 text-slate-500 border-b border-slate-200">
            <tr>
              <th className="p-3">شناسه</th>
              <th className="p-3">نام کاربر</th>
              <th className="p-3">ایمیل</th>
              <th className="p-3">موجودی امتیاز</th>
              <th className="p-3">موجودی کیف‌پول</th>
              <th className="p-3">عملیات مانیتورینگ</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {filtered.map((m) => (
              <tr key={m.id} className="hover:bg-slate-50">
                <td className="p-3 text-slate-400 font-mono">#{m.id}</td>
                <td className="p-3 font-bold text-slate-900">{m.display_name}</td>
                <td className="p-3 text-slate-500">{m.user_email}</td>
                <td className="p-3 font-bold text-amber-600">{(m.points_balance || 0).toLocaleString()}</td>
                <td className="p-3 font-bold text-emerald-600">{(m.wallet_balance || 0).toLocaleString()} تومان</td>
                <td className="p-3">
                  <button
                    onClick={() => handleInspect(m)}
                    className="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold rounded-lg cursor-pointer flex items-center gap-1.5 transition-all text-xs"
                  >
                    <Eye className="w-3.5 h-3.5" /> مشاهده پرونده و ویرایش
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {selectedMember && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
          <div className="bg-white rounded-2xl p-6 max-w-2xl w-full shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
            <div className="flex justify-between items-start border-b border-slate-100 pb-3">
              <div>
                <h4 className="font-extrabold text-slate-900 text-base">پرونده کاربر: {selectedMember.display_name}</h4>
                <p className="text-xs text-slate-400 mt-0.5">{selectedMember.user_email}</p>
              </div>
              <button
                onClick={() => setSelectedMember(null)}
                className="p-1 text-slate-400 hover:text-slate-600 cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {loadingHistory ? (
              <div className="py-8 text-center text-xs text-slate-500">در حال دریافت ریز سوابق کاربر...</div>
            ) : (
              <>
                <div className="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl">
                  <div>
                    <span className="text-[10px] text-slate-400">موجودی امتیاز</span>
                    <div className="text-xl font-extrabold text-amber-600">{selectedMember.points_balance} امتیاز</div>
                  </div>
                  <div>
                    <span className="text-[10px] text-slate-400">موجودی کیف‌پول</span>
                    <div className="text-xl font-extrabold text-emerald-600">{selectedMember.wallet_balance} تومان</div>
                  </div>
                </div>

                <form onSubmit={handleAdjust} className="p-4 border border-slate-200 rounded-xl bg-sky-50/50 space-y-3">
                  <h5 className="text-xs font-bold text-slate-900 flex items-center gap-1">
                    <Edit3 className="w-4 h-4 text-sky-600" /> اصلاح دستی اعتبارات کاربر
                  </h5>
                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <select
                        value={adjustType}
                        onChange={(e) => setAdjustType(e.target.value)}
                        className="w-full p-2.5 rounded-lg border border-slate-200 text-xs font-semibold"
                      >
                        <option value="points">امتیاز (Points)</option>
                        <option value="wallet">کیف‌پول (Wallet)</option>
                      </select>
                    </div>
                    <div>
                      <input
                        type="number"
                        placeholder="مقدار (مثبت یا منفی)"
                        value={adjustAmount}
                        onChange={(e) => setAdjustAmount(e.target.value)}
                        className="w-full p-2.5 rounded-lg border border-slate-200 text-xs font-bold"
                      />
                    </div>
                  </div>
                  <button type="submit" className="w-full py-2 bg-sky-600 text-white font-bold text-xs rounded-lg cursor-pointer">
                    اعمال تغییر اعتبار
                  </button>
                </form>

                {memberHistory?.points_history && (
                  <div>
                    <h5 className="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1">
                      <History className="w-4 h-4 text-amber-500" /> سوابق اخیر امتیازات
                    </h5>
                    <div className="max-h-36 overflow-y-auto divide-y divide-slate-100 border border-slate-100 rounded-lg p-2 text-xs">
                      {memberHistory.points_history.map((tx) => (
                        <div key={tx.id} className="py-1.5 flex justify-between text-[11px]">
                          <span>{tx.source} ({tx.type})</span>
                          <span className="font-bold text-amber-600">{tx.amount}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </>
            )}
          </div>
        </div>
      )}
    </div>
  );
}
