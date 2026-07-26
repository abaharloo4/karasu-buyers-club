# implementation-plan.md — Karasu Buyers Club

این سند برنامه‌ریزی فاز جاری توسعه پروژه را مطابق قوانین `AGENTS.md` نگهداری می‌کند.

---

## فاز ۰ — Setup اولیه (Bootstrap)

**تاریخ شروع:** 2026-07-27  
**نسخه مقصد فاز:** 0.1.0  
**هدف:** ساخت اسکلت کامل افزونه مستقل ووکامرس، کانفیگ ابزارهای بیلد (Vite/React/Tailwind/Composer)، اعلام سازگاری HPOS، ساخت زیپ اولیه و انتشار روی GitHub.

### فایل‌های در حال ایجاد/تغییر:

#### [NEW] karasu-buyers-club.php
فایل اصلی افزونه وردپرس، شامل هدر استاندارد، بررسی فعال بودن ووکامرس، لود autoloader و راه‌اندازی `KarasuBuyersClub\Core\Plugin::instance()`.

#### [NEW] composer.json
تعریف وابستگی‌های PHP و PSR-4 Autoloading برای `KarasuBuyersClub\` از مسیر `includes/`.

#### [NEW] package.json
تعریف اسکریپت‌های بیلد Vite (`build:admin`, `build:storefront`, `build`), و وابستگی‌های React, Tailwind CSS, Lucide Icons, Vite plugins.

#### [NEW] vite.config.admin.js & vite.config.storefront.js
کانفیگ‌های مجزای Vite برای تولید اسکریپت‌های ادمین و مشتری در `assets/dist/`.

#### [NEW] tailwind.config.js & postcss.config.js
سیستم رنگ‌بندی و پالت Karasu با پشتیبانی کامل RTL.

#### [NEW] includes/Core/Plugin.php
کلاس اصلی Singleton افزونه، متدهای `init`, `hooks`, `load_textdomain`.

#### [NEW] includes/Core/Activator.php & Deactivator.php
مدیریت هوک‌های `register_activation_hook` و `register_deactivation_hook`.

#### [NEW] includes/Integrations/HPOS/CompatibilityDeclaration.php
اعلام صریح سازگاری با WooCommerce High-Performance Order Storage via `FeaturesUtil::declare_compatibility`.

#### [NEW] src/admin/main.jsx & src/storefront/main.jsx
نقطه‌های ورود اپلیکیشن‌های React.

#### [NEW] phpcs.xml & .gitignore
قوانین استانداردهای کدنویسی وردپرس و فایل‌های استثنای Git.

### نتیجه فاز:
- ساختار بدون هیچ خطای PHP یا بیلد کامپایل می‌شود.
- افزونه در پنل وردپرس شناسایی می‌شود.
- زیپ `karasu-buyers-club-0.1.0.zip` تولید و به همراه Commit/Release روی GitHub پوش خواهد شد.
