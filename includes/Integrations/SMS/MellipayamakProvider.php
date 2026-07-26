<?php
namespace KarasuBuyersClub\Integrations\SMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * پیاده‌سازی سرویس پیامک ملی‌پیامک.
 *
 * @package KarasuBuyersClub\Integrations\SMS
 */
class MellipayamakProvider implements SMS_Provider_Interface {

	/**
	 * نام کاربری و کلمه عبور یا کلید API.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * شماره فرستنده.
	 *
	 * @var string
	 */
	private $from_number;

	/**
	 * سازنده.
	 *
	 * @param string $api_key
	 * @param string $from_number
	 */
	public function __construct( string $api_key, string $from_number = '' ) {
		$this->api_key     = $api_key;
		$this->from_number = $from_number;
	}

	/**
	 * ارسال پیامک از طریق REST API ملی‌پیامک.
	 *
	 * @param string $to
	 * @param string $message
	 * @param array  $params
	 * @return bool
	 */
	public function send_sms( string $to, string $message, array $params = [] ): bool {
		if ( empty( $this->api_key ) || empty( $to ) ) {
			return false;
		}

		$url = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';

		$body = array(
			'username' => sanitize_text_field( $this->api_key ),
			'password' => '',
			'to'       => sanitize_text_field( $to ),
			'from'     => sanitize_text_field( $this->from_number ),
			'text'     => sanitize_text_field( $message ),
			'isFlash'  => false,
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $body ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = wp_remote_retrieve_response_code( $response );
		return 200 === $code;
	}
}
