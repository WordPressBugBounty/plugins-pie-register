<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * NOTICE: THIS PLUGIN UPDATE CLASSS IS USED HANDLING UPDATES FOR ADDON PLUGINS.
 */
class Piereg_API_Manager_Example_MENU{

	//private $api_manager_example_key;
	private $piereg_api_manager_key_class;

	// Load admin menu
	public function __construct() {
		$this->piereg_api_manager_key_class = new Api_Manager_Example_Key();

		add_action( 'admin_init', array( $this, 'load_settings' ) );
	}

	// Register settings
	public function load_settings() {
		global $piereg_api_manager;

		register_setting( 'api_manager_example', 'api_manager_example', array( $this, 'validate_options' ) );
		
		// API Key
		add_settings_section( 'api_key', __( 'License Information', 'pie-register' ), array( $this, 'wc_am_api_key_text' ), 'api_manager_example_dashboard' );
		add_settings_field( 'api_key', __( 'License Key', 'pie-register' ), array( $this, 'wc_am_api_key_field' ), 'api_manager_example_dashboard', 'api_key' );
		add_settings_field( 'api_email', __( 'License email', 'pie-register' ), array( $this, 'wc_am_api_email_field' ), 'api_manager_example_dashboard', 'api_key' );

		// Activation settings
		register_setting( 'am_deactivate_example_checkbox', 'am_deactivate_example_checkbox', array( $this, 'wc_am_license_key_deactivation' ) );
		add_settings_section( 'deactivate_button', __( 'Plugin License Deactivation', 'pie-register' ), array( $this, 'wc_am_deactivate_text' ), 'api_manager_example_deactivation' );
		add_settings_field( 'deactivate_button', __( 'Deactivate Plugin License', 'pie-register' ), array( $this, 'wc_am_deactivate_textarea' ), 'api_manager_example_deactivation', 'deactivate_button' );

	}

	// Provides text for api key section
	public function wc_am_api_key_text() {
		//
	}

	// Outputs API License text field
	public function wc_am_api_key_field() {
		global $piereg_api_manager;

		$options = get_option( 'api_manager_example' );
		$api_key = $options['api_key'];
		echo "<input id='api_key' name='api_manager_example[api_key]' size='25' type='text' value='".esc_attr($api_key)."' />";
		if ( !empty( $options['api_key'] ) ) {
			echo "<span class='icon-pos'><img src='" . esc_url($piereg_api_manager->plugin_url()) . "assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . esc_url($piereg_api_manager->plugin_url()) . "assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Outputs API License email text field
	public function wc_am_api_email_field() {
		global $piereg_api_manager;

		$options = get_option( 'api_manager_example' );
		$activation_email = $options['activation_email'];
		echo "<input id='activation_email' name='api_manager_example[activation_email]' size='25' type='text' value='".esc_attr($activation_email)."' />";
		if ( !empty( $options['activation_email'] ) ) {
			echo "<span class='icon-pos'><img src='" . esc_url($piereg_api_manager->plugin_url()) . "assets/images/complete.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		} else {
			echo "<span class='icon-pos'><img src='" . esc_url($piereg_api_manager->plugin_url()) . "assets/images/warn.png' title='' style='padding-bottom: 4px; vertical-align: middle; margin-right:3px;' /></span>";
		}
	}

	// Sanitizes and validates all input and output for Dashboard
	public function validate_options( $input ) {
		
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'api_manager_example' );
		
		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );
		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );

		$activation_status = get_option( 'piereg_api_manager_activated' );
		$checkbox_status = get_option( 'am_deactivate_example_checkbox' );

		$current_api_key = $this->get_key();
		// Should match the settings_fields() value
		
			if ( $activation_status == 'Deactivated' || $activation_status == '' || $api_key == '' || $api_email == '' || $checkbox_status == 'on' || $current_api_key != $api_key  ) {
				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				if ( $current_api_key != $api_key && !empty($current_api_key) )
					$this->replace_license_key( $current_api_key );
				
				
				$args = array(
					'email' => $api_email,
					'licence_key' => $api_key,
					);
				
				$activate_results = $this->piereg_api_manager_key_class->activate( $args );
				$activate_results = json_decode($activate_results, true);
				
				if ( $activate_results['activated'] == true ) {//activate_text
					
					$this->pie_post_array['success'] = __("Plugin activated","pie-register");
					update_option( 'piereg_api_manager_activated', 'Activated' );
					update_option( 'am_deactivate_example_checkbox', 'off' );
					$old_option = get_option( 'api_manager_example' );
					$old_option['api_key'] = trim( $input['api_key'] );
					$old_option['activation_email'] = trim( $input['activation_email'] );
					update_option( 'api_manager_example', $old_option );
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
				}

				if ( $activate_results == false ) {//api_key_check_text
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-register"));
				}
				
				if ( isset( $activate_results['code'] ) ) {
					
					if( !isset($activate_results['additional info']) )
					{
						$activate_results['additional info'] = "";	
					}
					
					switch ( $activate_results['code'] ) {
						case '100'://api_email_text
							$errors->add("piereg_license_error",__($activate_results['error'].". ".$activate_results['additional info'],"pie-register"));
							$options['activation_email'] = '';
							$options['api_key'] = '';
							update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '101'://api_key_text
							$errors->add("piereg_license_error",__($activate_results['error'].". ".$activate_results['additional info'] ,"pie-register"));
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '102'://api_key_purchase_incomplete_text
							$errors->add("piereg_license_error",__($activate_results['error'].". " .$activate_results['additional info'] ,"pie-register"));
							$options['api_key'] = '';
							$options['activation_email'] = '';
							update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '103'://api_key_exceeded_text
								$errors->add("piereg_license_error",__($activate_results['error']. ". " .$activate_results['additional info'],"pie-register"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '104'://api_key_not_activated_text
								$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'],"pie-register"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '105'://api_key_invalid_text
								$errors->add("piereg_license_error",__($activate_results['error'],"pie-register"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
						case '106'://sub_not_active_text
								$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"pie-register"));
								$options['api_key'] = '';
								$options['activation_email'] = '';
								update_option( 'piereg_api_manager_activated', 'Deactivated' );
						break;
					}

				}

			} // End Plugin Activation
		return $options;
	}
	// Sanitizes and validates all input and output for Dashboard
	public function validate_addon_options( $input ) {
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		// Load existing options, validate, and update with changes from input before returning
		$options = get_option( 'api_manager_example' );
		
		$options['api_key'] = trim( $input['api_key'] );
		$options['activation_email'] = trim( $input['activation_email'] );
		/**
		  * Plugin Activation
		  */
		$api_email = trim( $input['activation_email'] );
		$api_key = trim( $input['api_key'] );
		$api_addon = array('is_addon'=>trim($input['api_addon']), 'is_addon_version'=>trim($input['api_addon_version']));
		$activation_status = get_option( 'piereg_api_manager_activated' );		
		$current_api_key = $this->get_key();
		
		
		// Should match the settings_fields() value
			if ( ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' && $current_api_key == $api_key ) || $input['is_piereg_pro_inactive'] === true   ) {
				/**
				 * If this is a new key, and an existing key already exists in the database,
				 * deactivate the existing key before activating the new key.
				 */
				// if ( $current_api_key != $api_key && !empty($current_api_key) )
				// 	$this->replace_license_key( $current_api_key );
				
				
				$args = array(
							'email' 					=> $api_email,
							'licence_key' 				=> $api_key,
							'is_piereg_pro_inactive' 	=> $input['is_piereg_pro_inactive']
						);
				
				$activate_results = $this->piereg_api_manager_key_class->activate( $args, $api_addon );
				
				$activate_results = json_decode($activate_results, true);
				
				if ( $activate_results['activated'] == true ) {//activate_text
					
					$this->pie_post_array['success'] = __("Addon activated","pie-register");
					update_option( 'piereg_api_manager_'.$api_addon['is_addon'].'_activated', 'Activated' );	
					
					if( $input['is_piereg_pro_inactive'] == true )
					{
						update_option( 'piereg_'.$api_addon['is_addon'].'_licence_key', $api_key );	
						update_option( 'piereg_'.$api_addon['is_addon'].'_licence_email', $api_email );					
					}					
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
				}
			
				if ( $activate_results == false ) {//api_key_check_text
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-register"));
				}
				
				if ( isset( $activate_results['code'] ) ) {
					$activate_results['additional info'] = isset($activate_results['additional info']) ? $activate_results['additional info'] : '';

						switch ( $activate_results['code'] ) {
							case '100':
								$errors->add("piereg_license_error",__($activate_results['error'].". ".$activate_results['additional info'],"pie-register"));
								
							break;
							case '101':
								$errors->add("piereg_license_error",__($activate_results['error'].". ".$activate_results['additional info'] ,"pie-register"));
								
							break;
							case '102':
								
								$errors->add("piereg_license_error",__($activate_results['error'].". " .$activate_results['additional info'] ,"pie-register"));
								
							break;
							case '103':
									$errors->add("piereg_license_error",__($activate_results['error']. ". " .$activate_results['additional info'],"pie-register"));
									
							break;
							case '104':
									$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'],"pie-register"));
									
							break;
							case '105':
									$errors->add("piereg_license_error",__($activate_results['error'],"pie-register"));
									
							break;
							case '106':
									$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"pie-register"));
									
							break;
							case '107':
									$errors->add("piereg_license_error",__($activate_results['error']. ". ".$activate_results['additional info'] ,"pie-register"));
									
							break;
						}
	
					}
					

			} // End Plugin Activation

		return $options;
	}
	
	public function get_key() {
		$wc_am_options = get_option('api_manager_example');
		$api_key = $wc_am_options['api_key'];

		return $api_key;
	}

	// Deactivate the current license key before activating the new license key
	public function replace_license_key( $current_api_key ) {
		global $errors;
		if(empty($errors))
			$errors = new WP_Error();
		
		$default_options = get_option( 'api_manager_example' );
		
		$api_email = $default_options['activation_email'];
		
		$args = array(
			'email' => $api_email,
			'licence_key' => $current_api_key,
			);
		
		$reset = $this->piereg_api_manager_key_class->deactivate( $args ); // reset license key activation
		
		if ( $reset == true )
			return true;

		return $errors->add("piereg_license_error",__('The license could not be deactivated. Use the License Deactivation tab to manually deactivate the license before activating a new license.',"pie-register"));
		
	}

	// Deactivates the license key to allow key to be used on another blog
	public function wc_am_license_key_deactivation( $input, $addon = false ) {
		global $piereg_api_manager,$errors;
		if(empty($errors))
			$errors = new WP_Error();

		$activation_status = get_option( 'piereg_api_manager_activated' );
		$default_options = get_option( 'api_manager_example' );

		$api_email = $default_options['activation_email'];
		$api_key = $default_options['api_key'];

		$args = array(
			'email' => $api_email,
			'licence_key' => $api_key,
			);
		
		$options = ( $input == 'on' ? 'on' : 'off' );
		$_ispro_inactive = isset($addon['is_piereg_pro_inactive']) ? sanitize_key($addon['is_piereg_pro_inactive']) : false;

		
		if ( ( $options == 'on' && $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) || $_ispro_inactive === true ) {
			
			if($addon){
				
				if( $_ispro_inactive == true )
				{
					$api_key	= get_option( 'piereg_'.sanitize_text_field($addon['is_addon']).'_licence_key' );	
					$api_email	= get_option( 'piereg_'.sanitize_text_field($addon['is_addon']).'_licence_email' );					
					
					$args = array(
									'email' 					=> $api_email,
									'licence_key' 				=> $api_key,
									'is_piereg_pro_inactive'	=> $_ispro_inactive
								);
				}
				$reset 	= $this->piereg_api_manager_key_class->deactivate( $args, $addon ); // reset addon license key activation	
				
				$reset_result = json_decode($reset, true);
				
				if ( $reset_result['deactivated'] == true ) {
					update_option( 'piereg_api_manager_'.sanitize_text_field($addon['is_addon']).'_activated', 'Deactivated' );
					
					//if( $_ispro_inactive == true ) {
						delete_option( 'piereg_'.sanitize_text_field($addon['is_addon']).'_licence_key' );	
						delete_option( 'piereg_'.sanitize_text_field($addon['is_addon']).'_licence_email' );					
					//}
					
					$errors->add("piereg_license_error",__('Addon license deactivated.',"pie-register"));
					// @header("location:".($piereg_api_manager->piereg_get_current_url()) ); - 3.6.11
					
					return $options;
				}else{
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-register"));
				}
				
			}else{
				$reset = $this->piereg_api_manager_key_class->deactivate( $args ); // reset license key activation
				$reset_result = json_decode($reset, true);
				
				if ( $reset_result['deactivated'] == true ) {
					$update = array(
						'api_key' => '',
						'activation_email' => ''
						);
					$merge_options = array_merge( $default_options, $update );
					update_option( 'api_manager_example', $merge_options );
					update_option( 'piereg_api_manager_activated', 'Deactivated' );
					
					# settings changes on license deactivation
					
						// assign registration form for free version
						if( !get_option("piereg_form_free_id") )
							(new PieReg_Base)->regFormForFreeVers();
						
						$verification = get_option(OPTION_PIE_REGISTER);
						if( $verification['verification'] == 3 )
						{
							$verification['verification'] = 1;
							update_option(OPTION_PIE_REGISTER, $verification );
							(new PieReg_Base)->set_pr_global_options(OPTION_PIE_REGISTER, $verification );					
						}

					$errors->add("piereg_license_error",__('Plugin license deactivated.',"pie-register"));
					@header("location:".($piereg_api_manager->piereg_get_current_url()) );
					
					return $options;
				}else{
					$errors->add("piereg_license_error",__('Connection failed to the License Key API server. Try again later.',"pie-register"));
				}
			}
			
		} else {

			return $options;
		}

	}

	public function wc_am_deactivate_text() {
	}

	public function wc_am_deactivate_textarea() {
		global $piereg_api_manager;
		$activation_status = get_option( 'am_deactivate_example_checkbox' );
		?>
		<input type="checkbox" id="am_deactivate_example_checkbox" name="am_deactivate_example_checkbox" value="on" <?php checked( $activation_status, 'on' ); ?> />
		<span class="description"><?php esc_html_e( 'Deactivates the plugin license so it can be used on another blog.', 'pie-register' ); ?></span>
		<?php
	}

	// Loads admin style sheets
	public function css_scripts() {
		global $piereg_api_manager;

		$curr_ver = $piereg_api_manager->version;

		wp_register_style( 'am-admin-example-css', $piereg_api_manager->plugin_url() . 'assets/css/admin-settings.css', array(), $curr_ver, 'all');
		wp_enqueue_style( 'am-admin-example-css' );
	}

	// displays sidebar
	public function wc_am_sidebar() {
		global $piereg_api_manager;
		?>
		<h3><?php esc_html_e( 'Prevent Comment Spam', 'pie-register' ); ?></h3>
		<ul class="celist">
			<li><a href="http://www.toddlahman.com/shop/simple-comments/" target="_blank"><?php esc_html_e( 'Simple Comments', 'pie-register' ); ?></a></li>
		</ul>
		<?php
	}

}
//$api_manager_example_menu = new Piereg_API_Manager_Example_MENU();