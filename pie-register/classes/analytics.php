<?php
/*
	* Registration analytics and conversion funnel
	* Views > Started > Submitted > Registered > Activated, per form and per field.
	* No IP address, user agent, user ID or field value is ever stored.
	* Since v4.0.0.0
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'PieReg_Analytics' ) ){

	class PieReg_Analytics
	{
		var $db_version		= '1.1.0';
		var $events_table	= '';
		var $daily_table	= '';
		var $utm_table		= '';
		var $page_suffix	= '';
		var $form_map_cache	= array();
		var $script_loaded	= false;

		/* Events a visitor's browser may report */
		var $client_events	= array( 'view', 'start', 'field', 'error', 'page', 'submit' );
		/* Events only the server may record */
		var $server_events	= array( 'registered', 'activated', 'draft_save', 'draft_resume' );

		function __construct(){
			global $wpdb;
			$this->events_table	= $wpdb->prefix . 'pieregister_analytics_events';
			$this->daily_table	= $wpdb->prefix . 'pieregister_analytics_daily';
			$this->utm_table	= $wpdb->prefix . 'pieregister_analytics_utm';

			add_action( 'init', array( $this, 'maybe_install' ) );

			// Front end
			add_filter( 'piereg_registration_form_extra_inputs', array( $this, 'registration_form_inputs' ), 10, 2 );
			add_action( 'wp_ajax_piereg_track_events', array( $this, 'ajax_track' ) );
			add_action( 'wp_ajax_nopriv_piereg_track_events', array( $this, 'ajax_track' ) );
			add_action( 'template_redirect', array( $this, 'remember_landing_utm' ) );

			// Server side events
			add_action( 'pie_register_after_user_created', array( $this, 'track_registered' ) );
			add_action( 'added_user_meta', array( $this, 'track_activated' ), 10, 4 );
			add_action( 'updated_user_meta', array( $this, 'track_activated' ), 10, 4 );

			// Scheduled rollup and cleanup
			add_filter( 'piereg_cron_tasks', array( $this, 'register_cron_task' ) );

			// Admin
			add_action( 'pie_register_add_menu', array( $this, 'add_menu' ) );
			add_action( 'wp_ajax_piereg_analytics_data', array( $this, 'ajax_data' ) );
			add_action( 'wp_ajax_piereg_analytics_save', array( $this, 'ajax_save_settings' ) );
			add_action( 'wp_ajax_piereg_analytics_purge', array( $this, 'ajax_purge' ) );
			add_action( 'admin_post_piereg_analytics_export', array( $this, 'export_csv' ) );
			add_action( 'admin_init', array( $this, 'privacy_policy_text' ) );

			add_action( 'pie_uninstall_base', array( $this, 'uninstall' ) );
		}

		/*
			* Settings and gates
		*/
		function get_options(){
			$defaults = array(
				'enable'			=> 1,
				'exclude_logged_in'	=> 1,
				'respect_dnt'		=> 1,
				'require_consent'	=> 0,
				'raw_retention'		=> 30,
				'daily_retention'	=> 365
			);
			$saved = get_option( 'piereg_analytics_options', array() );
			return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
		}

		/*
			* Analytics is available in the free version, no license check needed
		*/
		function is_pro(){
			return true;
		}

		function tracking_active( $form_id = 0 ){
			if( ! $this->is_pro() ){
				return false;
			}
			$opt = $this->get_options();
			if( empty( $opt['enable'] ) ){
				return false;
			}
			if( ! empty( $opt['exclude_logged_in'] ) && is_user_logged_in() ){
				return false;
			}
			return (bool) apply_filters( 'piereg_analytics_tracking_enabled', true, (int) $form_id );
		}

		function plugin_url( $path ){
			return PIEREG_PLUGIN_URL . $path;
		}

		function asset_version(){
			$v = defined( 'PIEREGISTER_VERSION' ) ? PIEREGISTER_VERSION : '4.0.0.0';
			// Add the newest analytics asset's change time so browsers never keep a stale tracker
			$m = 0;
			foreach( array( 'assets/js/piereg_analytics.js', 'assets/js/piereg_analytics_admin.js', 'assets/css/piereg_analytics.css' ) as $f ){
				$t = @filemtime( dirname( __DIR__ ) . '/' . $f );
				$m = max( $m, (int) $t );
			}
			return $m ? $v . '.' . $m : $v;
		}

		/*
			* Create or update the two tables (runs only when the stored version differs)
		*/
		function maybe_install(){
			if( get_option( 'piereg_analytics_db_version' ) === $this->db_version ){
				return;
			}
			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$charset = $wpdb->get_charset_collate();

			dbDelta( "CREATE TABLE {$this->events_table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  source varchar(20) NOT NULL DEFAULT 'registration',
  form_id int(10) unsigned NOT NULL DEFAULT 0,
  session_key char(32) NOT NULL DEFAULT '',
  event varchar(20) NOT NULL DEFAULT '',
  field_id varchar(60) NOT NULL DEFAULT '',
  page_no tinyint(3) unsigned NOT NULL DEFAULT 0,
  utm_source varchar(60) NOT NULL DEFAULT '',
  utm_medium varchar(60) NOT NULL DEFAULT '',
  utm_campaign varchar(80) NOT NULL DEFAULT '',
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY form_time (form_id,created_at),
  KEY session_key (session_key)
) $charset;" );

			dbDelta( "CREATE TABLE {$this->daily_table} (
  day date NOT NULL,
  source varchar(20) NOT NULL DEFAULT 'registration',
  form_id int(10) unsigned NOT NULL DEFAULT 0,
  event varchar(20) NOT NULL DEFAULT '',
  field_id varchar(60) NOT NULL DEFAULT '',
  total int(10) unsigned NOT NULL DEFAULT 0,
  sessions int(10) unsigned NOT NULL DEFAULT 0,
  abandoned int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (day,source,form_id,event,field_id)
) $charset;" );

			/* Traffic sources (UTM): unique visits and registrations per day, form and source */
			dbDelta( "CREATE TABLE {$this->utm_table} (
  day date NOT NULL,
  form_id int(10) unsigned NOT NULL DEFAULT 0,
  utm_source varchar(60) NOT NULL DEFAULT '',
  utm_medium varchar(60) NOT NULL DEFAULT '',
  utm_campaign varchar(80) NOT NULL DEFAULT '',
  views int(10) unsigned NOT NULL DEFAULT 0,
  registered int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (day,form_id,utm_source,utm_medium,utm_campaign)
) $charset;" );

			update_option( 'piereg_analytics_db_version', $this->db_version );
		}

		/*
			* Map of input name => label for one form, used to accept only real field names
		*/
		function get_field_map( $form_id ){
			$form_id = (int) $form_id;
			if( isset( $this->form_map_cache[ $form_id ] ) ){
				return $this->form_map_cache[ $form_id ];
			}
			$map		= array();
			$skip_types	= array( 'captcha', 'hcaptcha', 'math_captcha', 'honeypot', 'pricing', 'submit', 'form', 'pagebreak', 'sectionbreak', 'html', 'hidden', 'custom_role' );
			$fields		= maybe_unserialize( get_option( 'piereg_form_fields_' . $form_id ) );

			if( is_array( $fields ) ){
				foreach( $fields as $field ){
					if( ! is_array( $field ) || empty( $field['type'] ) || ! isset( $field['id'] ) || in_array( $field['type'], $skip_types, true ) ){
						continue;
					}
					$label = ( isset( $field['label'] ) && $field['label'] !== '' ) ? $field['label'] : $field['type'];
					switch( $field['type'] ){
						case 'username':
							$map['username'] = $label;
							break;
						case 'password':
							$map['password'] = $label;
							break;
						case 'email':
							$map['e_mail'] = $label;
							break;
						case 'name':
							$map['first_name'] = $label;
							$map['last_name']  = $label;
							break;
						default:
							$map[ str_replace( '-', '_', sanitize_title( $field['type'] . '_' . $field['id'] ) ) ] = $label;
					}
				}
			}
			$this->form_map_cache[ $form_id ] = $map;
			return $map;
		}

		/*
			* Turn a raw input name (for example "address_9_city" or "checkbox_4[]") into a known field key, or ''
		*/
		function normalise_field( $raw, $form_id ){
			$name = strtolower( preg_replace( '/\[.*$/', '', is_scalar( $raw ) ? (string) $raw : '' ) );
			$name = str_replace( '-', '_', $name );
			if( ! preg_match( '/^[a-z0-9_]{1,60}$/', $name ) ){
				return '';
			}
			$map = $this->get_field_map( $form_id );
			if( isset( $map[ $name ] ) ){
				return $name;
			}
			foreach( $map as $slug => $label ){
				if( strpos( $name, $slug . '_' ) === 0 ){
					return $slug;
				}
			}
			return '';
		}

		/*
			* Registration form: add the session input and load the tracker script
		*/
		function registration_form_inputs( $html, $form_id ){
			$form_id = (int) $form_id;
			if( $form_id < 1 || ! $this->tracking_active( $form_id ) ){
				return $html;
			}

			if( ! $this->script_loaded ){
				$this->script_loaded = true;
				$opt = $this->get_options();
				wp_enqueue_script( 'piereg_analytics', $this->plugin_url( 'assets/js/piereg_analytics.js' ), array( 'jquery' ), $this->asset_version(), true );
				wp_localize_script( 'piereg_analytics', 'pieAnalytics', array(
					'ajaxURL'		=> admin_url( 'admin-ajax.php' ),
					'nonce'			=> wp_create_nonce( 'piereg_track_events' ),
					'requireConsent'=> ! empty( $opt['require_consent'] ) ? 1 : 0,
					'respectDnt'	=> ! empty( $opt['respect_dnt'] ) ? 1 : 0
				) );
			}
			return $html . '<input type="hidden" name="piereg_ask" value="" />';
		}

		/*
			* Insert rows. Each row: source, form_id, session_key, event, field_id, page_no
		*/
		function insert_events( $rows ){
			global $wpdb;
			if( empty( $rows ) ){
				return false;
			}
			$now			= gmdate( 'Y-m-d H:i:s' );
			$placeholders	= array();
			$values			= array();
			foreach( $rows as $r ){
				$placeholders[] = '(%s,%d,%s,%s,%s,%d,%s,%s,%s,%s)';
				array_push(
					$values,
					$r['source'], $r['form_id'], $r['session_key'], $r['event'], $r['field_id'], $r['page_no'],
					isset( $r['utm_source'] ) ? $r['utm_source'] : '', isset( $r['utm_medium'] ) ? $r['utm_medium'] : '', isset( $r['utm_campaign'] ) ? $r['utm_campaign'] : '',
					$now
				);
			}
			$placeholders = implode( ',', $placeholders );
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare,WordPress.DB.DirectDatabaseQuery.DirectQuery -- table name is built from $wpdb->prefix, the placeholder list is fixed text
			return $wpdb->query( $wpdb->prepare( "INSERT INTO {$this->events_table} (source,form_id,session_key,event,field_id,page_no,utm_source,utm_medium,utm_campaign,created_at) VALUES {$placeholders}", $values ) );
		}

		/*
			* One UTM value: lower case, plain characters only, short (it is a campaign tag, never personal data)
		*/
		function clean_utm( $value, $len ){
			$value = strtolower( sanitize_text_field( is_scalar( $value ) ? (string) $value : '' ) );
			$value = preg_replace( '/[^a-z0-9 _.\-+\/]/', '', $value );
			return substr( trim( $value ), 0, $len );
		}

		function read_utm( $raw ){
			$u = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
			if( ! is_array( $u ) ){
				return array( 'utm_source' => '', 'utm_medium' => '', 'utm_campaign' => '' );
			}
			return array(
				'utm_source'	=> $this->clean_utm( isset( $u['source'] ) ? $u['source'] : '', 60 ),
				'utm_medium'	=> $this->clean_utm( isset( $u['medium'] ) ? $u['medium'] : '', 60 ),
				'utm_campaign'	=> $this->clean_utm( isset( $u['campaign'] ) ? $u['campaign'] : '', 80 )
			);
		}

		/*
			* Public API used by this plugin and by addons.
			* $source is 'registration' today; an invitation view can register its own.
		*/
		function record_event( $source, $form_id, $event, $field_id = '', $session_key = '', $page_no = 0, $utm = array() ){
			$sources = array_keys( apply_filters( 'piereg_analytics_sources', array( 'registration' => '' ) ) );
			$source  = sanitize_key( $source );
			$event   = sanitize_key( $event );
			if( ! in_array( $source, $sources, true ) || ! in_array( $event, array_merge( $this->client_events, $this->server_events ), true ) ){
				return false;
			}
			if( $session_key !== '' && ! preg_match( '/^[a-f0-9]{32}$/', $session_key ) ){
				$session_key = '';
			}
			$inserted = $this->insert_events( array( array(
				'source'		=> $source,
				'form_id'		=> absint( $form_id ),
				'session_key'	=> $session_key,
				'event'			=> $event,
				'field_id'		=> sanitize_key( $field_id ),
				'page_no'		=> min( 255, absint( $page_no ) ),
				'utm_source'	=> isset( $utm['utm_source'] ) ? $this->clean_utm( $utm['utm_source'], 60 ) : '',
				'utm_medium'	=> isset( $utm['utm_medium'] ) ? $this->clean_utm( $utm['utm_medium'], 60 ) : '',
				'utm_campaign'	=> isset( $utm['utm_campaign'] ) ? $this->clean_utm( $utm['utm_campaign'], 80 ) : ''
			) ) );

			/* registered/activated are rare, server-only events: roll today up right away
			 * so the dashboard reflects them without waiting on WP-Cron or an admin page load. */
			if( $inserted && in_array( $event, $this->server_events, true ) ){
				$this->rollup_days( array( gmdate( 'Y-m-d' ) ) );
			}
			return $inserted;
		}

		/*
			* Does the request look like a bot?
		*/
		function is_bot(){
			$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			if( $ua === '' ){
				return true;
			}
			$pattern = apply_filters( 'piereg_analytics_bot_ua', '/bot|crawl|spider|slurp|headless|phantom|curl|wget|python|scrapy|lighthouse|preview|monitor|facebookexternalhit/i' );
			return (bool) preg_match( $pattern, $ua );
		}

		/*
			* Simple per-visitor rate limit. The visitor is only ever a one-way hash in a transient.
		*/
		function rate_limited(){
			$ip		= isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			$key	= 'piereg_an_rl_' . substr( wp_hash( $ip ), 0, 16 );
			$count	= (int) get_transient( $key );
			if( $count >= 30 ){
				return true;
			}
			set_transient( $key, $count + 1, MINUTE_IN_SECONDS );
			return false;
		}

		/*
			* Visitors often land on a tagged link (?utm_source=...) and reach the form a page or two later,
			* where the URL no longer carries the tags. Keep the cleaned tags in a browser-session cookie so
			* the view (and through it the registration) is attributed to the campaign. Only campaign tags
			* are stored, never personal data, and the same opt-outs as the tracker apply.
		*/
		function remember_landing_utm(){
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only campaign tags from a public link
			if( empty( $_GET['utm_source'] ) && empty( $_GET['utm_medium'] ) && empty( $_GET['utm_campaign'] ) ){
				return;
			}
			$utm = $this->read_utm( array(
				'source'	=> isset( $_GET['utm_source'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_source'] ) ) : '',
				'medium'	=> isset( $_GET['utm_medium'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_medium'] ) ) : '',
				'campaign'	=> isset( $_GET['utm_campaign'] ) ? sanitize_text_field( wp_unslash( $_GET['utm_campaign'] ) ) : ''
			) );
			// phpcs:enable
			if( implode( '', $utm ) === '' || ! $this->tracking_active() ){
				return;
			}
			$opt = $this->get_options();
			// Consent can only be known in the browser, so nothing is stored server side when it is required
			if( ! empty( $opt['require_consent'] ) ){
				return;
			}
			if( $this->opted_out_by_header() ){
				return;
			}
			$value = wp_json_encode( array( 'source' => $utm['utm_source'], 'medium' => $utm['utm_medium'], 'campaign' => $utm['utm_campaign'] ) );
			$_COOKIE['piereg_utm'] = $value;
			if( headers_sent() ){
				return;
			}
			setcookie( 'piereg_utm', $value, array(
				'expires'	=> 0,
				'path'		=> COOKIEPATH ? COOKIEPATH : '/',
				'domain'	=> COOKIE_DOMAIN ? COOKIE_DOMAIN : '',
				'secure'	=> is_ssl(),
				'httponly'	=> true,
				'samesite'	=> 'Lax'
			) );
		}

		/*
			* Do-Not-Track / Global Privacy Control sent as request headers (the browser flags are checked in the tracker)
		*/
		function opted_out_by_header(){
			if( empty( $this->get_options()['respect_dnt'] ) ){
				return false;
			}
			return ( isset( $_SERVER['HTTP_DNT'] ) && '1' === $_SERVER['HTTP_DNT'] ) || ( isset( $_SERVER['HTTP_SEC_GPC'] ) && '1' === $_SERVER['HTTP_SEC_GPC'] );
		}

		/*
			* Campaign tags for a tracked visit: the ones the form page itself was opened with win,
			* otherwise the ones remembered from the landing page
		*/
		function visit_utm( $posted ){
			$utm = $this->read_utm( $posted );
			if( implode( '', $utm ) === '' && isset( $_COOKIE['piereg_utm'] ) ){
				$utm = $this->read_utm( sanitize_text_field( wp_unslash( $_COOKIE['piereg_utm'] ) ) );
			}
			return $utm;
		}

		/*
			* AJAX: events from the visitor's browser
		*/
		function ajax_track(){
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
			if( ! wp_verify_nonce( $nonce, 'piereg_track_events' ) ){
				wp_send_json_error( null, 403 );
			}

			$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
			$session = isset( $_POST['session_key'] ) ? sanitize_text_field( wp_unslash( $_POST['session_key'] ) ) : '';

			// Quietly accept and drop anything we do not want to count
			if( $form_id < 1 || ! preg_match( '/^[a-f0-9]{32}$/', $session ) || ! $this->tracking_active( $form_id ) || $this->opted_out_by_header() || $this->is_bot() || $this->rate_limited() ){
				wp_send_json_success();
			}
			if( ! get_option( 'piereg_form_fields_' . $form_id ) ){
				wp_send_json_success();
			}

			$raw	= isset( $_POST['events'] ) ? sanitize_text_field( wp_unslash( $_POST['events'] ) ) : '';
			$events	= json_decode( $raw, true );
			if( ! is_array( $events ) ){
				wp_send_json_success();
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- every value is cleaned in read_utm()
			$utm  = $this->visit_utm( isset( $_POST['utm'] ) ? wp_unslash( $_POST['utm'] ) : '' );
			$rows = array();
			foreach( array_slice( $events, 0, 50 ) as $e ){
				if( ! is_array( $e ) || empty( $e['e'] ) || ! in_array( $e['e'], $this->client_events, true ) ){
					continue;
				}
				$field = '';
				if( in_array( $e['e'], array( 'field', 'error' ), true ) ){
					$field = $this->normalise_field( isset( $e['f'] ) ? $e['f'] : '', $form_id );
					if( $field === '' ){
						continue;
					}
				}
				$rows[] = array(
					'source'		=> 'registration',
					'form_id'		=> $form_id,
					'session_key'	=> $session,
					'event'			=> $e['e'],
					'field_id'		=> $field,
					'page_no'		=> isset( $e['p'] ) ? min( 255, absint( $e['p'] ) ) : 0
				) + ( $e['e'] === 'view' ? $utm : array() );
			}
			$this->insert_events( $rows );
			wp_send_json_success();
		}

		/*
			* Server side: a user was created by the registration form
		*/
		function track_registered( $user ){
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- the registration nonce is verified earlier in pie_save_registration()
			$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
			$session = isset( $_POST['piereg_ask'] ) ? sanitize_text_field( wp_unslash( $_POST['piereg_ask'] ) ) : '';
			// phpcs:enable
			if( $form_id < 1 || ! $this->is_pro() || ! $this->get_options()['enable'] ){
				return;
			}
			$utm = array();
			if( preg_match( '/^[a-f0-9]{32}$/', $session ) ){
				global $wpdb;
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT utm_source, utm_medium, utm_campaign FROM {$this->events_table} WHERE session_key = %s AND event = 'view' ORDER BY id DESC LIMIT 1", $session ), ARRAY_A );
				if( $row ){ $utm = $row; }
			}
			$this->record_event( 'registration', $form_id, 'registered', '', $session, 0, $utm );
			// Keep the visit's session so a later activation counts in the same (tracked) funnel
			if( is_object( $user ) && ! empty( $user->ID ) && preg_match( '/^[a-f0-9]{32}$/', $session ) ){
				update_user_meta( (int) $user->ID, 'piereg_analytics_session', $session );
			}
		}

		/*
			* Server side: an account became active (any route: no verification, e-mail link or admin approval)
		*/
		function track_activated( $meta_id, $user_id, $meta_key, $value ){
			if( $meta_key !== 'active' || (string) $value !== '1' ){
				return;
			}
			if( get_user_meta( $user_id, 'piereg_analytics_activated', true ) ){
				return;
			}
			$form_id = (int) get_user_meta( $user_id, 'user_registered_form_id', true );
			if( $form_id < 1 || ! $this->is_pro() || ! $this->get_options()['enable'] ){
				return;
			}
			update_user_meta( $user_id, 'piereg_analytics_activated', 1 );
			$this->record_event( 'registration', $form_id, 'activated', '', (string) get_user_meta( $user_id, 'piereg_analytics_session', true ) );
		}

		/*
			* Cron: roll raw events up into daily totals and clean old data
		*/
		function register_cron_task( $tasks ){
			$tasks['analytics'] = array( $this, 'run_cron' );
			return $tasks;
		}

		function run_cron(){
			global $wpdb;
			if( get_option( 'piereg_analytics_db_version' ) !== $this->db_version ){
				return;
			}
			$this->rollup_days( array( gmdate( 'Y-m-d' ), gmdate( 'Y-m-d', time() - DAY_IN_SECONDS ) ) );
			$this->maybe_rebuild_sources();

			// Cleanup once a day
			if( get_transient( 'piereg_analytics_purged' ) ){
				return;
			}
			set_transient( 'piereg_analytics_purged', 1, 23 * HOUR_IN_SECONDS );
			$opt		= $this->get_options();
			$raw_cut	= gmdate( 'Y-m-d H:i:s', time() - max( 7, (int) $opt['raw_retention'] ) * DAY_IN_SECONDS );
			$daily_cut	= gmdate( 'Y-m-d', time() - max( 30, (int) $opt['daily_retention'] ) * DAY_IN_SECONDS );
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names come from $wpdb->prefix
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$this->events_table} WHERE created_at < %s", $raw_cut ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$this->daily_table} WHERE day < %s", $daily_cut ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$this->utm_table} WHERE day < %s", $daily_cut ) );
			// phpcs:enable
		}

		/*
			* Traffic-source rows now count only registrations from tracked visits. Re-roll the days whose raw
			* events are still kept so rows written by the old rule match; older rows are left as they were.
		*/
		function maybe_rebuild_sources(){
			if( get_option( 'piereg_analytics_sources_rule' ) === '3' ){
				return;
			}
			update_option( 'piereg_analytics_sources_rule', '3', false );
			$keep = min( 90, max( 7, (int) $this->get_options()['raw_retention'] ) );
			$days = array();
			for( $i = 0; $i <= $keep; $i++ ){
				$days[] = gmdate( 'Y-m-d', time() - $i * DAY_IN_SECONDS );
			}
			$this->rollup_days( $days );
		}

		function rollup_days( $days ){
			global $wpdb;
			foreach( $days as $day ){
				if( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $day ) ){
					continue;
				}
				$start = $day . ' 00:00:00';
				$end   = $day . ' 23:59:59';

				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names come from $wpdb->prefix
				// "sessions" of a registration / activation counts only visits whose view was recorded: a visit key
				// alone (tracker blocked, no consent, beacon lost) does not make an account part of the funnel
				$wpdb->query( $wpdb->prepare(
					"INSERT INTO {$this->daily_table} (day,source,form_id,event,field_id,total,sessions,abandoned)
					SELECT %s, e.source, e.form_id, e.event, e.field_id, COUNT(*),
						COUNT(DISTINCT CASE WHEN e.event IN ('registered','activated') AND NOT EXISTS (
							SELECT 1 FROM {$this->events_table} v WHERE v.session_key = e.session_key AND v.event = 'view'
						) THEN NULL ELSE NULLIF(e.session_key,'') END), 0
					FROM {$this->events_table} e WHERE e.created_at BETWEEN %s AND %s
					GROUP BY e.source, e.form_id, e.event, e.field_id
					ON DUPLICATE KEY UPDATE total = VALUES(total), sessions = VALUES(sessions)",
					$day, $start, $end
				) );
				$wpdb->query( $wpdb->prepare( "UPDATE {$this->daily_table} SET abandoned = 0 WHERE day = %s", $day ) );
				// phpcs:enable

				$this->rollup_abandoned( $day, $start, $end );

				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names come from $wpdb->prefix
				// Source rows: same basis as the totals (distinct tracked visits). A registration takes the campaign of
				// its visit's first view, so it lands in the same row even when it was recorded before the view arrived.
				// The day is rebuilt from scratch so rows written by an older rule do not linger.
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$this->utm_table} WHERE day = %s", $day ) );
				$wpdb->query( $wpdb->prepare(
					"INSERT INTO {$this->utm_table} (day,form_id,utm_source,utm_medium,utm_campaign,views,registered)
					SELECT %s, x.form_id, x.s, x.m, x.c,
						COUNT(DISTINCT CASE WHEN x.event = 'view' THEN x.sk END),
						COUNT(DISTINCT CASE WHEN x.event = 'registered' AND x.has_view = 1 THEN x.sk END)
					FROM (
						SELECT e.form_id, e.event, NULLIF(e.session_key,'') AS sk, IF(v.id IS NULL, 0, 1) AS has_view,
							IF(e.event = 'registered' AND v.id IS NOT NULL, v.utm_source, e.utm_source) AS s,
							IF(e.event = 'registered' AND v.id IS NOT NULL, v.utm_medium, e.utm_medium) AS m,
							IF(e.event = 'registered' AND v.id IS NOT NULL, v.utm_campaign, e.utm_campaign) AS c
						FROM {$this->events_table} e
						LEFT JOIN (
							SELECT session_key, MIN(id) AS mid FROM {$this->events_table} WHERE event = 'view' AND session_key <> '' GROUP BY session_key
						) fv ON fv.session_key = e.session_key AND e.session_key <> ''
						LEFT JOIN {$this->events_table} v ON v.id = fv.mid
						WHERE e.created_at BETWEEN %s AND %s AND e.source = 'registration' AND e.event IN ('view','registered')
					) x
					WHERE x.event = 'view' OR x.has_view = 1
					GROUP BY x.form_id, x.s, x.m, x.c",
					$day, $start, $end
				) );
				// phpcs:enable
			}
		}

		/*
			* A session that started but never submitted, and has been quiet for 30 minutes, is abandoned.
			* The last field it touched gets the "abandoned here" count.
		*/
		function rollup_abandoned( $day, $start, $end ){
			global $wpdb;
			$cutoff = gmdate( 'Y-m-d H:i:s', time() - 30 * MINUTE_IN_SECONDS );
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names come from $wpdb->prefix
			$sessions = $wpdb->get_results( $wpdb->prepare(
				"SELECT session_key, form_id, source, MAX(created_at) AS last_at, SUM(event='start') AS started, SUM(event='submit') AS submitted
				FROM {$this->events_table} WHERE created_at BETWEEN %s AND %s AND session_key <> ''
				GROUP BY session_key, form_id, source
				HAVING started > 0 AND submitted = 0 AND last_at < %s LIMIT 5000",
				$start, $end, $cutoff
			) );
			if( empty( $sessions ) ){
				return;
			}

			$last_field = array();
			foreach( array_chunk( wp_list_pluck( $sessions, 'session_key' ), 500 ) as $keys ){
				$holders = implode( ',', array_fill( 0, count( $keys ), '%s' ) );
				$args    = array_merge( array( $start, $end ), $keys );
				$rows    = $wpdb->get_results( $wpdb->prepare(
					"SELECT session_key, field_id FROM {$this->events_table} WHERE created_at BETWEEN %s AND %s AND event = 'field' AND session_key IN ($holders) ORDER BY id ASC",
					$args
				) );
				foreach( (array) $rows as $row ){
					$last_field[ $row->session_key ] = $row->field_id;
				}
			}

			$counts = array();
			foreach( $sessions as $s ){
				if( empty( $last_field[ $s->session_key ] ) ){
					continue;
				}
				$k = $s->source . '|' . (int) $s->form_id . '|' . $last_field[ $s->session_key ];
				$counts[ $k ] = isset( $counts[ $k ] ) ? $counts[ $k ] + 1 : 1;
			}
			foreach( $counts as $k => $n ){
				list( $source, $form_id, $field ) = explode( '|', $k, 3 );
				$wpdb->query( $wpdb->prepare(
					"UPDATE {$this->daily_table} SET abandoned = %d WHERE day = %s AND source = %s AND form_id = %d AND event = 'field' AND field_id = %s",
					$n, $day, $source, (int) $form_id, $field
				) );
			}
			// phpcs:enable
		}

		/*
			* Admin menu: Pie Register > Analytics
		*/
		function add_menu(){
			if( ! $this->is_pro() ){
				return;
			}
			$this->page_suffix = add_submenu_page(
				'pie-register',
				__( 'Analytics', 'pie-register' ),
				__( 'Analytics', 'pie-register' ),
				'piereg_manage_cap',
				'pie-analytics',
				array( $this, 'render_page' ),
				1
			);
			add_action( 'admin_print_scripts-' . $this->page_suffix, array( $this, 'admin_assets' ) );
		}

		function admin_assets(){
			wp_enqueue_style( 'piereg_analytics_css', $this->plugin_url( 'assets/css/piereg_analytics.css' ), array(), $this->asset_version() );
			wp_enqueue_script( 'piereg_analytics_admin', $this->plugin_url( 'assets/js/piereg_analytics_admin.js' ), array( 'jquery' ), $this->asset_version(), true );

			$forms = array();
			foreach( $this->get_forms() as $id => $title ){
				$forms[] = array( 'id' => (int) $id, 'title' => $title );
			}
			wp_localize_script( 'piereg_analytics_admin', 'pieAnalyticsAdmin', array(
				'ajaxURL'	=> admin_url( 'admin-ajax.php' ),
				'nonce'		=> wp_create_nonce( 'piereg_analytics_admin' ),
				'exportURL'	=> wp_nonce_url( admin_url( 'admin-post.php?action=piereg_analytics_export' ), 'piereg_analytics_export' ),
				'forms'		=> $forms,
				'today'		=> gmdate( 'Y-m-d' ),
				'i18n'		=> array(
					'loading'	=> __( 'Loading…', 'pie-register' ),
					'error'		=> __( 'Could not load analytics data.', 'pie-register' ),
					'noData'	=> __( 'No data for this range yet.', 'pie-register' ),
					'pickForm'	=> __( 'Select a single form to see field drop-off.', 'pie-register' ),
					'saved'		=> __( 'Settings saved.', 'pie-register' ),
					'confirm'	=> __( 'Delete ALL analytics data? This cannot be undone.', 'pie-register' ),
					'purged'	=> __( 'All analytics data deleted.', 'pie-register' ),
					'views'		=> __( 'Views', 'pie-register' ),
					'started'	=> __( 'Started', 'pie-register' ),
					'submitted'	=> __( 'Submitted', 'pie-register' ),
					'registered'=> __( 'Registered', 'pie-register' ),
					'activated'	=> __( 'Activated', 'pie-register' ),
					'conversion'=> __( 'Conversion', 'pie-register' ),
					'notTracked'=> __( 'Accounts from visits the tracker skipped (logged in, Do Not Track or Global Privacy Control, no consent, or JavaScript off). They have no campaign to attribute.', 'pie-register' ),
					/* translators: %s: number of accounts that came from tracked visits */
					'fromTracked'=> __( '%s from tracked visits', 'pie-register' ),
					'noSources'	=> __( 'No visits from tagged links in this range. Add utm_source / utm_medium / utm_campaign to the links that point to your registration page.', 'pie-register' )
				)
			) );
		}

		/*
			* All registration forms: id => title
		*/
		function get_forms(){
			// The free version has a single registration form
			$forms	= array();
			$id		= $this->free_form_id();
			if( $id > 0 ){
				$info = get_option( 'piereg_form_field_option_' . $id );
				$forms[ $id ] = ( is_array( $info ) && ! empty( $info['Title'] ) ) ? $info['Title'] : sprintf( /* translators: %d: form id */ __( 'Form %d', 'pie-register' ), $id );
			}
			return $forms;
		}

		function free_form_id(){
			return (int) get_option( 'piereg_form_free_id' );
		}

		function render_page(){
			if( ! current_user_can( 'piereg_manage_cap' ) ){
				wp_die( esc_html__( 'You do not have permission to view this page.', 'pie-register' ) );
			}
			$options = $this->get_options();
			include_once PIEREG_DIR_NAME . '/menus/PieRegAnalytics.php';
		}

		/*
			* Make sure today's numbers are fresh when the admin looks (at most every 5 minutes)
		*/
		function maybe_rollup_today(){
			if( get_transient( 'piereg_analytics_roll_today' ) ){
				return;
			}
			set_transient( 'piereg_analytics_roll_today', 1, 5 * MINUTE_IN_SECONDS );
			$this->rollup_days( array( gmdate( 'Y-m-d' ) ) );
		}

		function admin_request_ok(){
			if( ! check_ajax_referer( 'piereg_analytics_admin', 'nonce', false ) ){
				wp_send_json_error( array( 'message' => __( 'Security check failed.', 'pie-register' ) ), 403 );
			}
			if( ! current_user_can( 'piereg_manage_cap' ) || ! $this->is_pro() ){
				wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pie-register' ) ), 403 );
			}
		}

		/*
			* Read and validate the filters from a request (form, from, to)
		*/
		function read_filters(){
			// phpcs:disable WordPress.Security.NonceVerification -- called only after the nonce was checked
			$form_id	= isset( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : 0;
			$from		= isset( $_REQUEST['from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['from'] ) ) : '';
			$to			= isset( $_REQUEST['to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['to'] ) ) : '';
			// phpcs:enable
			if( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $from ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $to ) ){
				$to		= gmdate( 'Y-m-d' );
				$from	= gmdate( 'Y-m-d', time() - 29 * DAY_IN_SECONDS );
			}
			if( $from > $to ){
				$tmp = $from; $from = $to; $to = $tmp;
			}
			if( ( strtotime( $to ) - strtotime( $from ) ) > 400 * DAY_IN_SECONDS ){
				$from = gmdate( 'Y-m-d', strtotime( $to ) - 400 * DAY_IN_SECONDS );
			}
			// Only the free form is reported on
			$form_id	= $this->free_form_id();
			return array( $form_id, $from, $to );
		}

		/*
			* AJAX: numbers for the admin screen
		*/
		function ajax_data(){
			$this->admin_request_ok();
			global $wpdb;
			list( $form_id, $from, $to ) = $this->read_filters();
			$this->maybe_rebuild_sources();
			$this->maybe_rollup_today();

			$where	= 'day BETWEEN %s AND %s AND source = %s';
			$args	= array( $from, $to, 'registration' );
			// Always limited to the free form (no form set yet = no data), so other forms never show
			$where	.= ' AND form_id = %d';
			$args[]	 = $form_id;

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- $where holds only fixed fragments and placeholders
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT event, SUM(total) AS total, SUM(sessions) AS sessions FROM {$this->daily_table} WHERE $where AND field_id = '' GROUP BY event", $args
			) );
			// $totals counts tracked visits (sessions) at every step so the funnel and conversion compare like with like.
			// $accounts counts every account, including ones from visits the tracker skipped (logged in, Do Not Track, no JS).
			$totals		= array( 'view' => 0, 'start' => 0, 'submit' => 0, 'registered' => 0, 'activated' => 0 );
			$accounts	= array( 'registered' => 0, 'activated' => 0 );
			foreach( (array) $rows as $r ){
				if( isset( $totals[ $r->event ] ) ){
					$totals[ $r->event ] = (int) $r->sessions;
				}
				if( isset( $accounts[ $r->event ] ) ){
					$accounts[ $r->event ] = (int) $r->total;
				}
			}

			$trend_rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT day, event, SUM(total) AS total, SUM(sessions) AS sessions FROM {$this->daily_table} WHERE $where AND field_id = '' AND event IN ('view','registered') GROUP BY day, event ORDER BY day ASC", $args
			) );
			$days = array();
			for( $t = strtotime( $from ); $t <= strtotime( $to ); $t += DAY_IN_SECONDS ){
				$days[ gmdate( 'Y-m-d', $t ) ] = array( 'day' => gmdate( 'Y-m-d', $t ), 'views' => 0, 'registered' => 0 );
			}
			foreach( (array) $trend_rows as $r ){
				if( ! isset( $days[ $r->day ] ) ){
					continue;
				}
				if( $r->event === 'view' ){
					$days[ $r->day ]['views'] = (int) $r->sessions;
				}else{
					$days[ $r->day ]['registered'] = (int) $r->total;
				}
			}

			$fields = array();
			if( $form_id > 0 ){
				$field_rows = $wpdb->get_results( $wpdb->prepare(
					"SELECT field_id, event, SUM(total) AS total, SUM(sessions) AS sessions, SUM(abandoned) AS abandoned FROM {$this->daily_table} WHERE $where AND field_id <> '' AND event IN ('field','error') GROUP BY field_id, event", $args
				) );
				$map	= $this->get_field_map( $form_id );
				$acc	= array();
				foreach( (array) $field_rows as $r ){
					if( ! isset( $acc[ $r->field_id ] ) ){
						$acc[ $r->field_id ] = array( 'field' => $r->field_id, 'label' => isset( $map[ $r->field_id ] ) ? $map[ $r->field_id ] : $r->field_id, 'interacted' => 0, 'errors' => 0, 'abandoned' => 0 );
					}
					if( $r->event === 'field' ){
						$acc[ $r->field_id ]['interacted'] = (int) $r->sessions;
						$acc[ $r->field_id ]['abandoned'] += (int) $r->abandoned;
					}else{
						$acc[ $r->field_id ]['errors'] = (int) $r->total;
					}
				}
				// Form order (the builder's order); keys the form no longer has go last
				$order	= array_flip( array_keys( $map ) );
				uasort( $acc, function( $a, $b ) use ( $order ){
					$pa = isset( $order[ $a['field'] ] ) ? $order[ $a['field'] ] : PHP_INT_MAX;
					$pb = isset( $order[ $b['field'] ] ) ? $order[ $b['field'] ] : PHP_INT_MAX;
					return $pa === $pb ? strcmp( $a['field'], $b['field'] ) : ( $pa < $pb ? -1 : 1 );
				} );
				$fields = array_values( $acc );
			}
			// phpcs:enable

			/* Traffic sources (UTM) */
			$uwhere	= 'day BETWEEN %s AND %s';
			$uargs	= array( $from, $to );
			$uwhere	.= ' AND form_id = %d';
			$uargs[] = $form_id;
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- $uwhere holds only fixed fragments and placeholders
			$src_rows = $wpdb->get_results( $wpdb->prepare( "SELECT utm_source, utm_medium, utm_campaign, SUM(views) AS views, SUM(registered) AS registered FROM {$this->utm_table} WHERE $uwhere GROUP BY utm_source, utm_medium, utm_campaign ORDER BY views DESC, registered DESC LIMIT 15", $uargs ) );
			$sources = array();
			foreach( (array) $src_rows as $r ){
				$label = $r->utm_source === '' ? __( '(direct / no campaign)', 'pie-register' ) : $r->utm_source . ( $r->utm_medium !== '' ? ' / ' . $r->utm_medium : '' );
				$sources[] = array( 'source' => $label, 'campaign' => $r->utm_campaign, 'views' => (int) $r->views, 'registered' => (int) $r->registered );
			}

			$untracked = max( 0, $accounts['registered'] - $totals['registered'] );
			if( $untracked > 0 ){
				$sources[] = array( 'source' => __( '(not tracked)', 'pie-register' ), 'campaign' => '', 'views' => null, 'registered' => $untracked, 'untracked' => true );
			}

			$views = $totals['view'];
			wp_send_json_success( array(
				'sources'		=> $sources,
				'totals'		=> $totals,
				'accounts'		=> $accounts,
				'conversion'	=> $views > 0 ? min( 100, round( $totals['registered'] / $views * 100, 1 ) ) : null,
				'trend'			=> array_values( $days ),
				'fields'		=> $fields,
				'from'			=> $from,
				'to'			=> $to
			) );
		}

		/*
			* AJAX: save the settings panel
		*/
		function ajax_save_settings(){
			$this->admin_request_ok();
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified in admin_request_ok()
			$new = array(
				'enable'			=> ! empty( $_POST['enable'] ) ? 1 : 0,
				'exclude_logged_in'	=> ! empty( $_POST['exclude_logged_in'] ) ? 1 : 0,
				'respect_dnt'		=> ! empty( $_POST['respect_dnt'] ) ? 1 : 0,
				'require_consent'	=> ! empty( $_POST['require_consent'] ) ? 1 : 0,
				'raw_retention'		=> isset( $_POST['raw_retention'] ) ? min( 90, max( 7, absint( $_POST['raw_retention'] ) ) ) : 30,
				'daily_retention'	=> isset( $_POST['daily_retention'] ) ? min( 1095, max( 30, absint( $_POST['daily_retention'] ) ) ) : 365
			);
			// phpcs:enable
			update_option( 'piereg_analytics_options', $new );
			wp_send_json_success( array( 'message' => __( 'Settings saved.', 'pie-register' ) ) );
		}

		/*
			* AJAX: delete everything
		*/
		function ajax_purge(){
			$this->admin_request_ok();
			global $wpdb;
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.SchemaChange -- fixed table names from $wpdb->prefix
			$wpdb->query( "TRUNCATE TABLE {$this->events_table}" );
			$wpdb->query( "TRUNCATE TABLE {$this->daily_table}" );
			$wpdb->query( "TRUNCATE TABLE {$this->utm_table}" );
			// phpcs:enable
			wp_send_json_success( array( 'message' => __( 'All analytics data deleted.', 'pie-register' ) ) );
		}

		/*
			* CSV export of the daily totals
		*/
		function export_csv(){
			check_admin_referer( 'piereg_analytics_export' );
			if( ! current_user_can( 'piereg_manage_cap' ) || ! $this->is_pro() ){
				wp_die( esc_html__( 'Permission denied.', 'pie-register' ), '', array( 'response' => 403 ) );
			}
			global $wpdb;
			list( $form_id, $from, $to ) = $this->read_filters();

			$where	= 'day BETWEEN %s AND %s';
			$args	= array( $from, $to );
			// Only the free form
			$where	.= ' AND form_id = %d';
			$args[]	 = $form_id;
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.PreparedSQL.NotPrepared -- $where holds only fixed fragments and placeholders
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT day, source, form_id, event, field_id, total, sessions, abandoned FROM {$this->daily_table} WHERE $where ORDER BY day ASC, form_id ASC, event ASC", $args ), ARRAY_A );
			// phpcs:enable

			nocache_headers();
			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="pie-register-analytics-' . $from . '_' . $to . '.csv"' );
			$out = fopen( 'php://output', 'w' );
			fputcsv( $out, array( 'day', 'source', 'form_id', 'event', 'field_id', 'total', 'sessions', 'abandoned' ) );
			foreach( (array) $rows as $row ){
				fputcsv( $out, $row );
			}
			fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- closes the php://output stream, not a file
			exit;
		}

		function privacy_policy_text(){
			if( ! function_exists( 'wp_add_privacy_policy_content' ) ){
				return;
			}
			wp_add_privacy_policy_content(
				'Pie Register',
				wp_kses_post( '<p>' . __( 'Pie Register can count anonymous steps on registration forms (viewed, started, submitted) to show form owners where visitors leave. It stores a random per-tab code, the form, the field name, the time and, when the visit came from a tagged link, its campaign tags (utm_source, utm_medium, utm_campaign); those tags are also kept in a cookie that ends with the browser session so a visit is still attributed when the form is reached from another page. It does not store IP addresses, user agents, user accounts or anything typed into a field. Raw events are deleted automatically after the retention period set in Pie Register > Analytics.', 'pie-register' ) . '</p>' )
			);
		}

		/*
			* Uninstall: remove tables, options and the activation marker
		*/
		function uninstall(){
			global $wpdb;
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.SchemaChange -- fixed table names from $wpdb->prefix
			$wpdb->query( "DROP TABLE IF EXISTS {$this->events_table}" );
			$wpdb->query( "DROP TABLE IF EXISTS {$this->daily_table}" );
			$wpdb->query( "DROP TABLE IF EXISTS {$this->utm_table}" );
			// phpcs:enable
			delete_option( 'piereg_analytics_options' );
			delete_option( 'piereg_analytics_db_version' );
			delete_metadata( 'user', 0, 'piereg_analytics_activated', '', true );
			delete_metadata( 'user', 0, 'piereg_analytics_session', '', true );
			delete_option( 'piereg_analytics_sources_rule' );
		}
	}

	$GLOBALS['piereg_analytics'] = new PieReg_Analytics();

	/*
		* Global helper for addons: piereg_analytics_record_event( 'registration', $form_id, 'view' )
	*/
	if( ! function_exists( 'piereg_analytics_record_event' ) ){
		function piereg_analytics_record_event( $source, $form_id, $event, $field_id = '', $session_key = '', $page_no = 0 ){
			global $piereg_analytics;
			return is_object( $piereg_analytics ) ? $piereg_analytics->record_event( $source, $form_id, $event, $field_id, $session_key, $page_no ) : false;
		}
	}
}
