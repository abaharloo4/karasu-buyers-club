# GUIDELINES.md — Karasu Buyers Club

**نسخه سند:** 1.0.0
**پیش‌نیاز مطالعه:** PRD.md، ARCHITECTURE.md

این سند **الزام‌آور** است. هر تغییر کدی که این قوانین رو رعایت نکنه، باید قبل از merge/commit اصلاح بشه.

---

## ۱. قوانین سخت‌گیرانه امنیتی

### ۱.۱ Sanitization (ورودی)
- **هیچ ورودی خام (`$_POST`, `$_GET`, `$_REQUEST`, REST params) مستقیماً استفاده نمی‌شه.**
- هر ورودی باید با تابع مناسب پاک‌سازی بشه:
  - متن ساده → `sanitize_text_field()`
  - عدد صحیح → `absint()` / `intval()`
  - عدد اعشاری (مبلغ/امتیاز) → `floatval()` + اعتبارسنجی محدوده مجاز
  - ایمیل → `sanitize_email()`
  - HTML محدود (قالب پیام) → `wp_kses_post()`
  - کد معرف/شناسه‌های الفبایی‌عددی → `sanitize_key()` یا Regex اختصاصی
- تمام Controller های REST باید در تعریف `args` خودشون، `sanitize_callback` و `validate_callback` رو صریحاً مشخص کنن — هیچ endpoint بدون این دو مجاز نیست.

### ۱.۲ Escaping (خروجی)
- **هیچ خروجی HTML بدون escape چاپ نمی‌شه:**
  - متن ساده → `esc_html()`
  - ویژگی HTML → `esc_attr()`
  - URL → `esc_url()`
  - JS inline (در صورت نیاز) → `esc_js()` یا ترجیحاً `wp_localize_script()`/`wp_add_inline_script()`
- داده‌هایی که به React پاس داده می‌شن (از طریق `wp_localize_script` یا REST) باید سمت PHP escape/sanitize شده باشن؛ escape مضاعف سمت React (مثل جلوگیری از XSS در رندر) هم الزامیه — هرگز به `dangerouslySetInnerHTML` بدون sanitize اعتماد نکن.

### ۱.۳ Nonce و احراز هویت
- هر فرم و هر AJAX/REST call که تغییر داده ایجاد می‌کنه (POST/PUT/DELETE) باید:
  - سمت PHP: nonce با `wp_create_nonce('kbc_action_name')` تولید و با `wp_verify_nonce()` یا `check_ajax_referer()` بررسی بشه
  - سمت REST: از `X-WP-Nonce` استاندارد وردپرس (`wp_rest`) استفاده بشه و در `permission_callback` هر route چک بشه
- **بدون استثنا:** هیچ endpoint نویسنده‌ای (Write) بدون بررسی nonce و `current_user_can()` منتشر نمی‌شه، حتی برای تست داخلی

### ۱.۴ Capability Checks
- عملیات ادمین همیشه در برابر `current_user_can('manage_woocommerce')` چک می‌شه (نه فقط `is_admin()`)
- عملیات مربوط به خود مشتری (مثل مصرف امتیاز) در برابر `is_user_logged_in()` و تطابق `user_id` درخواست با کاربر لاگین‌شده (جلوگیری از IDOR) چک می‌شه

### ۱.۵ کوئری‌های دیتابیس
- **همیشه Prepared Statements** — استفاده از `$wpdb->prepare()` برای هر کوئری دارای متغیر، بدون استثنا
- ممنوعیت کامل ساخت SQL با string concatenation مستقیم از ورودی کاربر

### ۱.۶ Rate Limiting
- endpoint هایی که به درگاه پیامک وصل می‌شن (مثل ارسال کد یا اطلاع‌رسانی دستی) باید محدودیت نرخ داشته باشن (مثلاً transient-based، حداکثر N درخواست در دقیقه به ازای هر کاربر/IP)

### ۱.۷ داده‌های حساس
- API Key های درگاه پیامک با `wp_options` autoload=no ذخیره بشن (نه در کد یا فایل تنظیمات عمومی)
- بدون هیچ لاگ کردن اطلاعات حساس مشتری (شماره تلفن کامل، محتوای پیامک) در DEBUG_LOG — فقط شناسه‌های ارجاعی

---

## ۲. استانداردهای کدنویسی وردپرس (WP Coding Standards)

- رعایت کامل **WordPress Coding Standards (WPCS)** از طریق PHP_CodeSniffer (`phpcs.xml` در ریشه پروژه با ruleset `WordPress`)
- Indentation: Tab (نه Space) برای فایل‌های PHP — مطابق استاندارد وردپرس
- تمام رشته‌های قابل ترجمه در `__()` / `_e()` / `esc_html__()` با text-domain ثابت `karasu-buyers-club`
- استفاده از Yoda Conditions در شرط‌های مقایسه‌ای (`if ( true === $value )`)
- Docblock کامل (PHPDoc) برای هر کلاس و متد public: توضیح، `@param`، `@return`، `@since`
- برای فرانت‌اند (React/JS): رعایت ESLint + Prettier با پیکربندی استاندارد پروژه‌های قبلی (karasu-woo-pannel)، Tailwind فقط با کلاس‌های استاندارد (بدون آربیتری غیرضروری)

---

## ۳. نام‌گذاری (Naming Conventions)

### ۳.۱ Prefix اختصاصی
**Prefix رسمی پروژه: `kbc_`** (مخفف Karasu Buyers Club) برای همه موارد procedural، و **namespace اصلی: `KarasuBuyersClub`** برای کلاس‌ها.

| نوع | قاعده | مثال |
|---|---|---|
| نام کلاس PHP | PascalCase، داخل namespace `KarasuBuyersClub\...` | `KarasuBuyersClub\Services\PointsEngineService` |
| نام تابع procedural (Hook callback و...) | snake_case با پیشوند `kbc_` | `kbc_award_points_on_order_complete()` |
| نام Hook اختصاصی (action/filter) | snake_case با پیشوند `kbc_` | `do_action( 'kbc_points_awarded', $user_id, $amount )` |
| نام جدول دیتابیس | snake_case با پیشوند `{$wpdb->prefix}kbc_` | `kbc_points_ledger` |
| نام گزینه در wp_options | snake_case با پیشوند `kbc_` | `kbc_db_version`, `kbc_settings` |
| نام Meta Key (در صورت نیاز محدود) | snake_case با پیشوند `_kbc_` (Hidden meta) | `_kbc_referral_code` |
| REST Namespace | kebab-case | `karasu-buyers-club/v1` |
| نام فایل PHP کلاس | مطابق نام کلاس، `class-` prefix در صورت عدم استفاده از Composer autoload | `class-points-engine-service.php` |
| Enqueue Handle (اسکریپت/استایل) | kebab-case با پیشوند `kbc-` | `kbc-admin-app`, `kbc-storefront-app` |
| متغیر localize شده به JS | camelCase با namespace شیء `kbcData` | `kbcData.restUrl`, `kbcData.nonce` |
| نام کامپوننت React | PascalCase | `PointsHistoryTable.jsx` |
| نام کلاس Elementor Widget | PascalCase با پیشوند `Widget_` (داخل namespace) | `Widget_Loyalty_Status` |

### ۳.۲ قوانین تکمیلی
- هیچ تابع/کلاس/متغیر سراسری بدون Prefix `kbc_` یا namespace اختصاصی نوشته نمی‌شه (جلوگیری از تداخل با سایر افزونه‌ها)
- نام فایل‌ها همیشه lowercase و با خط تیره (kebab-case)، نه underscore
- هر Migration دیتابیس دقیقاً با شماره نسخه‌ای که معرفی شده نام‌گذاری می‌شه: `Migration_{major}_{minor}_{patch}.php`

---

## ۴. قوانین Commit و مستندسازی (تکمیل‌کننده AGENTS.md)

- هر Pull Request/Commit باید فقط یک موضوع مشخص رو پوشش بده (Atomic Commits)
- پیام Commit به فرمت: `type(scope): description` (مثلاً `feat(points): add expiry cron job`)
- پیش از هر Commit: اجرای `phpcs` و `eslint` بدون خطا الزامیه
- هیچ کد جدیدی بدون به‌روزرسانی مستندات مرتبط (ARCHITECTURE.md در صورت تغییر ساختار، DEBUG_LOG.md در صورت رفع باگ) merge نمی‌شه
