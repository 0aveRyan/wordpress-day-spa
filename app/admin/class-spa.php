<?php
namespace WordPress_Day_Spa\Admin;

/**
 * Class SPA
 * @package WordPress_Day_Spa\Admin
 */
class SPA {
	/**
	 * @var
	 */
	public $plugin_dir;
	/**
	 * @var
	 */
	public $plugin_url;
	/**
	 * SPA constructor.
	 *
	 * @param string $plugin_dir
	 * @param string $plugin_url
	 */
	function __construct( $plugin_dir, $plugin_url ) {
		// preset vars directory and url location for plugin (from Abstract Plugin Base)
		$this->plugin_dir = $plugin_dir;
		$this->plugin_url = $plugin_url;
		// Register SPA Page
		add_action( 'admin_menu', array( $this, 'register_admin_dashboard_page' ) );
		// Register and Enqueue Assets
		add_action( 'admin_enqueue_scripts', array( $this, 'register_spa_assets' ) );
	}

	/**
	 *
	 */
	function register_admin_dashboard_page() {
		add_menu_page(
			'SPA Day',
			'SPA Day',
			'read',
			'spa-day',
			array( $this, 'dashboard_page_callback' ),
			'dashicons-media-code',
			999
		);

	}

	/**
	 *
	 */
	function dashboard_page_callback() {
		ob_start(); ?>
		<div id="app-wrap" class="wrap">
			<div id="app"></div>
		</div>
		<?php echo ob_get_clean();
		echo '<div id="app"></div>';
	}

	/**
	 * @param string $hook
	 */
	function register_spa_assets( $hook ) {

		if ( 'toplevel_page_spa-day' !== $hook ) {
			return;
		}

		wp_register_script(
			'wds-lib-bundle',
			$this->plugin_url . 'app/assets/js/wds.lib.bundle.js',
			array(),
			rand(0,100000000),
			true
		);

		wp_register_script(
			'wds-app-bundle',
			$this->plugin_url . 'app/assets/js/wds.app.bundle.js',
			array( 'wds-lib-bundle' ),
			rand(0,100000000),
			true
		);

		wp_enqueue_media();
		wp_enqueue_script( 'wds-app-bundle' );

		wp_localize_script( 'wds-app-bundle', 'wdsData', array(
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'userDisplayName' => wp_get_current_user()->display_name,
			'userId' => wp_get_current_user()->ID,
			'siteName' => get_bloginfo( 'name' ),
			'siteUrl' => get_bloginfo( 'url' ),
			'restUrl' => rest_url(),
			'adminUrl' => admin_url()
		) );
	}
}
