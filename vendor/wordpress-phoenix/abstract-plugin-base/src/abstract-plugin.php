<?php
/**
 * Plugin_Base Class for WordPress Plugins
 *
 * @author  Seth Carstens
 * @package abtract-plugin-base
 * @version 2.5.1
 * @license GPL 2.0 - please retain comments that express original build of this file by the author.
 */

/*
 * Namespace with versions as a solution to composer vs WordPress plugins
 * Reference url https://wptavern.com/a-narrative-of-using-composer-in-a-wordpress-plugin
 */

namespace WPAZ_Plugin_Base\V_2_5;

/**
 * Class Plugin_Base
 */
abstract class Abstract_Plugin {
	/**
	 * Turn debugging on or off
	 *
	 * @var bool $debug
	 */
	public $debug;

	/**
	 * Used to hold an instance of the admin object related to the plugin.
	 *
	 * @var null|\stdClass|Abstract_Plugin $admin
	 */
	public $abstract_plugin_version = __NAMESPACE__;

	/**
	 * Used to hold an instance of the admin object related to the plugin.
	 *
	 * @var null|\stdClass|Abstract_Plugin $admin
	 */
	public $admin;

	/**
	 * Use magic constant to tell abstract class current namespace as prefix for all other namespaces in the plugin.
	 *
	 * @var string $autoload_class_prefix magic constant
	 */
	public static $autoload_class_prefix = __NAMESPACE__;

	/**
	 * Define the folder or folders that spl_autoload should check for custom PHP classes that need autoloaded
	 *
	 * @var array|string $autoload_dir
	 */
	public $autoload_dir = [ '/app/', '/app/admin/', '/app/admin/inc/' ];

	/**
	 * Usually the depth of your namespace prefix, defaults to 1, only applies to psr-4 autoloading type.
	 *
	 * @var string $autoload_ns_match_depth more efficient when set to 2, when using package [ns_prefix]/[ns]
	 */
	public static $autoload_ns_match_depth = 1;

	/**
	 * Autoload type can be classmap or psr-4
	 *
	 * @var string $autoload_dir classmap or psr-4 or false
	 */
	public static $autoload_type = 'classmap';

	/**
	 * Magic constant trick that allows extended classes to pull actual server file location, copy into subclass.
	 *
	 * @var string $current_file
	 */
	protected static $current_file = __FILE__;

	/**
	 * Filename prefix standard for WordPress when the file represents a class
	 *
	 * @var string $filename_prefix typically class- is the prefix
	 */
	public static $filename_prefix = 'class-';

	/**
	 * Plugins class object installed directory on the server
	 *
	 * @var string $installed_dir
	 */
	public $installed_dir;

	/**
	 * Plugin root directory on the server
	 *
	 * @var string $plugin_object_basedir
	 */
	public $plugin_basedir;

	/**
	 * Plugins URL for access to any static files or assets like css, js, or media
	 *
	 * @var string $installed_url
	 */
	public $installed_url;

	/**
	 * Modules is a collection class that holds the modules / parts of the plugin.
	 *
	 * @var \stdClass $modules
	 */
	public $modules;

	/**
	 * Related WordPress multisite network url with smarter fallbacks to guarantee a value
	 *
	 * @var string $network_url
	 */
	public $network_url;

	/**
	 * When main plugin filename matches folder name this gets the value from get_plugin_data()
	 *
	 * @var array $plugin_data Array of meta data representing meta from main plugin file
	 */
	public $plugin_data = array();

	/**
	 * If plugin_data is built, this represents the version number defined the the main plugin file meta
	 *
	 * @var string $version
	 */
	public $version;

	/**
	 * Assumed path to main plugin file. Assumes your plugin folder and main plugin file are the same.
	 *
	 * @var string $plugin_file
	 */
	public $plugin_file;

	/**
	 * The slug or name stored in array WordPress uses to associate a "short path" for each plugin.
	 * Example found in site_option('active_sitewide_plugins')
	 *
	 * @var string $wp_plugin_slug
	 */
	public $wp_plugin_slug;

	/**
	 * A true or false value indicating if the plugin has been activated network wide in multisite.
	 *
	 * @var bool $is_network_active
	 */
	public $is_network_active = false;

	/**
	 * Construct the plugin object.
	 * Note that classes that extend this class should add there construction actions into onload()
	 */
	public function __construct() {

		// Hook can be used by mu plugins to modify plugin behavior after plugin is setup.
		do_action( get_called_class() . '_preface', $this );

		// configure and setup the plugin class variables.
		$this->configure_defaults();

		// Define globals used by the plugin including bloginfo.
		$this->defines_and_globals();

		// If enabled, register auto-loading to include any files in the $autoload_dir.
		if ( ! empty( static::$autoload_type ) ) {
			spl_autoload_register( array( $this, 'autoload' ) );
		}

		// Onload to do things during plugin construction.
		$this->onload( $this );

		// Most actions go into init which loads after WordPress core sets up all the defaults.
		add_action( 'init', array( $this, 'init' ) );

		// Init for use with logged in users, see this::authenticated_init for more details.
		add_action( 'init', array( $this, 'authenticated_init' ) );

		// Hook can be used by mu plugins to modify plugin behavior after plugin is setup.
		do_action( get_called_class() . '_setup', $this );

	} // END public function __construct

	/**
	 * Activated the plugin actions
	 *
	 * @return void
	 */
	public static function activate() {
	}

	/**
	 * Initialize the plugin - for admin (back end)
	 * You would expected this to be handled on action admin_init, but it does not properly handle
	 * the use case for all logged in user actions. Always keep is_user_logged_in() wrapper within
	 * this function for proper usage.
	 *
	 * @since   0.1
	 * @return  void
	 */
	abstract public function authenticated_init();

	/**
	 * Auto-load classes on demand to reduce memory consumption. Classes must have a namespace so as to resolve
	 * performance issues around auto-loading classes unrelated to current plugin.
	 *
	 * @param string $class The name of the class object.
	 */
	public function autoload( $class ) {
		$parent               = explode( '\\', get_class( $this ) );
		$class_array          = explode( '\\', $class );
		$intersect            = array_intersect_assoc( $parent, $class_array );
		$intersect_depth      = count( $intersect );
		$autoload_match_depth = static::$autoload_ns_match_depth;
		// Confirm $class is in same namespace as this autoloader
		if ( $intersect_depth >= $autoload_match_depth ) {
			$file = $this->get_file_name_from_class( $class );
			if ( 'classmap' === static::$autoload_type && is_array( $this->autoload_dir ) ) {
				foreach ( $this->autoload_dir as $dir ) {
					$this->load_file( $this->installed_dir . $dir . $file );
				}
			} else {
				$this->load_file( $this->installed_dir . $file );
			}
		}

	}

	/**
	 * Setup plugins global params.
	 */
	protected function configure_defaults() {
		$this->modules           = new \stdClass();
		$this->modules->count    = 0;
		$this->installed_dir     = static::dirname( static::$current_file, 1 );
		$this->plugin_basedir    = static::dirname( static::$current_file, 2 );
		$assumed_plugin_name     = basename( $this->plugin_basedir );
		$this->plugin_file       = $this->plugin_basedir . '/' . $assumed_plugin_name . '.php';
		$this->wp_plugin_slug    = $assumed_plugin_name . '/' . $assumed_plugin_name . '.php';

		if ( is_callable( 'is_plugin_active_for_network' ) ) {
			$this->is_network_active = is_plugin_active_for_network( $this->wp_plugin_slug );
		}

		if ( file_exists( $this->plugin_file ) ) {
			$this->installed_url = plugins_url( '/', $this->plugin_file );
			// Ensure get_plugin_data is available
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$this->plugin_data = get_plugin_data( $this->plugin_file, $markup = true, $translate = true );
			if ( is_array( $this->plugin_data ) && isset( $this->plugin_data['Version'] ) ) {
				$this->version = $this->plugin_data['Version'];
			}
		} else {
			$this->installed_url = plugins_url( '/', static::$current_file );
		}
		// Setup network url and fallback in case siteurl is not defined.
		if ( ! defined( 'WP_NETWORKURL' ) && is_multisite() ) {
			define( 'WP_NETWORKURL', network_site_url() );
		} elseif ( ! defined( 'WP_NETWORKURL' ) ) {
			define( 'WP_NETWORKURL', get_site_url() );
		}
		$this->network_url = WP_NETWORKURL;
	}

	/**
	 * Deactivated the plugin actions
	 *
	 * @return  void
	 */
	public static function deactivate() {
	}

	/**
	 * Enforce that the plugin prepare any defines or globals in a standard location.
	 *
	 * @return mixed
	 */
	abstract protected function defines_and_globals();

	/**
	 * Dirname function that mimics PHP7 dirname() enhancements so that we can enable PHP5.6 support.
	 *
	 * @param     $path
	 * @param int $count
	 *
	 * @return string
	 */
	public static function dirname($path, $count=1){
		if ($count > 1){
			return dirname(static::dirname($path, --$count));
		}else{
			return dirname($path);
		}
	}

	/**
	 * Utility function to get class name from filename if you follow this abstract plugin's naming standards
	 *
	 * @param string $file          Absolute path to file.
	 * @param string $installed_dir Absolute path to plugin folder.
	 * @param string $namespace     Namespace of calling class, if any.
	 *
	 * @return string $class_name Name of Class to load based on file path.
	 */
	public static function filepath_to_classname( $file, $installed_dir, $namespace ) {
		/**
		 * Convert path and filename into namespace and class
		 */
		$path_info        = str_ireplace( $installed_dir, '', $file );
		$path_info        = pathinfo( $path_info );
		$converted_dir    = str_replace( '/', '\\', $path_info['dirname'] );
		$converted_dir    = ucwords( $converted_dir, '_\\' );
		$filename_search  = array( static::$filename_prefix, '-' );
		$filename_replace = array( '', '_' );
		$class            = str_ireplace( $filename_search, $filename_replace, $path_info['filename'] );
		$class_name       = $namespace . $converted_dir . '\\' . ucwords( $class, '_' );

		return $class_name;
	}

	/**
	 * Used to get the instance of the class as an unforced singleton model
	 *
	 * @return bool|Abstract_Plugin|mixed $instance
	 */
	public static function get() {
		global $wp_plugins;
		$plugin_name = strtolower( get_called_class() );
		if ( isset( $wp_plugins ) && isset( $wp_plugins->$plugin_name ) ) {
			return $wp_plugins->$plugin_name;
		} else {
			return false;
		}
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param  string $class Raw class name.
	 *
	 * @return string
	 */
	private function get_file_name_from_class( $class ) {
		if ( 'classmap' === static::$autoload_type ) {
			$filtered_class_name = explode( '\\', $class );
			$class_filename      = end( $filtered_class_name );
			$class_filename      = str_replace( '_', '-', $class_filename );

			return static::$filename_prefix . $class_filename . '.php';
		} else {

			return $this->psr4_get_file_name_from_class( $class );
		}
	}

	/**
	 * Initialize the plugin - for public (front end)
	 * Example of building a module of the plugin into init
	 * ```$this->modules->FS_Mail = new FS_Mail( $this, $this->plugin_object_basedir );```
	 *
	 * @since   0.1
	 * @return  void
	 */
	abstract public function init();

	/**
	 * Initialize the plugin - for public (front end)
	 *
	 * @param mixed $instance Parent instance passed through to child.
	 *
	 * @since   0.1
	 * @return  void
	 */
	abstract public function onload( $instance );

	/**
	 * Include a class file.
	 *
	 * @param  string $path Server path to file for inclusion.
	 *
	 * @return bool successful or not
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once( $path );
			$success = true;
		}

		return ! ( empty( $success ) ) ? true : false;
	}

	/**
	 * Take a namespaced class name and turn it into a file name.
	 *
	 * @param  string $class
	 *
	 * @return string
	 */
	private function psr4_get_file_name_from_class( $class ) {
		$class = strtolower( $class );
		if ( stristr( $class, '\\' ) ) {

			// if the first item is == the collection name, trim it off
			$class = str_ireplace( static::$autoload_class_prefix, '', $class );

			// Maybe fix formatting underscores to dashes and double to single slashes.
			$class     = str_replace( array( '_', '\\' ), array( '-', '/' ), $class );
			$class     = explode( '/', $class );
			$file_name = &$class[ count( $class ) - 1 ];
			$file_name = static::$filename_prefix . $file_name . '.php';
			$file_path = join( DIRECTORY_SEPARATOR, $class );

			return $file_path;
		} else {
			return static::$filename_prefix . str_replace( '_', '-', $class ) . '.php';
		}
	}

	/**
	 * Setup special hooks that don't run after plugins_loaded action
	 *
	 * @param $file
	 */
	public static function run( $file ) {
		// Logic required for WordPress VIP plugins to load during themes function file initialization.
		if ( did_action( 'plugins_loaded' ) ) {
			add_action( 'init', array( get_called_class(), 'load' ), 1 );
		} else {
			add_action( 'plugins_loaded', array( get_called_class(), 'load' ) );
			// Installation and un-installation hooks.
			register_activation_hook( $file, array( get_called_class(), 'activate' ) );
			register_deactivation_hook( $file, array( get_called_class(), 'deactivate' ) );
			register_uninstall_hook( $file, array( get_called_class(), 'uninstall' ) );
		}
	}

	/**
	 * Build and initialize the plugin - on plugins_loaded
	 */
	public static function load() {
		self::set();
	}

	/**
	 * Used to setup the instance of the class and place in wp_plugins collection.
	 *
	 * @param bool|Abstract_Plugin|mixed $instance Contains object representing the plugin.
	 */
	private static function set( $instance = false ) {
		// Make sure the plugin hasn't already been instantiated before.
		global $wp_plugins;
		if ( ! isset( $wp_plugins ) ) {
			$wp_plugins = new \stdClass();
		}
		// Get the fully qualified parent class name and instantiate an instance of it.
		$called_class = get_called_class();
		$plugin_name  = strtolower( $called_class );
		if ( empty( $instance ) || ! is_a( $instance, $called_class ) ) {
			$wp_plugins->$plugin_name = new $called_class();
		} else {
			$wp_plugins->$plugin_name = $instance;
		}
	}

	/**
	 * Uninstall the plugin actions
	 *
	 * @return  void
	 */
	public static function uninstall() {
	}

} // END class
