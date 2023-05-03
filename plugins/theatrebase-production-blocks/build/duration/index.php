<?php
declare(strict_types=1);

namespace TheatreBase\Production\Duration;

use Figuren_Theater\Network\Post_Types;



/**
 * Plugin Name:       TheatreBase Production Duration
 * Description:       Show Duration Date of production.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.2.0
 * Author:            Carsten Bach
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       theatrebase-production-blocks
 *
 * @package           theatrebase
 */


const DURATION_META = '_theatre_base_prod_and_event__duration';

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/
 */
function block_init() {
	\register_block_type(
		__DIR__,
		array(
			'render_callback' => __NAMESPACE__.'\\render_block',
		)
	);

	// register custom meta tag field
	// $post_types = [ THEATRE_BASE_PRODUCTION_CPT, THEATRE_BASE_EVENT_CPT ];
	$post_types = [ 
		Post_Types\Post_Type__ft_production::NAME,
		'post'
	];
	array_map(
		function( $post_type ) {
			\register_post_meta(
				$post_type,
				DURATION_META,
				[
					'show_in_rest' => true,
					'single' => true,
					'type' => 'integer',
					'auth_callback' => function() {
						return \current_user_can('edit_posts');
					},
				]
			);
		},
		$post_types
	);

	$block = 'duration';
	\wp_set_script_translations(
		"theatrebase-theatrebase-production-$block-editor-script",
		// 'theatrebase-theatrebase-production-targetgroup-editor-script',
		'theatrebase-production-blocks',
		\plugin_dir_path( dirname((dirname(__FILE__)) ) ) . 'languages'
	);

}
\add_action( 'init', __NAMESPACE__.'\\block_init' );




/**
 * Renders the `theatrebase-production-duration` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) ) {
		return '';
	}

	$post_ID = $block->context['postId'];

	// using the '$attributes['metaFieldValue']' helps the <ServerSideRenderer> 
	// to show up-to-date results
	// otherwise it would use the saved post_meta 
	// which could be not correct during the edit process
	$duration   = ( isset( $_GET['metaFieldValue'] ) && ! empty( $_GET['metaFieldValue'] )) ? $_GET['metaFieldValue'] : \get_post_meta(
		$post_ID,
		DURATION_META,
		true
	);

	// return early
	if ( ! $duration )
		return '';

	// show human readable version
	// of given duration
	if ( ! empty( $attributes['humanReadable'] )) {

		// get "1 hour 20 minutes 20 seconds"
		// from wp, based on our given "123" minutes
		$duration_readable = \human_readable_duration( 
			\gmdate(
				'H:i:s',
				$duration * 60
			)
		);
		// cut sentence into parts
		$duration_parts = explode(', ', $duration_readable);

		// remove seconds, as this is useless
		array_pop( $duration_parts );

		// remove "00 minutes", as this might be useless
		if ( is_int( ( intval( $duration ) / 60 ) ) )
			array_pop( $duration_parts );

		// if less than 60 minutes
		// remove '00 hours'
		if ( 60 > intval( $duration ) ){
			$duration_parts = array_reverse( $duration_parts );
			array_pop( $duration_parts );
		}

		// re-glue everything
		$duration = implode(' ', $duration_parts );
	}

	// TODO // not used at the moment, so it defaults to: div
	$tag_name         = empty( $attributes['tagName'] ) ? 'div' : $attributes['tagName'];
	
	// set text-align CSS class
	$align_class_name = empty( $attributes['textAlign'] ) ? '' : "has-text-align-{$attributes['textAlign']}";

	// get and merge wrapper attributes with text-align CSS class
	$wrapper_attributes = \get_block_wrapper_attributes( [ 'class' => $align_class_name ] );

	// get and prepare html
	$prefix   = empty( $attributes['prefix'] ) ? '' : '<span>'. $attributes['prefix'] . '</span> ';
	$suffix   = empty( $attributes['suffix'] ) ? '' : ' <span>'. $attributes['suffix'] . '</span>';
	$duration = '<span>'. $duration . '</span>';

	// 
	return sprintf(
		'<%1$s %2$s>%3$s</%1$s>',
		$tag_name,
		$wrapper_attributes,
		$prefix . $duration . $suffix
	);
}
