<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Elementor\Plugin as ElementorPlugin;
/**
 * Improve Elementor Compatibility.
*/
class PieRegElementor {

    function __construct() {
        $this->hooks();
    }
    
	/**
     * Integration hooks.
	*/
    protected function hooks() {
        // Skip if Elementor is not available.
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
            return;
		}
        
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'preview_assets' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_assets' ] );
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widget' ) );
	}

	/**
	 * Load assets in the preview panel.
	 */
	public function preview_assets() {

		if ( ! ElementorPlugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		wp_enqueue_style(
			'pieregister-integrations',
			PIEREG_PLUGIN_URL . "assets/css/elementor-integration.css",
			'',
			PIEREGISTER_VERSION
		);

		wp_enqueue_script(
			'pieregister-elementor',
			PIEREG_PLUGIN_URL . "assets/js/elementor-integration.js",
			[ 'elementor-frontend', 'jquery', 'wp-util' ],
			PIEREGISTER_VERSION,
			false
		);
	}

	/**
	 * Load an integration css in the elementor document.
	 */
	public function editor_assets() {

		if ( empty( $_GET['action'] ) || $_GET['action'] !== 'elementor' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing check, only decides whether to enqueue editor CSS
			return;
		}

		wp_enqueue_style(
			'pieregister-integrations',
			PIEREG_PLUGIN_URL . "assets/css/elementor-integration.css",
			array(),
			PIEREGISTER_VERSION
		);

		wp_enqueue_script(
			'pieregister-elementor',
			PIEREG_PLUGIN_URL . "assets/js/elementor-integration.js",
			array(),
			PIEREGISTER_VERSION,
			false
		);
	}

	/**
	 * Register Pie Register Widget.
	 */
	public function register_widget() {
        if( file_exists(plugin_dir_path(__FILE__)."elementorWidget.php") )
            require_once(plugin_dir_path(__FILE__)."elementorWidget.php");

		ElementorPlugin::instance()->widgets_manager->register_widget_type( new PieRegElementorWidget() );
	}

}
// Load Elementor code only if Elementor is activated.
if (class_exists( '\Elementor\Plugin' ) ) {
	add_action( 'pieregister_elementor', 'initialize_elementorwidget');
}
function initialize_elementorwidget(){
	$Elementor_widget = new PieRegElementor();
}