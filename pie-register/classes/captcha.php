<?php
/*
	* Captcha helpers: reCAPTCHA v2 / v3
	* Registration, login and forgot password all verify through piereg_verify_captcha(), so every type is checked the same way.
	* Since v3.8.4.16
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'piereg_captcha_type' ) ){

	/*
		* The selected type: v2 (checkbox) or v3. Anything else (e.g. a removed type) falls back to v2.
	*/
	function piereg_captcha_type( $settings = null ){
		if( ! is_array( $settings ) ){
			$settings = get_option( OPTION_PIE_REGISTER );
		}
		$type = isset( $settings['piereg_recaptcha_type'] ) ? $settings['piereg_recaptcha_type'] : 'v2';
		return in_array( $type, array( 'v2', 'v3' ), true ) ? $type : 'v2';
	}

	/*
		* Verify the submitted captcha answer.
		* Returns true (passed), false (failed) or null (no secret key saved for the selected type).
	*/
	function piereg_verify_captcha( $settings = null ){
		if( ! is_array( $settings ) ){
			$settings = get_option( OPTION_PIE_REGISTER );
		}
		$type	= piereg_captcha_type( $settings );
		$ip		= isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		$secret	= ( $type === 'v3' ) ? ( isset( $settings['captcha_private_v3'] ) ? trim( $settings['captcha_private_v3'] ) : '' ) : ( isset( $settings['captcha_private'] ) ? trim( $settings['captcha_private'] ) : '' );
		$url	= 'https://www.google.com/recaptcha/api/siteverify';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- the calling form checks its own nonce
		$token	= isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
		if( $secret === '' ){
			return null;
		}
		if( $token === '' ){
			return false;
		}
		$response = wp_remote_post( $url, array(
			'timeout'	=> 10,
			'body'		=> array( 'secret' => $secret, 'response' => $token, 'remoteip' => $ip )
		) );
		if( is_wp_error( $response ) ){
			return false;
		}
		$body	= json_decode( wp_remote_retrieve_body( $response ), true );
		$passed	= ! empty( $body['success'] );
		// v3 never shows a challenge: a valid token only says the request was scored, so a low (bot-like) score must fail too
		if( $passed && $type === 'v3' && isset( $body['score'] ) ){
			$passed = ( (float) $body['score'] >= (float) apply_filters( 'piereg_recaptcha_v3_threshold', 0.5 ) );
		}
		return (bool) apply_filters( 'piereg_captcha_verified', $passed, $type, $body );
	}
}
