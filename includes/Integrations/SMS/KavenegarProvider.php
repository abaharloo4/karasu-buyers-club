<?php
namespace KarasuBuyersClub\Integrations\SMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * پیاده‌سازی سرویس پیامک کاوه‌نگار.
 *
 * @package KarasuBuyersClub\Integrations\SMS
 */
class KavenegarProvider implements SMS_Provider_Interface {

	/**
	 * کلید API.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * سازنده.
	 *
	 * @param string $api_key
	 */
	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * ارسال پیامک از طریق کاوه‌نگار.
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

		$url = sprintf( 'https://api.kavenegar.com/v1/%s/sms/send.json', $this->api_key );

		$body = array(
			'receptor' => sanitize_text_field( $to ),
			'message'  => sanitize_text_field( $message ),
		);

		$response = wp_remote_post(
			$url,
			array(
				'body'    => $body,
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
