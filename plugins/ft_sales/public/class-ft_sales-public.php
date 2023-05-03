<?php

# TODO # nothing STRICT here

use Figuren_Theater\Coresites\Post_Types;


/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://carsten-bach.de/
 * @since      1.0.0
 *
 * @package    Ft_sales
 * @subpackage Ft_sales/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ft_sales
 * @subpackage Ft_sales/public
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_sales_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ft_sales_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ft_sales_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		\wp_enqueue_style( $this->plugin_name, \plugin_dir_url( __FILE__ ) . 'css/ft_sales-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ft_sales_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ft_sales_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		\wp_enqueue_script( $this->plugin_name, \plugin_dir_url( __FILE__ ) . 'js/ft_sales-public.js', array( 'jquery' ), $this->version, false );

	}


	public function ft_disable_author_at_twentytwenty_post_meta(){


		\add_filter('twentytwenty_post_meta_location_single_top', function ($metas) {

			if ( Post_Types\Post_Type__ft_feature::NAME !== \get_post_type() )
				return $metas;

			return array(
				// 'author',
				// 'post-date',
				// 'comments',
				'sticky',
			);
		} );
	}
}
