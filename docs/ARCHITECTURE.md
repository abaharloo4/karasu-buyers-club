# ARCHITECTURE.md — Karasu Buyers Club

**نسخه سند:** 1.0.0
**پیش‌نیاز مطالعه:** PRD.md

---

## ۱. اصول کلی معماری

- **معماری هیبریدی PHP + React**، دقیقاً مطابق الگوی فنی karasu-woo-pannel: بک‌اند PHP مسئول منطق کسب‌وکار، امنیت و دیتابیس؛ فرانت‌اند React مسئول تعامل کاربر (پنل مدیریت + رابط مشتری)، هیدریت‌شده روی صفحات وردپرس/wp-admin
- **OOP کامل** با namespace اختصاصی، بدون تابع‌های سراسری پراکنده (به‌جز فایل‌های Bootstrap/Bridge ضروری وردپرس)
- **جداکننده منطق و نمایش:** Service Layer (منطق کسب‌وکار) از Controller Layer (REST endpoints) و از Repository Layer (دسترسی دیتابیس) کاملاً جدا هستن
- **PSR-4 Autoloading** برای تمام کلاس‌های PHP
- **دو بیلد Vite مجزا:** یکی برای پنل ادمین (`admin`)، یکی برای رابط مشتری (`storefront`)

---

## ۲. ساختار پوشه‌بندی پروژه

```
karasu-buyers-club/
├── karasu-buyers-club.php                # فایل اصلی افزونه (Bootstrap، Header استاندارد وردپرس)
├── uninstall.php                         # پاک‌سازی کامل دیتا هنگام حذف افزونه
├── composer.json                         # PSR-4 autoload + وابستگی‌های PHP
├── package.json                          # وابستگی‌های Node/Vite/React/Tailwind
├── vite.config.admin.js
├── vite.config.storefront.js
├── tailwind.config.js
│
├── includes/                             # هسته PHP (namespace: KarasuBuyersClub)
│   ├── Core/
│   │   ├── Plugin.php                    # کلاس Singleton اصلی راه‌انداز افزونه
│   │   ├── Activator.php                 # منطق فعال‌سازی (ساخت جداول، نسخه‌گذاری)
│   │   ├── Deactivator.php
│   │   └── Autoloader.php                # (در صورت عدم استفاده از Composer autoload)
│   │
│   ├── Database/
│   │   ├── Schema.php                    # تعریف و migrate جداول اختصاصی
│   │   ├── Migrations/
│   │   │   ├── Migration_1_0_0.php
│   │   │   └── ...
│   │   └── Repositories/
│   │       ├── PointsRepository.php
│   │       ├── WalletRepository.php
│   │       ├── TierRepository.php
│   │       ├── ReferralRepository.php
│   │       └── NotificationRepository.php
│   │
│   ├── Services/                         # منطق کسب‌وکار (بدون آگاهی از HTTP/DB خام)
│   │   ├── PointsEngineService.php
│   │   ├── RedemptionService.php
│   │   ├── WalletService.php
│   │   ├── TierService.php
│   │   ├── ReferralService.php
│   │   ├── OccasionService.php           # تولد/سالگرد عضویت
│   │   └── NotificationDispatcherService.php
│   │
│   ├── Integrations/
│   │   ├── WooCommerce/
│   │   │   ├── OrderHooks.php            # اتصال به woocommerce_order_status_completed و...
│   │   │   ├── CheckoutWalletGateway.php # اعمال کیف‌پول در چک‌اوت
│   │   │   └── CouponBridge.php          # ساخت کد تخفیف پویا از امتیاز
│   │   ├── SMS/
│   │   │   ├── SMS_Provider_Interface.php
│   │   │   ├── KavenegarProvider.php
│   │   │   ├── MellipayamakProvider.php
│   │   │   └── FarazSmsProvider.php
│   │   ├── Elementor/
│   │   │   ├── Widget_Loyalty_Status.php
│   │   │   ├── Widget_Tier_Badge.php
│   │   │   └── Widget_Club_CTA.php
│   │   └── HPOS/
│   │       └── CompatibilityDeclaration.php
│   │
│   ├── REST/
│   │   ├── RestServiceProvider.php       # ثبت همه route ها
│   │   ├── Controllers/
│   │   │   ├── PointsController.php
│   │   │   ├── WalletController.php
│   │   │   ├── TierController.php
│   │   │   ├── ReferralController.php
│   │   │   ├── NotificationController.php
│   │   │   └── AdminSettingsController.php
│   │   └── Permissions/
│   │       └── PermissionChecker.php     # کنترل capability و nonce
│   │
│   ├── Admin/
│   │   ├── AdminMenu.php                 # ثبت صفحات wp-admin
│   │   └── AssetsLoader.php              # enqueue بیلد React ادمین
│   │
│   ├── Storefront/
│   │   ├── MyAccountTab.php              # ثبت تب در My Account
│   │   ├── ClubPageController.php        # صفحه اختصاصی باشگاه (Shortcode/Template)
│   │   └── AssetsLoader.php              # enqueue بیلد React مشتری
│   │
│   └── Utils/
│       ├── Sanitizer.php
│       ├── Formatter.php                 # قالب‌بندی اعداد فارسی/تقویم شمسی
│       └── Logger.php                    # ثبت خطا در DEBUG_LOG
│
├── src/                                  # سورس React (قبل از بیلد)
│   ├── admin/
│   │   ├── main.jsx
│   │   ├── App.jsx
│   │   └── components/ (Dashboard, PointsSettings, TiersManager, ...)
│   └── storefront/
│       ├── main.jsx
│       ├── App.jsx
│       └── components/ (MyAccountWidget, ClubDashboard, ...)
│
├── assets/
│   └── dist/                             # خروجی بیلد Vite (admin.js, storefront.js, css)
│
├── languages/                            # فایل‌های ترجمه .pot/.po/.mo
│
└── docs/                                 # مستندات پروژه (PRD, ARCHITECTURE, GUIDELINES, ROADMAP, DEBUG_LOG, AGENTS, tasks, implementation-plan)
```

---

## ۳. لایه دیتابیس

### ۳.۱ اصل کلی
به‌جای استفاده از User Meta پراکنده، تمام داده‌های تراکنشی در **جداول اختصاصی** (Custom Tables) با پیشوند `{$wpdb->prefix}kbc_` نگهداری می‌شن تا کوئری‌پذیری، ایندکس‌گذاری و مقیاس‌پذیری تضمین بشه.

### ۳.۲ جداول اصلی

| جدول | توضیح |
|---|---|
| `kbc_points_ledger` | دفتر تراکنش‌های امتیاز (کسب/مصرف/انقضا) — user_id, amount, type, source, expires_at, created_at |
| `kbc_wallet_ledger` | دفتر تراکنش‌های کیف‌پول — user_id, amount, type (credit/debit), reference, created_at |
| `kbc_tiers` | تعریف سطوح — id, name, threshold, benefits (JSON), sort_order |
| `kbc_user_tier_history` | تاریخچه ارتقای سطح کاربران — user_id, tier_id, achieved_at |
| `kbc_referrals` | کدهای معرف و روابط — user_id, referral_code, referred_by_user_id, status |
| `kbc_redemption_rules` | قوانین تبدیل امتیاز به پاداش — type, rate, is_active |
| `kbc_notifications_log` | تاریخچه اطلاع‌رسانی‌های ارسال‌شده — user_id, channel, template_key, status, sent_at |

### ۳.۳ مدیریت نسخه دیتابیس
- شماره نسخه schema در `wp_options` (`kbc_db_version`) نگهداری می‌شه
- کلاس `Database\Schema` هنگام فعال‌سازی یا آپدیت افزونه، migration های لازم رو با `dbDelta()` اجرا می‌کنه
- هر migration در فایل جدا (`Migration_x_y_z.php`) برای قابلیت پیگیری تغییرات

### ۳.۴ سازگاری با HPOS
- هیچ کوئری مستقیمی به جداول قدیمی `wp_postmeta` سفارش انجام نمی‌شه؛ تمام دسترسی به داده سفارش از طریق `wc_get_order()` و CRUD API ووکامرس
- اعلام صریح سازگاری HPOS از طریق `FeaturesUtil::declare_compatibility()` در `Integrations/HPOS/CompatibilityDeclaration.php`

---

## ۴. REST API

### ۴.۱ ساختار Namespace
```
karasu-buyers-club/v1
```

### ۴.۲ Endpointهای کلیدی (نمونه)

| Method | Route | توضیح | دسترسی |
|---|---|---|---|
| GET | `/points/summary` | خلاصه امتیاز/سطح/کیف‌پول کاربر جاری | کاربر لاگین‌شده |
| GET | `/points/history` | تاریخچه تراکنش‌های امتیاز | کاربر لاگین‌شده |
| POST | `/points/redeem` | تبدیل امتیاز به پاداش انتخابی | کاربر لاگین‌شده + nonce |
| GET | `/wallet/balance` | موجودی کیف‌پول | کاربر لاگین‌شده |
| GET | `/tiers` | لیست سطوح و مزایا | عمومی (کش‌شده) |
| GET | `/referral/my-code` | دریافت/ساخت کد معرف کاربر | کاربر لاگین‌شده |
| GET | `/referral/stats` | آمار عملکرد معرفی | کاربر لاگین‌شده |
| GET | `/admin/settings` | دریافت تنظیمات کلی | `manage_woocommerce` |
| POST | `/admin/settings` | ذخیره تنظیمات | `manage_woocommerce` + nonce |
| GET | `/admin/members` | لیست/جستجوی اعضا | `manage_woocommerce` |
| POST | `/admin/members/{id}/adjust` | ویرایش دستی امتیاز/کیف‌پول/سطح | `manage_woocommerce` + nonce |

### ۴.۳ احراز هویت و امنیت
- تمام درخواست‌های سمت مشتری از **X-WP-Nonce** استاندارد وردپرس (`wp_rest`) استفاده می‌کنن
- تمام Controller ها از طریق `Permissions\PermissionChecker` قبل از اجرای منطق، `current_user_can()` و اعتبار nonce رو چک می‌کنن
- Rate limiting سفارشی روی endpoint هایی که به سرویس پیامک وصل می‌شن (جلوگیری از سوءاستفاده)

### ۴.۴ الگوی پاسخ استاندارد
```json
{
  "success": true,
  "data": { ... },
  "message": "..."
}
```
خطاها با `WP_Error` و کد HTTP مناسب (400/403/404/500) برگردونده می‌شن.

---

## ۵. یکپارچگی با Elementor

- سه ویجت اختصاصی در `Integrations/Elementor/`:
  - **Widget_Loyalty_Status:** نمایش امتیاز/سطح فعلی کاربر لاگین‌شده در هر صفحه‌ای که ادمین بخواد
  - **Widget_Tier_Badge:** نمایش بصری بج سطح مشتری
  - **Widget_Club_CTA:** دکمه فراخوان به عمل برای هدایت به صفحه باشگاه/ثبت‌نام
- ویجت‌ها فقط زمانی ثبت می‌شن که Elementor فعال باشه (`did_action('elementor/loaded')`)
- رندر ویجت‌ها از طریق mount کردن همون کامپوننت‌های React مشتری (`src/storefront/components`) به‌صورت سبک، تا یکپارچگی بصری با بقیه بخش‌ها حفظ بشه

---

## ۶. جریان داده‌ها (Data Flow) — نمونه: خرید موفق

```
woocommerce_order_status_completed
        │
        ▼
Integrations/WooCommerce/OrderHooks.php
        │
        ▼
Services/PointsEngineService::awardForPurchase()
        │
        ├──▶ Database/Repositories/PointsRepository::insert()
        ├──▶ Services/TierService::recalculate()
        │         └──▶ در صورت ارتقا → Repositories/TierRepository + NotificationDispatcherService
        └──▶ Services/NotificationDispatcherService::dispatch('points_earned')
                  ├──▶ Integrations/SMS/*Provider
                  ├──▶ wp_mail()
                  └──▶ NotificationRepository (برای اعلان داخلی React)
```

---

## ۷. مدیریت نسخه و Assets

- شماره نسخه افزونه در هدر `karasu-buyers-club.php` و در `package.json`/`composer.json` هم‌زمان نگهداری می‌شه (مطابق قوانین AGENTS.md)
- فایل‌های بیلد React (`assets/dist/*.js`) با query string نسخه (`?ver={{plugin_version}}`) enqueue می‌شن تا مشکل کش مرورگر پیش نیاد
