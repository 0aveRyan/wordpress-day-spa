<?php
namespace WordPress_Day_Spa;

use WPAZ_Plugin_Base\V_2_5\Abstract_Plugin;
use WordPress_Day_Spa\Admin\SPA;

/**
 * Class Plugin
 * @package WordPress_Day_Spa
 */
class Plugin extends Abstract_Plugin {

	/**
	 * @var string
	 */
	public static $autoload_class_prefix = __NAMESPACE__;
	/**
	 * @var string
	 */
	protected static $current_file = __FILE__;
	/**
	 * @var string
	 */
	public static $autoload_type = 'psr-4';
	/**
	 * @var int
	 */
	public static $autoload_ns_match_depth = 1;

	/**
	 * @param mixed $instance
	 */
	public function onload( $instance ) {
		// Nothing yet
	}

	public function init() {
		do_action( get_called_class() . '_before_init' );
		// nothing yet
		do_action( get_called_class() . '_after_init' );
	}

	public function authenticated_init() {
		if ( is_user_logged_in() ) {
			new SPA( $this->installed_dir, $this->installed_url );
		}
	}

	protected function defines_and_globals() {
	}

}
