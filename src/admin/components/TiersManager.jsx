import React, { useState, useEffect } from 'react';
import { Award, Plus, Trash2, Edit3, Save, Shield, CheckCircle2 } from 'lucide-react';

export default function TiersManager() {
  const [tiers, setTiers] = useState([]);
  const [editingTier, setEditingTier] = useState(null);
  const [formData, setFormData] = useState({ id: 0, name: '', threshold: 0, sort_order: 0 });
  const [message, setMessage] = useState(null);
  const [loading, setLoading] = useState(false);

  const fetchTiers = async () => {
    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    try {
      const res = await fetch(`${restUrl}/tiers`);
      const json = await res.json();
      if (json.success) setTiers(json.data);
    } catch (e) {}
  };

  useEffect(() => {
    fetchTiers();
  }, []);

  const handleEdit = (tier) => {
    setEditingTier(tier);
    setFormData({
      id: tier.id,
      name: tier.name,
      threshold: tier.threshold,
      sort_order: tier.sort_order || 0,
    });
    setMessage(null);
  };

  const handleNew = () => {
    setEditingTier({ id: 0 });
    setFormData({ id: 0, name: '', threshold: 0, sort_order: tiers.length + 1 });
    setMessage(null);
  };

  const handleDelete = async (id) => {
    if (!window.confirm('آیا از حذف این سطح اطمینان دارید؟')) return;

    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/admin/tiers/${id}`, {
        method: 'DELETE',
        headers: { 'X-WP-Nonce': nonce },
      });
      const json = await res.json();
      if (json.success) {
        setMessage({ type: 'success', text: 'سطح با موفقیت حذف شد.' });
        fetchTiers();
      }
    } catch (e) {}
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setMessage(null);

    const restUrl = window.kbcAdminData?.restUrl || '/wp-json/karasu-buyers-club/v1';
    const nonce = window.kbcAdminData?.nonce || '';

    try {
      const res = await fetch(`${restUrl}/admin/tiers`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce,
        },
        body: JSON.stringify(formData),
      });
      const json = await res.json();
      if (json.success) {
        setMessage({ type: 'success', text: 'اطلاعات سطح با موفقیت ذخیره شد.' });
        setEditingTier(null);
        fetchTiers();
      }
    } catch (e) {} finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-6 max-w-4xl">
      <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div className="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
          <div>
            <h3 className="text-lg font-bold text-slate-900 flex items-center gap-2">
              <Shield className="w-5 h-5 text-amber-500" /> مدیریت سطوح مشتریان (Tiers)
            </h3>
            <p className="text-xs text-slate-500 mt-1">تعریف و ایجاد سطوح وفاداری مشتریان بر اساس مجموع خریدهای قبلی (LTV)</p>
          </div>
          <button
            onClick={handleNew}
            className="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 cursor-pointer transition-all shadow-sm"
          >
            <Plus className="w-4 h-4" /> تعریف سطح جدید
          </button>
        </div>

        {message && (
          <div className="p-4 bg-emerald-50 text-emerald-800 rounded-xl text-xs font-medium mb-4 flex items-center gap-2">
            <CheckCircle2 className="w-4 h-4" /> {message.text}
          </div>
        )}

        <div className="divide-y divide-slate-100">
          {tiers.map((t) => (
            <div key={t.id} className="py-4 flex items-center justify-between hover:bg-slate-50 p-2 rounded-xl transition-all">
              <div className="flex items-center gap-3">
                <div className="p-3 bg-amber-100 text-amber-800 rounded-xl">
                  <Award className="w-6 h-6" />
                </div>
                <div>
                  <div className="font-bold text-slate-900 text-sm">{t.name}</div>
                  <div className="text-xs text-slate-500 mt-0.5">
                    آستانه ارتقا: <span className="font-bold text-slate-700">{parseFloat(t.threshold).toLocaleString()} تومان</span>
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-2">
                <button
                  onClick={() => handleEdit(t)}
                  className="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg cursor-pointer transition-all flex items-center gap-1 text-xs"
                >
                  <Edit3 className="w-3.5 h-3.5" /> ویرایش
                </button>
                <button
                  onClick={() => handleDelete(t.id)}
                  className="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg cursor-pointer transition-all flex items-center gap-1 text-xs"
                >
                  <Trash2 className="w-3.5 h-3.5" /> حذف
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      {editingTier && (
        <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl">
            <h4 className="font-bold text-slate-900 text-base mb-4">
              {formData.id > 0 ? `ویرایش سطح: ${formData.name}` : 'تعریف سطح جدید'}
            </h4>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">نام سطح</label>
                <input
                  type="text"
                  required
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  placeholder="مثال: نقره‌ای / طلایی"
                  className="w-full p-3 rounded-xl border border-slate-200 font-bold text-xs"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">آستانه خرید (تومان)</label>
                <input
                  type="number"
                  required
                  min="0"
                  value={formData.threshold}
                  onChange={(e) => setFormData({ ...formData, threshold: e.target.value })}
                  className="w-full p-3 rounded-xl border border-slate-200 font-bold text-xs"
                />
                <p className="text-[10px] text-slate-400 mt-1">کاربر پس از رسیدن مجموع خریدهایش به این مبلغ به این سطح ارتقا می‌یابد.</p>
              </div>

              <div className="flex gap-2 pt-2">
                <button
                  type="submit"
                  disabled={loading}
                  className="flex-1 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl cursor-pointer transition-all flex items-center justify-center gap-1"
                >
                  <Save className="w-4 h-4" /> {loading ? 'در حال ذخیره...' : 'ذخیره سطح'}
                </button>
                <button
                  type="button"
                  onClick={() => setEditingTier(null)}
                  className="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl cursor-pointer"
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
