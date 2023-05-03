<?php

namespace Figuren_Theater\Coresites\AdminUI;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://carsten-bach.de
 * @since      1.0.0
 *
 * @package    Ft_coresites
 * @subpackage Ft_coresites/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ft_coresites
 * @subpackage Ft_coresites/admin
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_coresites_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ft_coresites_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ft_coresites_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		\wp_enqueue_style( $this->plugin_name, \plugin_dir_url( __FILE__ ) . 'css/ft_coresites-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ft_coresites_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ft_coresites_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		\wp_enqueue_script( $this->plugin_name, \plugin_dir_url( __FILE__ ) . 'dist/ft_coresites-admin.js', array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-polyfill', 'wp-primitives'), $this->version, true );
	}

	public function fix_no_editor_on_posts_page( $post_type, $post ) {
		if( isset( $post ) && $post->ID != \get_option('page_for_posts') ) {
			return;
		}

		\remove_action( 'edit_form_after_title', '_wp_posts_page_notice' );
		\add_post_type_support( 'page', 'editor' );
	}


	/**
	 * Simulate non-empty content to enable Gutenberg editor
	 *
	 * @param bool    $replace Whether to replace the editor.
	 * @param WP_Post $post    Post object.
	 * @return bool
	 */
	public function enable_gutenberg_editor_for_blog_page( $replace, $post ) {

		if ( ! $replace && absint( \get_option( 'page_for_posts' ) ) === $post->ID && empty( $post->post_content ) ) {
			// This comment will be removed by Gutenberg since it won't parse into block.
			$post->post_content = '<!--non-empty-content-->';
		}

		return $replace;
	}

}
