# tasks.md — Karasu Buyers Club

فهرست تسک‌های اجرایی پروژه به‌تفکیک فازهای ROADMAP.md.

---

## فاز ۰ — Setup اولیه (Bootstrap)
- [x] مقداردهی اولیه Git و اتصال ریموت origin به GitHub (2026-07-27)
- [x] ساخت فایل `docs/tasks.md` و `docs/implementation-plan.md` (2026-07-27)
- [x] ساخت فایل اصلی افزونه `karasu-buyers-club.php` (هدر استاندارد وردپرس v0.1.0، برسی ووکامرس، لودر) (2026-07-27)
- [x] ساخت `composer.json` (PSR-4 autoload برای namespace `KarasuBuyersClub`) (2026-07-27)
- [x] ساخت `package.json` (Vite, React 18, Tailwind CSS, Lucide icons) (2026-07-27)
- [x] تنظیمات بیلد `vite.config.admin.js` و `vite.config.storefront.js` (2026-07-27)
- [x] تنظیمات `tailwind.config.js` و `postcss.config.js` (2026-07-27)
- [x] ساخت کلاس‌های پایه PHP (`Plugin`, `Activator`, `Deactivator`) در `includes/Core/` (2026-07-27)
- [x] اعلام سازگاری صریح با HPOS در `includes/Integrations/HPOS/CompatibilityDeclaration.php` (2026-07-27)
- [x] ساخت فایل‌های ورودی React فرانت‌اند (`src/admin/` و `src/storefront/`) (2026-07-27)
- [x] ساخت فایل تنظیمات لینتر و استانداردهای کدنویسی (`phpcs.xml`, `.gitignore`) (2026-07-27)
- [x] تست بیلد Vite و اجرا بدون خطا (2026-07-27)
- [x] بامپ و ثبت نسخه 0.1.0 در تمام فایل‌ها (2026-07-27)
- [x] ساخت فایل زیپ `karasu-buyers-club-0.1.0.zip` در ریشه پروژه (2026-07-27)
- [ ] Commit + Push + GitHub Release تگ `v0.1.0` با فایل زیپ پیوست

---

## فاز ۱ — لایه دیتابیس و موتور امتیازدهی هسته‌ای
- [ ] پیاده‌سازی `Database\Schema` و اولین Migration (تمام جداول)
- [ ] پیاده‌سازی `PointsRepository`
- [ ] پیاده‌سازی `PointsEngineService` (محاسبه امتیاز خرید + اکشن‌های اولیه ثبت‌نام/اولین خرید)
- [ ] منطق انقضای امتیاز (Cron + FIFO)
- [ ] تست عملکرد ثبت و محاسبه امتیاز در دیتابیس

---

## فاز ۲ — سطح‌بندی، کیف‌پول و مصرف امتیاز
- [ ] پیاده‌سازی `TierRepository` و `TierService`
- [ ] پیاده‌سازی `WalletRepository`، `WalletService` و `CheckoutWalletGateway`
- [ ] پیاده‌سازی `RedemptionService` و `CouponBridge`
- [ ] تست چرخه کامل کسب و مصرف امتیاز و کیف‌پول

---

## فاز ۳ — معرفی دوست و مناسبت‌های خاص
- [ ] پیاده‌سازی `ReferralRepository` و `ReferralService`
- [ ] پیاده‌سازی `OccasionService` (پشتیبانی تقویم شمسی + کرون مناسبت‌ها)
- [ ] تست سناریوهای معرفی دوست و پاداش تولد/سالگرد

---

## فاز ۴ — اطلاع‌رسانی چندکاناله
- [ ] پیاده‌سازی `SMS_Provider_Interface` و تامین‌کنندگان Kavenegar، Mellipayamak، FarazSMS
- [ ] پیاده‌سازی `NotificationDispatcherService` و `NotificationRepository`
- [ ] تنظیمات قالب‌های پیام و Rate limiting
- [ ] تست ارسال پیامک، ایمیل و اعلان داخلی

---

## فاز ۵ — REST API کامل
- [ ] پیاده‌سازی `RestServiceProvider` و کنترلرهای تمام اندپوئینت‌ها
- [ ] اعمال کنترل دسترسی، Nonce و Capability
- [ ] تست تمامی route ها

---

## فاز ۶ — رابط کاربری مشتری (Storefront React)
- [ ] توسعه کامپوننت‌های React مشتری (My Account + صفحه اختصاصی باشگاه)
- [ ] اتصال به REST API و استایل‌دهی RTL با Tailwind

---

## فاز ۷ — پنل مدیریت ادمین (Admin React Panel)
- [ ] توسعه پنل ادمین React (تمام تب‌های مدیریت، تنظیمات، اعضا و گزارش‌ها)
- [ ] اتصال به API و خروجی CSV/Excel

---

## فاز ۸ — یکپارچگی Elementor
- [ ] پیاده‌سازی ویجت‌های `Widget_Loyalty_Status`, `Widget_Tier_Badge`, `Widget_Club_CTA`
- [ ] تست عملکرد و رندر در Elementor Editor

---

## فاز ۹ — سخت‌سازی امنیتی و تست نهایی
- [ ] بازبینی امنیتی کامل (Sanitization/Escaping/Nonce/Capability)
- [ ] تست‌های HPOS و بار
- [ ] رفع خطاهای احتمالی

---

## فاز ۱۰ — بسته‌بندی و خروجی نهایی v1.0.0
- [ ] به‌روزرسانی نسخه ۱.۰.۰، ساخت زیپ نهایی و انتشار GitHub Release
