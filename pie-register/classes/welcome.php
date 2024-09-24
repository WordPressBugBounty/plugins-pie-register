<?php

/**
 * Welcome page class.
 *
 * This page is shown when the plugin is activated.
 *
 */
class PieRegister_Welcome extends PieReg_Base {

	/**
	 * Hidden welcome page slug.
	 *
	 */
	const SLUG = 'pieregister-getting-started';

	/**
	 * Primary class constructor.
	 *
	 */
	public function __construct() {

		add_action( 'plugins_loaded', array( $this, 'hooks' ) );
    
    }

	/**
	 * Register all WP hooks.
	 *
	 */
	public function hooks() {

		// If user is in admin ajax or doing cron, return.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// If user cannot manage_options, return.
		if ( !current_user_can('piereg_manage_cap') ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'admin_head', array( $this, 'hide_menu' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );
		add_action( 'admin_init', array( $this, 'piereg_welcome' ) );
	}

	/**
	 * Register the pages to be used for the Welcome screen (and tabs).
	 *
	 * These pages will be removed from the Dashboard menu, so they will
	 * not actually show. Sneaky, sneaky.
	 *
	 */
	public function register() {

		// Getting started - shows after installation.
		add_dashboard_page(
			esc_html__( 'Welcome to PieRegister', 'pie-register' ),
			esc_html__( 'Welcome to PieRegister', 'pie-register' ),
			apply_filters( 'pieregister_welcome_cap', 'piereg_manage_cap' ),
			self::SLUG,
			array( $this, 'output' )
		);
	}

	/**
	 * Removed the dashboard pages from the admin menu.
	 *
	 * This means the pages are still available to us, but hidden.
	 *
	 */
	public function hide_menu() {
		remove_submenu_page( 'index.php', self::SLUG );
	}

	/**
	 * Welcome screen redirect.
	 *
	 * This function checks if a new install or update has just occurred. If so,
	 * then we redirect the user to the appropriate page.
	 *
	 */
	public function redirect() {

		// Check if we should consider redirection.
		if ( ! get_transient( 'pieregister_activation_redirect' ) ) {
			return;
		}

		// If we are redirecting, clear the transient so it only happens once.
		delete_transient( 'pieregister_activation_redirect' );

		// Check option to disable welcome redirect.
		if ( get_option( 'pieregister_activation_redirect', false ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=' . self::SLUG ) );
		exit;
	}

	/**
	 * Enqueue CSS for activation page.
	 *
	 */
	function piereg_welcome()
	{
		wp_enqueue_style('pie_welcome_css'); 
	}

	/**
	 * Getting Started screen. Shows after first install.
	 *
	 */
	public function output() {
		?>

		<div id="pieregister-welcome">
			<div class="container">
				<div class="intro">
					<div class="pie-register-logo">
						<img src="<?php echo plugins_url("assets/images/pie-register-logo.png",dirname(__FILE__)) ?>" alt="<?php esc_attr_e( 'Pie Register Logo', 'pie-register' ); ?>">
					</div>
					<div class="block">
						<h6 class="welcome-msg"><?php esc_html_e( 'How to create and use your first registration form', 'pie-register' ); ?></h6>
					</div>
					<div class="block">
						<div class="welcome-pr-video">
							<iframe width="833" height="441" src="https://www.youtube.com/embed/Gz-AOxZxHGQ?rel=0" title="How to create your first registration form" frameborder="0" rel="0"allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>						
						</div>
					</div>
					<div class="block block-white get-started-btns">
						<div class="button-wrap pieregister-clear">
							<div class="left">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=pie-register' ) ); ?>" class="pieregister-btn pieregister-btn-block pieregister-btn-lg pieregister-btn-red">
									<?php esc_html_e( 'Get Started', 'pie-register' ); ?>
								</a>
							</div>
							<div class="left">
								<a href="https://pieregister.com/documentation/"
									class="pieregister-btn pieregister-btn-block pieregister-btn-lg pieregister-btn-white" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Read the Full Guide', 'pie-register' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div><!-- /.intro -->

				<div class="features">
					<div class="block-features">
						<h1><?php esc_html_e( 'Frequently Used Features', 'pie-register' ); ?></h1>
						<div class="feature-list">
							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pie-notifications')) ?>" target="_blank">
								<div class="feature-block first">
									<div class="feature-block-icon">
										<img src="<?php echo plugins_url("assets/images/welcome/email-notifications.png",dirname(__FILE__)) ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Set Email Notifications', 'pie-register' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pie-invitation-codes')) ?>" target="_blank">
								<div class="feature-block last">
									<div class="feature-block-icon">
										<img src="<?php echo plugins_url("assets/images/welcome/invitation-codes.png",dirname(__FILE__)) ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Generate Invitation Codes', 'pie-register' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pie-gateway-settings')) ?>" target="_blank">
								<div class="feature-block first">
									<div class="feature-block-icon">
										<img src="<?php echo plugins_url("assets/images/welcome/payment-methods.png",dirname(__FILE__)) ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Setup Payment Methods', 'pie-register' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pie-user-roles-custom')) ?>" target="_blank">
								<div class="feature-block last">
									<div class="feature-block-icon">
										<img src="<?php echo plugins_url("assets/images/welcome/user-roles.png",dirname(__FILE__)) ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Create User Roles', 'pie-register' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pie-black-listed-users')) ?>" target="_blank">
								<div class="feature-block first">
									<div class="feature-block-icon">
										<img src="<?php echo plugins_url("assets/images/welcome/control-users.png",dirname(__FILE__)) ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Control Users', 'pie-register' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pie-settings&tab=security')) ?>" target="_blank">
								<div class="feature-block last">
									<div class="feature-block-icon">
										<img src="<?php echo plugins_url("assets/images/welcome/update-security.png",dirname(__FILE__)) ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Update Security', 'pie-register' ); ?></h5>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div><!-- /.features -->

				<div class="resourceful-links">
					<div class="block block-white">
						<h1><?php esc_html_e( 'Resourceful Links', 'pie-register' ); ?></h1>
						<div class="resourceful-links-list">
							<div class="resourceful-link">
								<a href="https://pieregister.com/docs-category/getting-started/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo plugins_url("assets/images/welcome/getting-started.png",dirname(__FILE__)) ?>">
									</div>
									<h6><?php esc_html_e( 'Getting Started', 'pie-register' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href="https://pieregister.com/docs-category/addons/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo plugins_url("assets/images/welcome/addons.png",dirname(__FILE__)) ?>">
									</div>
									<h6><?php esc_html_e( 'Add-ons', 'pie-register' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href="https://pieregister.com/docs-category/features/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo plugins_url("assets/images/welcome/features.png",dirname(__FILE__)) ?>">
									</div>
									<h6><?php esc_html_e( 'Features', 'pie-register' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href="https://pieregister.com/docs-category/shortcuts/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo plugins_url("assets/images/welcome/shortcuts.png",dirname(__FILE__)) ?>">
									</div>
									<h6><?php esc_html_e( 'Shortcuts', 'pie-register' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href="https://pieregister.com/docs-category/how-to-articles/?utm_source=plugindashboard&utm_medium=abouttab&utm_campaign=documentlink" target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo plugins_url("assets/images/welcome/how-to-articles.png",dirname(__FILE__)) ?>">
									</div>
									<h6><?php esc_html_e( 'How-to', 'pie-register' ); ?></h6>
								</a>
							</div>
						</div>
					</div>
				</div><!-- /.resourceful links -->			
			</div><!-- /.container -->
		</div><!-- /#pieregister-welcome -->
		<?php
	}
}

new PieRegister_Welcome();
