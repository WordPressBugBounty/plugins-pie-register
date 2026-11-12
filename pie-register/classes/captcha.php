<?php
/*
	* Captcha helpers: reCAPTCHA v2 / v2 Invisible / v3 and Cloudflare Turnstile
	* Registration, login and forgot password all verify through piereg_verify_captcha(), so every type is checked the same way.
	* Since v4.0.0.0
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'piereg_captcha_type' ) ){

	/*
		* The selected type: v2 (checkbox), v2_invisible, v3 or turnstile
	*/
	function piereg_captcha_type( $settings = null ){
		if( ! is_array( $settings ) ){
			$settings = get_option( OPTION_PIE_REGISTER );
		}
		$type = isset( $settings['piereg_recaptcha_type'] ) ? $settings['piereg_recaptcha_type'] : 'v2';
		return in_array( $type, array( 'v2', 'v2_invisible', 'v3', 'turnstile' ), true ) ? $type : 'v2';
	}

	/*
		* Is captcha switched on? The free version has no on/off setting for it (only the premium version does),
		* so it counts as on unless the setting exists and is explicitly off. The selected type and its keys decide the rest.
	*/
	function piereg_captcha_enabled( $settings = null ){
		if( ! is_array( $settings ) ){
			$settings = get_option( OPTION_PIE_REGISTER );
		}
		return ! isset( $settings['piereg_enable_recaptcha'] ) || 1 === (int) $settings['piereg_enable_recaptcha'];
	}

	/*
		* Markup for the two newer types. Returns '' for v2 / v3, which keep their existing markup.
	*/
	function piereg_captcha_extra_markup(){
		$settings = get_option( OPTION_PIE_REGISTER );
		$type     = piereg_captcha_type( $settings );
		if( $type === 'turnstile' ){
			$key = isset( $settings['turnstile_site'] ) ? trim( $settings['turnstile_site'] ) : '';
			if( $key === '' ){
				return '<div class="input_fields pie_captcha_err">' . esc_html__( 'Cloudflare Turnstile is not configured correctly.', 'pie-register' ) . '</div>';
			}
			// Turnstile renders every .cf-turnstile element itself and adds the hidden cf-turnstile-response input to the form
			return '<div class="cf-turnstile piereg-turnstile" data-sitekey="' . esc_attr( $key ) . '" data-theme="light"></div>';
		}
		if( $type === 'v2_invisible' ){
			$key = isset( $settings['captcha_publc'] ) ? trim( $settings['captcha_publc'] ) : '';
			if( $key === '' ){
				return '<div class="input_fields pie_captcha_err">' . esc_html__( 'Captcha is not configured correctly', 'pie-register' ) . '.</div>';
			}
			// Rendered by assets/js/piereg_captcha.js; runs when the form is submitted
			return '<div class="piereg-recaptcha-invisible" data-sitekey="' . esc_attr( $key ) . '"></div>';
		}
		return '';
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

		if( $type === 'turnstile' ){
			$secret	= isset( $settings['turnstile_secret'] ) ? trim( $settings['turnstile_secret'] ) : '';
			$url	= 'https://challenges.cloudflare.com/turnstile/v0/siteverify'; // phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent -- server-side Turnstile verification API (service disclosed in readme), not offloaded content
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- the calling form checks its own nonce
			$token	= isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
		}else{
			$secret	= ( $type === 'v3' ) ? ( isset( $settings['captcha_private_v3'] ) ? trim( $settings['captcha_private_v3'] ) : '' ) : ( isset( $settings['captcha_private'] ) ? trim( $settings['captcha_private'] ) : '' );
			$url	= 'https://www.google.com/recaptcha/api/siteverify';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- the calling form checks its own nonce
			$token	= isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
		}
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

	/*
		* Scripts for the two newer types (called where the plugin loads its captcha scripts)
	*/
	function piereg_enqueue_captcha_extra(){
		$type = piereg_captcha_type();
		$ver  = defined( 'PIEREGISTER_VERSION' ) ? PIEREGISTER_VERSION : '4.0.0.0';
		if( $type === 'turnstile' ){
			wp_enqueue_script( 'pie_turnstile_script', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion,PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent -- Cloudflare Turnstile must be loaded from Cloudflare, unversioned (service disclosed in readme)
		}elseif( $type === 'v2_invisible' ){
			$settings = get_option( OPTION_PIE_REGISTER );
			$lang     = isset( $settings['piereg_recaptcha_language'] ) ? $settings['piereg_recaptcha_language'] : 'en';
			wp_enqueue_script( 'pie_recaptcha_invisible', PIEREG_PLUGIN_URL . 'assets/js/piereg_captcha.js', array( 'jquery' ), $ver, true );
			wp_enqueue_script( 'pie_recaptcha_invisible_api', 'https://www.google.com/recaptcha/api.js?onload=pieregInvisibleInit&render=explicit&hl=' . rawurlencode( $lang ), array( 'pie_recaptcha_invisible' ), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google requires the unversioned URL
		}
	}
}
