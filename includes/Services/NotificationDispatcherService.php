<?php
namespace KarasuBuyersClub\Services;

use KarasuBuyersClub\Database\Repositories\NotificationRepository;
use KarasuBuyersClub\Integrations\SMS\KavenegarProvider;
use KarasuBuyersClub\Integrations\SMS\MellipayamakProvider;
use KarasuBuyersClub\Integrations\SMS\FarazSmsProvider;
use KarasuBuyersClub\Integrations\SMS\SMS_Provider_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * سرویس مرکزی مدیریت و توزیع اطلاع‌رسانی چندکاناله.
 *
 * @package KarasuBuyersClub\Services
 */
class NotificationDispatcherService {

	/**
	 * مخزن اعلان‌ها.
	 *
	 * @var NotificationRepository
	 */
	private $repository;

	/**
	 * سازنده.
	 */
	public function __construct() {
		$this->repository = new NotificationRepository();
	}

	/**
	 * ارسال اعلان به کاربر.
	 *
	 * @param int    $user_id      شناسه کاربر
	 * @param string $event_type   نوع رویداد (points_earned, tier_upgraded, birthday, etc.)
	 * @param array  $placeholders جایگزین‌های متن پیام (نام مشتری، امتیاز، ...)
	 * @return bool
	 */
	public function dispatch( int $user_id, string $event_type, array $placeholders = [] ): bool {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}

		// جایگزین‌های پیش‌فرض.
		$default_placeholders = array(
			'{customer_name}' => $user->display_name ?: $user->user_login,
			'{user_id}'       => (string) $user_id,
		);

		$all_placeholders = array_merge( $default_placeholders, $placeholders );

		// ۱. ارسال پیامک (در صورت فعال بودن).
		if ( (bool) get_option( 'kbc_sms_enabled', false ) ) {
			$phone = get_user_meta( $user_id, 'billing_phone', true );
			if ( ! empty( $phone ) && $this->check_rate_limit( $user_id ) ) {
				$template = get_option( 'kbc_tpl_sms_' . $event_type, 'مشتری گرامی {customer_name}، رویداد {event} رخ داد.' );
				$message  = strtr( $template, $all_placeholders );

				$sms_provider = $this->get_sms_provider();
				if ( $sms_provider ) {
					$sent = $sms_provider->send_sms( $phone, $message );
					$this->repository->add_log( $user_id, 'sms', $event_type, $sent ? 'sent' : 'failed' );
				}
			}
		}

		// ۲. ارسال ایمیل (در صورت فعال بودن).
		if ( (bool) get_option( 'kbc_email_enabled', true ) && ! empty( $user->user_email ) ) {
			$email_subject  = __( 'اطلاعیه باشگاه مشتریان Karasu', 'karasu-buyers-club' );
			$email_template = get_option( 'kbc_tpl_email_' . $event_type, 'سلام {customer_name} عزیز.' );
			$email_body     = strtr( $email_template, $all_placeholders );

			$sent = wp_mail( $user->user_email, $email_subject, $email_body );
			$this->repository->add_log( $user_id, 'email', $event_type, $sent ? 'sent' : 'failed' );
		}

		// ۳. ثبت اعلان داخلی (In-App).
		$this->repository->add_log( $user_id, 'in_app', $event_type, 'sent' );

		return true;
	}

	/**
	 * دریافت تامین‌کننده پیامک فعال.
	 *
	 * @return SMS_Provider_Interface|null
	 */
	private function get_sms_provider(): ?SMS_Provider_Interface {
		$active_gateway = get_option( 'kbc_sms_gateway', 'kavenegar' );
		$api_key        = get_option( 'kbc_sms_api_key', '' );
		$from_number    = get_option( 'kbc_sms_from_number', '' );

		if ( empty( $api_key ) ) {
			return null;
		}

		switch ( $active_gateway ) {
			case 'kavenegar':
				return new KavenegarProvider( $api_key );
			case 'mellipayamak':
				return new MellipayamakProvider( $api_key, $from_number );
			case 'farazsms':
				return new FarazSmsProvider( $api_key, $from_number );
			default:
				return null;
		}
	}

	/**
	 * کنترل نرخ محدودیت ارسال پیامک جهت جلوگیری از سوءاستفاده (Rate Limiting).
	 *
	 * @param int $user_id
	 * @return bool
	 */
	private function check_rate_limit( int $user_id ): bool {
		$transient_key = 'kbc_sms_rate_' . absint( $user_id );
		$current_count = (int) get_transient( $transient_key );

		if ( $current_count >= 5 ) {
			return false; // حداکثر ۵ پیامک در هر ۱۰ دقیقه.
		}

		set_transient( $transient_key, $current_count + 1, 10 * MINUTE_IN_SECONDS );
		return true;
	}
}
