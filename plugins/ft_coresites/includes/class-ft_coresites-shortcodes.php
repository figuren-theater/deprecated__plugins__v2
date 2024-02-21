<?php

namespace Figuren_Theater\Coresites\Shortcodes;

/**
 * Define the Shortcodes used by this plugin
 *
 *
 * @link       https://carsten-bach.de
 * @since      1.0.0
 *
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 */

/**
 * Define the Shortcodes used by this plugin
 *
 * @since      1.0.0
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 * @author     Carsten Bach <mail+theatre_base@carsten-bach.de>
 */
class Ft_coresites_Shortcodes {


	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;


	/**
	 * Another unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $prefix    The string used to uniquely identify this plugin.
	 */
	protected $prefix;


	/**
	 * Shortcode Attributes of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $atts    Shortcode Attributes used to handle the shortcode.
	 */
	protected $atts;


	/**
	 * Shortcode Name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $name    Shortcode Name used to handle the shortcode.
	 */
	protected $name;


	/**
	 * Needed CSS and JS files registered via wp_regis...
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $name    list of handles
	 */
	protected $required_css_and_js;

	protected $output;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $plugin_name ) {

		$this->output = '';


		$this->plugin_name = $plugin_name;
		$this->prefix = '_' . $this->plugin_name . '_';
		$this->required_css_and_js = $this->set_required_css_and_js();

		$this->name = $name;

		\add_shortcode( $this->name, array($this, 'handler') );
#		add_action( 'register_shortcode_ui', array($this, 'register_shortcode_ui' ) );
	}


	protected function set_required_css_and_js() {
		return array();
	}

	protected function default_atts() {
		return array(
			array(
				'param' => '',
				'default' => '',
				'desc' => '',
				'ui_field' => '',
			),
		);
	}


	private function atts() {

		return \wp_list_pluck( $this->default_atts(), 'default', 'param' );
	}



	public function handler( $atts ) {

		//
		$this->atts = \shortcode_atts( $this->atts(), $atts, $this->name );

		//
		$this->prepare_output();

		if ( false === $this->output )
			return;

		$this->load_css_and_js();

		// because this is a shortcode, only return results
		return $this->render( $this->atts );
	}



	protected function prepare_output() {
		$this->output = 'ein beispielhafter versuch ### PARENT ### HO ho ho';
	}



	protected function render( $atts ) {
		return $this->output;
	}



	protected function load_css_and_js() {
		foreach ($this->required_css_and_js as $required_css_and_js__key) {
			\wp_enqueue_style( $required_css_and_js__key );
		}
	}



	/**
	 * Shortcode UI setup for the shortcake_dev shortcode.
	 *
	 * It is called when the Shortcake action hook `register_shortcode_ui` is called.
	 *
	 * This example shortcode has many editable attributes, and more complex UI.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode_ui() {
		/*
		 * Define the UI for attributes of the shortcode. Optional.
		 *
		 * In this demo example, we register multiple fields related to showing a quotation
		 * - Attachment, Citation Source, Select Page, Background Color, Alignment and Year.
		 *
		 * If no UI is registered for an attribute, then the attribute will
		 * not be editable through Shortcake's UI. However, the value of any
		 * unregistered attributes will be preserved when editing.
		 *
		 * Each array must include 'attr', 'type', and 'label'.
		 * * 'attr' should be the name of the attribute.
		 * * 'type' options include: text, checkbox, textarea, radio, select, email,
		 *     url, number, and date, post_select, attachment, color.
		 * * 'label' is the label text associated with that input field.
		 *
		 * Use 'meta' to add arbitrary attributes to the HTML of the field.
		 *
		 * Use 'encode' to encode attribute data. Requires customization in shortcode callback to decode.
		 *
		 * Depending on 'type', additional arguments may be available.
		 */
		$fields = \wp_list_pluck( $this->default_atts(), 'ui_field' );

		/*
		 * Define the Shortcode UI arguments.
		 */
		$shortcode_ui_args = array(
			/*
			 * How the shortcode should be labeled in the UI. Required argument.
			 */
#			'label' => esc_html__( 'References', 'shortcode-ui-example', 'theatre_base' ),
			'label' => \ucfirst( $this->name ),

			/*
			 * Include an icon with your shortcode. Optional.
			 * Use a dashicon, or full HTML (e.g. <img src="/path/to/your/icon" />).
			 */
			'listItemImage' => 'dashicons-awards',

			/*
			 * Limit this shortcode UI to specific posts. Optional.
			 */
#			'post_type' => array( 'post' ),

			/*
			 * Register UI for the "inner content" of the shortcode. Optional.
			 * If no UI is registered for the inner content, then any inner content
			 * data present will be backed-up during editing.
			'inner_content' => array(
				'label'        => esc_html__( 'Quote', 'shortcode-ui-example', 'theatre_base' ),
				'description'  => esc_html__( 'Include a statement from someone famous.', 'shortcode-ui-example', 'theatre_base' ),
			),
			 */

			/*
			 * Define the UI for attributes of the shortcode. Optional.
			 *
			 * See above, to where the the assignment to the $fields variable was made.
			 */
			'attrs' => $fields,
		);
		\shortcode_ui_register_for_shortcode( $this->name, $shortcode_ui_args );
	}


} // Ft_coresites_Shortcodes
