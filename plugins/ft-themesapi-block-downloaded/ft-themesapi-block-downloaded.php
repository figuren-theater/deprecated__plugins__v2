<?php
/**
 * Plugin Name:       f.t | Themes API Downloaded Block
 * Description:       Show the wordpress.org download count for the given theme, loaded from post_meta.
 * Requires at least: 5.8
 * Requires PHP:      7.3
 * Version:           0.1.0
 * Author:            Carsten Bach
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ft-themesapi-block-downloaded
 *
 * @package           figurentheater
 */

namespace figurentheater\ft_themesapi_block_downloaded;

use WP_Block;

use function add_action;
use function add_post_type_support;
use function get_block_wrapper_attributes;
use function get_post_meta;
use function get_post_types_by_support;
use function load_plugin_textdomain;
use function plugin_basename;
use function plugin_dir_path;
use function register_block_type;
use function register_post_meta;
use function wp_set_script_translations;

add_action( 'init', function ()
{
	// load i18n & register block
	add_action( 'init', __NAMESPACE__ . '\\init' );

	// register post_meta
	add_action( 'init', __NAMESPACE__ . '\\register_meta', 11 );

}, 0 );



/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/
 */
function init() {

	load_plugin_textdomain( 
		'ft-themesapi-block-downloaded',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);

	register_block_type( 
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);

	// Load available translations.
	wp_set_script_translations( 
		'figurentheater-ft-themesapi-block-downloaded-editor-script',
		'ft-themesapi-block-downloaded',
		plugin_dir_path( __FILE__ ) . 'languages'
	);
}

// register custom meta tag field
function register_meta() {

	// prepare builtin post_types
	add_post_type_support( 'ft_theme', 'downloaded' );
	
	$post_types = get_post_types_by_support( 'downloaded' );

	array_walk(
		$post_types,
		function( $post_type, $i ) {
			register_post_meta( 
				$post_type,
				'downloaded',
				[
					'show_in_rest' => true,
					'single'       => true,
					'type'         => 'string',
				]
			);
		}
	);
}



/**
 * Renders the `figurentheater_ft_themesapi_block_downloaded` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string  Returns ....
 */
function render( array $attributes, string $content, WP_Block $block ) : string {
	if ( ! isset( $block->context['postId'] ) )
		return '';

	$downloaded   = get_post_meta(
		$block->context['postId'],
		'downloaded',
		true
	);

	if ( ! $downloaded )
		return '';

	// adjust formatting
	$readable_downloaded = sprintf(
		'Diese Vorlage wurde <strong>%s</strong> mal verwendet.',
		\number_format_i18n( floatval( $downloaded ) )
	);

	$align_class_name = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";

	// default, so far
	// as long as there where no tag-selector
	$tag_name = 'h2';

	if ( isset( $attributes['level'] ) ) {
		$tag_name = 0 === $attributes['level'] ? 'p' : 'h' . $attributes['level'];
	}

	$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $align_class_name ] );

	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$readable_downloaded
	);
}
