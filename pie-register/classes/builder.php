<?php
/*
	* Form builder: opt-in "new layout"
	* Stage A of the builder modernisation. The builder markup, field settings and save action are unchanged;
	* the new layout is CSS and a small script on top. Each user chooses the layout for themselves.
	* Since v4.0.0.0
*/
if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'PieReg_Builder' ) ){

	class PieReg_Builder
	{
		const META = 'piereg_new_builder';

		function __construct(){
			add_filter( 'admin_body_class', array( $this, 'body_class' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'piereg_builder_header', array( $this, 'render_switch' ) );
			add_action( 'admin_post_piereg_toggle_builder', array( $this, 'toggle' ) );
			add_action( 'pie_uninstall_base', array( $this, 'uninstall' ) );
		}

		function on_builder_page(){
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- only reads which admin page is shown
			return ( is_admin() && isset( $_GET['page'] ) && sanitize_key( wp_unslash( $_GET['page'] ) ) === 'pr_new_registration_form' );
		}

		/* The new builder is the only builder (classic screen removed) */
		function is_new(){
			return is_user_logged_in();
		}

		function body_class( $classes ){
			if( $this->on_builder_page() && $this->is_new() ){
				$classes .= ' piereg-newbuilder';
			}
			return $classes;
		}

		function enqueue(){
			if( ! $this->on_builder_page() || ! $this->is_new() ){
				return;
			}
			$ver = defined( 'PIEREGISTER_VERSION' ) ? PIEREGISTER_VERSION : '4.0.0.0';
			wp_enqueue_style( 'piereg_builder_css', PIEREG_PLUGIN_URL . 'assets/css/piereg_builder.css', array(), $ver );
			wp_enqueue_script( 'piereg_builder_js', PIEREG_PLUGIN_URL . 'assets/js/piereg_builder.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable' ), $ver, true );
			wp_localize_script( 'piereg_builder_js', 'pieBuilder', array(
				'i18n' => array(
					'pick'			=> __( 'Select a field on the canvas to see its settings. Click the form title for the form settings, or the submit button for its settings.', 'pie-register' ),
					/* translators: 1: current step, 2: number of steps */
					'stepOf'		=> __( 'Step %1$s of %2$s', 'pie-register' ),
					'selectField'	=> __( '- Select field -', 'pie-register' ),
					'submit'		=> __( 'Register', 'pie-register' ),
					'reset'			=> __( 'Reset', 'pie-register' ),
					'type'			=> __( 'Field type', 'pie-register' ),
					'label'			=> __( 'Label', 'pie-register' ),
					'placeholder'	=> __( 'Placeholder', 'pie-register' ),
					'required'		=> __( 'Required', 'pie-register' ),
					'logic'			=> __( 'Conditional logic', 'pie-register' ),
					'desc'			=> __( 'Description', 'pie-register' ),
					'rule'			=> __( 'Validation rule', 'pie-register' ),
					'message'		=> __( 'Validation message', 'pie-register' ),
					'css'			=> __( 'CSS class', 'pie-register' ),
					'profile'		=> __( 'Show in profile', 'pie-register' ),
					'action'		=> __( 'Action', 'pie-register' ),
					'when'			=> __( 'When', 'pie-register' ),
					'or'			=> __( 'Or', 'pie-register' ),
					'step'			=> __( 'Step', 'pie-register' ),
					'removeStep'	=> __( 'Remove this empty step', 'pie-register' ),
					'unsaved'		=> __( 'Unsaved changes', 'pie-register' ),
					'saving'		=> __( 'Saving…', 'pie-register' ),
					'notYet'		=> __( 'This field is not available on this site.', 'pie-register' ),
					'onlyOnce'		=> __( 'This field can only be added once and is already on the form.', 'pie-register' ),
					'addHint'		=> __( 'Click to add, or drag onto the form', 'pie-register' ),
					'dropHere'		=> __( 'Drop fields here, or click a field on the left.', 'pie-register' ),
					'locked'		=> __( 'The E-mail field is required by Pie Register and cannot be removed.', 'pie-register' ),
					'confirmDelete'	=> __( 'Remove this field from the form? You can undo before saving.', 'pie-register' ),
					'leaveTitle'	=> __( 'Leave without saving?', 'pie-register' ),
					'reloadTitle'	=> __( 'Reload without saving?', 'pie-register' ),
					'stepSettings'	=> __( 'Step settings', 'pie-register' ),
					'next'			=> __( 'Next', 'pie-register' ),
					'previous'		=> __( 'Previous', 'pie-register' ),
					'imageButton'	=> __( 'image button', 'pie-register' ),
					'reload'		=> __( 'Reload', 'pie-register' ),
					'leaveText'		=> __( 'You have changes that are not saved yet. If you leave now, they will be lost.', 'pie-register' ),
					'leave'			=> __( 'Leave', 'pie-register' ),
					'stay'			=> __( 'Stay and keep editing', 'pie-register' ),
					'removeTitle'	=> __( 'Remove field', 'pie-register' ),
					'titleNeeded'	=> __( 'Form title needed', 'pie-register' ),
					'titleText'		=> __( 'Please give the form a title in the top bar before saving.', 'pie-register' ),
					'untitled'		=> __( 'Untitled form', 'pie-register' ),
					'cannotRemove'	=> __( 'This field is required', 'pie-register' ),
					'cancel'		=> __( 'Cancel', 'pie-register' ),
					'ok'			=> __( 'OK', 'pie-register' ),
					'up'			=> __( 'Move up', 'pie-register' ),
					'down'			=> __( 'Move down', 'pie-register' ),
					'duplicate'		=> __( 'Duplicate', 'pie-register' ),
					'remove'		=> __( 'Remove', 'pie-register' ),
					'moveTo'		=> __( 'Move to step…', 'pie-register' ),
					'option'		=> __( 'Option', 'pie-register' ),
					'yes'			=> __( 'Yes', 'pie-register' ),
					'no'			=> __( 'No', 'pie-register' ),
					'show'			=> __( 'Show', 'pie-register' ),
					'hiddenText'	=> __( 'Hidden from visitors', 'pie-register' ),
					'htmlText'		=> __( 'HTML content', 'pie-register' ),
					'select'		=> __( 'Select…', 'pie-register' ),
					'confirmPass'	=> __( 'Confirm Password', 'pie-register' ),
					'confirmEmail'	=> __( 'Confirm Email', 'pie-register' ),
					'logicOff'		=> __( 'Off', 'pie-register' ),
					'logicOn'		=> __( 'On', 'pie-register' ),
					'showField'		=> __( 'Show', 'pie-register' ),
					'hideField'		=> __( 'Hide', 'pie-register' ),
					'whenText'		=> __( 'this field when', 'pie-register' ),
					'pickField'		=> __( 'Choose a field…', 'pie-register' ),
					'valueText'		=> __( 'Value', 'pie-register' ),
					'andText'		=> __( 'and', 'pie-register' ),
					'orText'		=> __( 'or', 'pie-register' ),
					'orGroup'		=> __( 'or another set of rules', 'pie-register' ),
					'addOption'		=> __( 'Add option', 'pie-register' ),
					'noSettings'	=> __( 'This field has no settings.', 'pie-register' ),
					'op_=='			=> __( 'is', 'pie-register' ),
					'op_!='			=> __( 'is not', 'pie-register' ),
					'op_empty'		=> __( 'is empty', 'pie-register' ),
					'op_not_empty'	=> __( 'is not empty', 'pie-register' ),
					'op_>'			=> __( 'greater than', 'pie-register' ),
					'op_<'			=> __( 'less than', 'pie-register' ),
					'op_contains'	=> __( 'contains', 'pie-register' ),
					'op_starts_with'=> __( 'starts with', 'pie-register' ),
					'op_ends_with'	=> __( 'ends with', 'pie-register' ),
					'op_not_contains'	=> __( 'does not contain', 'pie-register' ),
					'op_not_starts_with'=> __( 'does not start with', 'pie-register' ),
					'op_not_ends_with'	=> __( 'does not end with', 'pie-register' ),
					'op_range'		=> __( 'in range', 'pie-register' ),
					'logicHelp'		=> __( 'Rules in a group must all match (AND). Groups are alternatives (OR). For NOT, use the "is not", "is not empty" or "does not ..." rules.', 'pie-register' ),
					'first'			=> __( 'First name', 'pie-register' ),
					'last'			=> __( 'Last name', 'pie-register' ),
					'street'		=> __( 'Street Address', 'pie-register' ),
					'address2'		=> __( 'Address Line 2', 'pie-register' ),
					'city'			=> __( 'City', 'pie-register' ),
					'stateIntl'		=> __( 'State / Province / Region', 'pie-register' ),
					'state'			=> __( 'State', 'pie-register' ),
					'province'		=> __( 'Province', 'pie-register' ),
					'zip'			=> __( 'Zip / Postal Code', 'pie-register' ),
					'country'		=> __( 'Country', 'pie-register' ),
					'chooseFile'	=> __( 'Choose file…', 'pie-register' ),
					'dateText'		=> __( 'mm/dd/yyyy', 'pie-register' ),
					'timeText'		=> __( 'hh : mm', 'pie-register' ),
					'agree'			=> __( 'I agree to the terms', 'pie-register' ),
					'robot'			=> __( "I'm not a robot", 'pie-register' ),
					'listText'		=> __( 'Row 1 · + Add another', 'pie-register' ),
					'newNote'		=> __( 'New field with its default settings. Editing settings comes in the next step.', 'pie-register' ),
					'readonly'		=> __( 'Editing settings comes in the next step.', 'pie-register' )
				)
			) );
		}

		/*
			* Link under the page title: switch between the classic and the new layout
		*/
		/* No layout switch any more: the new builder is always used */
		function render_switch(){
		}

		function toggle(){
			check_admin_referer( 'piereg_toggle_builder' );
			if( ! current_user_can( 'piereg_manage_cap' ) ){
				wp_die( esc_html__( 'Permission denied.', 'pie-register' ), '', array( 'response' => 403 ) );
			}
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- nonce checked above
			$mode	= ( isset( $_GET['mode'] ) && sanitize_key( wp_unslash( $_GET['mode'] ) ) === 'new' ) ? '1' : '0';
			$args	= array( 'page' => 'pr_new_registration_form' );
			if( isset( $_GET['form_id'] ) && absint( $_GET['form_id'] ) > 0 ){
				$args['form_id'] = absint( $_GET['form_id'] );
			}
			// phpcs:enable
			update_user_meta( get_current_user_id(), self::META, $mode );
			wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
			exit;
		}

		function uninstall(){
			delete_metadata( 'user', 0, self::META, '', true );
		}
	}

	$GLOBALS['piereg_builder'] = new PieReg_Builder();
}
