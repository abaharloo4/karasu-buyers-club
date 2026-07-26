<?php
namespace KarasuBuyersClub\Integrations\SMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * اینترفیس پیاده‌سازی سرویس‌دهندگان پیامک.
 *
 * @package KarasuBuyersClub\Integrations\SMS
 */
interface SMS_Provider_Interface {

	/**
	 * ارسال پیامک به گیرنده.
	 *
	 * @param string $to      شماره گیرنده (مثلاً 09123456789)
	 * @param string $message متن پیامک
	 * @param array  $params  پارامترهای اضافی (مانند پترن یا متغیرها)
	 * @return bool
	 */
	public function send_sms( string $to, string $message, array $params = [] ): bool;
}
