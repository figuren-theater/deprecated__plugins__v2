<?php
declare(strict_types=1);

namespace ImageSourceControl\Blocks;


/**
 * Plugin Name:       Image Source Control Lite | Block
 * Plugin URI:        https://websites.fuer.figuren.theater
 * Description:       Show the Image-Sources managed via 'Image Source Control' using a block.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Carsten Bach
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       image-source-control-isc-block
 * Domain Path:       /languages
 *
 * @package           isc
 */



function i18n()
{

	// load_script_textdomain( $handle, $domain = 'default', $path = null )
}
\add_action( 'init', __NAMESPACE__.'\\i18n' );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function block_init() {


	\load_plugin_textdomain( 
		'image-source-control-isc-block', 
		false,
		dirname( \plugin_basename( __FILE__ ) ) . '/languages'
	);

	\register_block_type(
		\plugin_dir_path( __FILE__ ) . 'build',
		array(
			'render_callback' => __NAMESPACE__.'\\render_block',
		)
	);


	// kind of a duplication
	// which is needed to set the dependency


	// $script_dependencies = array('wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render');

	// \wp_register_script(
	// 	'isc-image-source-control-isc-block-editor-script',
	// 	\plugins_url( 'build/index.js', __FILE__ ),
	// 	$script_dependencies,
	// 	'0.2.0'
	// );

	\wp_set_script_translations(
		'isc-image-source-control-isc-block-editor-script',
		'image-source-control-isc-block',
		\plugin_dir_path( __FILE__ ) . 'languages'
	);

	// \do_action( 'qm/debug', '');

}
\add_action( 'init', __NAMESPACE__.'\\block_init' );




/**
 * Renders the `isc/image-source-control-isc-block` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block( $attributes, $content, $block ) {

	$shortcode = ( !empty($attributes['showAll']) ) ? '[isc_list_all]' : '[isc_list]';

	// TODO // not used at the moment, so it defaults to: div
	$tag_name         = empty( $attributes['tagName'] ) ? 'div' : $attributes['tagName'];
	
	// set text-align CSS class
	// $align_class_name = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";

	// get and merge wrapper attributes with text-align CSS class
	// $wrapper_attributes = \get_block_wrapper_attributes( [ 'class' => $align_class_name ] );
	// $wrapper_attributes = \get_block_wrapper_attributes();
	// trick to load core table-block styles
	$wrapper_attributes = \get_block_wrapper_attributes( [ 'class' => 'wp-block-table is-style-stripes' ] );

	// get and prepare html
	$output = \do_shortcode( $shortcode );

	$fallback = (\current_user_can( 'upload_files' )) ? sprintf(
		'<p class="%1$s">%2$s</p>',
		'notice notice-warning',
		__('Add some images to the post, to see their sources listed here.','image-source-control-isc-block')
	):'';

	$output = ( ! empty( $output ) ) ? $output : $fallback;

	// 
	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$output
	);
}


/*

function load_block_table_styles_for_isc_all_shortcode($output, $tag, $attr){
	//make sure it is the right shortcode
	if('isc_list_all' !== $tag)
		return $output;

	// this triggers the loading of 'table-block'
	// related scripts and styles
	$_pseudo_content = '<!-- wp:table {"className":"is-style-stripes"} --><!-- /wp:table -->';
	$_useless_var = \do_blocks( $_pseudo_content );

	return $output;
}
\add_filter('do_shortcode_tag',__NAMESPACE__.'\\load_block_table_styles_for_isc_all_shortcode', 10, 3 );
*/
