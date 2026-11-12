<?php
/*
	* Shared WP-Cron dispatcher
	* One hourly event runs every task registered through the "piereg_cron_tasks" filter.
	* Since v4.0.0.0
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'PieReg_Cron' ) ){

	class PieReg_Cron
	{
		const HOOK = 'piereg_cron_hourly';

		function __construct(){
			add_action( 'init', array( $this, 'schedule' ) );
			add_action( self::HOOK, array( $this, 'run' ) );
			add_action( 'pie_deactivation_base', array( $this, 'unschedule' ) );
		}

		/*
			* Make sure the hourly event exists (also repairs a cleared schedule)
		*/
		function schedule(){
			if( ! wp_next_scheduled( self::HOOK ) ){
				wp_schedule_event( time() + 300, 'hourly', self::HOOK );
			}
		}

		function unschedule(){
			wp_clear_scheduled_hook( self::HOOK );
		}

		/*
			* Run all registered tasks. A short lock stops overlapping runs.
			* Usage: add_filter( 'piereg_cron_tasks', function( $tasks ){ $tasks['my_task'] = 'my_callback'; return $tasks; } );
		*/
		function run(){
			if( get_transient( 'piereg_cron_lock' ) ){
				return;
			}
			set_transient( 'piereg_cron_lock', 1, 5 * MINUTE_IN_SECONDS );

			$tasks = apply_filters( 'piereg_cron_tasks', array() );
			foreach( (array) $tasks as $key => $callback ){
				if( is_callable( $callback ) ){
					try{
						call_user_func( $callback );
					}catch( Throwable $e ){ // Errors too (TypeError etc.), so one broken task can't stop the rest or leave the lock set
						continue;
					}
				}
			}

			update_option( 'piereg_cron_last_run', time(), false );
			delete_transient( 'piereg_cron_lock' );
		}
	}

	$GLOBALS['piereg_cron'] = new PieReg_Cron();
}
