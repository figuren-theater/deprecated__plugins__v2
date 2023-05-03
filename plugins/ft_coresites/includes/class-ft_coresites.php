<?php

namespace Figuren_Theater\Coresites;

use Figuren_Theater\Coresites\AdminUI as AdminUI;
use Figuren_Theater\Coresites\Shortcodes as Shortcodes;


/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://carsten-bach.de
 * @since      1.0.0
 *
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_coresites {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ft_coresites_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( \defined( 'FT_coresites_VERSION' ) ) {
			$this->version = FT_coresites_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ft_coresites';

		$this->load_dependencies();
		// $this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ft_coresites_Loader. Orchestrates the hooks of the plugin.
	 * - Ft_coresites_i18n. Defines internationalization functionality.
	 * - Ft_coresites_Admin. Defines all hooks for the admin area.
	 * - Ft_coresites_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once \plugin_dir_path( \dirname( __FILE__ ) ) . 'includes/class-ft_coresites-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		// require_once \plugin_dir_path( \dirname( __FILE__ ) ) . 'includes/class-ft_coresites-i18n.php';


		/**
		 * The class responsible for defining all the shortcodes used by this plugin
		 */
		require_once \plugin_dir_path( \dirname( __FILE__ ) ) . 'includes/class-ft_coresites-shortcodes.php';
		// auto-include all Shortcodes
		foreach ( \glob( \plugin_dir_path( \dirname( __FILE__ ) ) . 'shortcodes/*.php' ) as $file )
			require_once( $file );


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once \plugin_dir_path( \dirname( __FILE__ ) ) . 'admin/class-ft_coresites-admin.php';



		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		// require_once \plugin_dir_path( \dirname( __FILE__ ) ) . 'public/class-ft_coresites-public.php';

		$this->loader = new Ft_coresites_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ft_coresites_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	private function set_locale() {

		$plugin_i18n = new Ft_coresites_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}
	 */

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new AdminUI\Ft_coresites_Admin( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'fix_no_editor_on_posts_page', 0, 2 );
		$this->loader->add_filter( 'replace_editor', $plugin_admin, 'enable_gutenberg_editor_for_blog_page', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		// $plugin_public = new Ft_coresites_Public( $this->get_plugin_name(), $this->get_version() );

		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
#		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Shortcodes
		// Load feature-list Shortcode
		$plugin_sc_featurelist     = new Shortcodes\Ft_coresites_Shortcode__featurelist( 'ft_featurelist', $this->get_plugin_name() );
		$plugin_sc_count_posts     = new Shortcodes\Ft_coresites_Shortcode__count_posts( 'ft_cp', $this->get_plugin_name() );
	#	$plugin_sc_streams_list    = new Shortcodes\Ft_coresites_Shortcode__streams_list( 'ft_streams_list', $this->get_plugin_name() );
	#	$plugin_sc_streams_preview = new Shortcodes\Ft_coresites_Shortcode__streams_preview( 'ft_streams_preview', $this->get_plugin_name() );
		$plugin_sc_social_share    = new Shortcodes\Ft_coresites_Shortcode__social_share( 'ft_social_share', $this->get_plugin_name() );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ft_coresites_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
