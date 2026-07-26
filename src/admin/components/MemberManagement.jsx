import React, { useState } from 'react';
import { Search, Edit3, Award, Wallet } from 'lucide-react';

export default function MemberManagement({ members = [], onRefresh }) {
  const [search, setSearch] = useState('');
  const [selectedMember, setSelectedMember] = useState(null);
  const [adjustType, setAdjustType] = useState('points');
  const [adjustAmount, setAdjustAmount] = useState(0);

  const filtered = members.filter(
    (m) =>
      m.display_name?.toLowerCase().includes(search.toLowerCase()) ||
      m.user_email?.toLowerCase().includes(search.toLowerCase())
  );

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
        alert('تغییرات با موفقیت ثبت شد.');
        setSelectedMember(null);
        if (onRefresh) onRefresh();
      }
    } catch (e) {}
  };

  return (
    <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
      <div className="flex justify-between items-center border-b border-slate-100 pb-4">
        <h3 className="text-lg font-bold text-slate-900">مدیریت اعضای باشگاه</h3>
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
              <th className="p-3">کاربر</th>
              <th className="p-3">ایمیل</th>
              <th className="p-3">موجودی امتیاز</th>
              <th className="p-3">موجودی کیف‌پول</th>
              <th className="p-3">عملیات</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {filtered.map((m) => (
              <tr key={m.id} className="hover:bg-slate-50">
                <td className="p-3 font-bold text-slate-900">{m.display_name}</td>
                <td className="p-3 text-slate-500">{m.user_email}</td>
                <td className="p-3 font-bold text-amber-600">{(m.points_balance || 0).toLocaleString()}</td>
                <td className="p-3 font-bold text-emerald-600">{(m.wallet_balance || 0).toLocaleString()} تومان</td>
                <td className="p-3">
                  <button
                    onClick={() => setSelectedMember(m)}
                    className="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg cursor-pointer flex items-center gap-1"
                  >
                    <Edit3 className="w-3.5 h-3.5" /> ویرایش
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {selectedMember && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h4 className="font-bold text-slate-900 text-base mb-4">ویرایش دستی اعتبارات کاربر: {selectedMember.display_name}</h4>
            <form onSubmit={handleAdjust} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">نوع اعتبار</label>
                <select
                  value={adjustType}
                  onChange={(e) => setAdjustType(e.target.value)}
                  className="w-full p-3 rounded-xl border border-slate-200 font-semibold text-xs"
                >
                  <option value="points">امتیاز (Points)</option>
                  <option value="wallet">کیف‌پول (Wallet)</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-600 mb-1">مبلغ / تعداد تغییر (مثبت یا منفی)</label>
                <input
                  type="number"
                  value={adjustAmount}
                  onChange={(e) => setAdjustAmount(e.target.value)}
                  className="w-full p-3 rounded-xl border border-slate-200 font-bold"
                />
              </div>

              <div className="flex gap-2 pt-2">
                <button type="submit" className="flex-1 py-2.5 bg-sky-600 text-white font-bold text-xs rounded-xl cursor-pointer">
                  اعمال تغییر
                </button>
                <button
                  type="button"
                  onClick={() => setSelectedMember(null)}
                  className="px-4 py-2.5 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl cursor-pointer"
                >
                  انصراف
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
