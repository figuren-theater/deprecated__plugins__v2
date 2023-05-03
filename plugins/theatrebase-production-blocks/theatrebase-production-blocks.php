<?php
/**
 * Plugin Name:       Theatrebase Production Blocks
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Carsten Bach
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       theatrebase-production-blocks
 *
 * @package           theatrebase-production-blocks
 */

function theatrebase_production_blocks__init() 
{
	//
	\add_action( 'init', 'theatrebase_production_blocks__i18n', 1 );

	// 
	\add_action( 'init', 'theatrebase_production_blocks__dynamic_blocks_init', 9 );

	//
	\add_action( 'init', 'theatrebase_production_blocks__static_blocks_init' );

	// https://developer.wordpress.org/reference/hooks/enqueue_block_assets/
	#add_action( 'enqueue_block_assets', 'theatrebase_production_blocks__enqueue_assets' );
	add_action( 'enqueue_block_editor_assets', 'theatrebase_production_blocks__enqueue_assets' );

	// \do_action( 'qm/debug', '');
}
\add_action( 'init', 'theatrebase_production_blocks__init', 0 );



function theatrebase_production_blocks__i18n()
{

	\load_plugin_textdomain( 
		'theatrebase-production-blocks', 
		false,
		dirname( \plugin_basename( __FILE__ ) ) . '/languages'
	);
/*
	\wp_set_script_translations(
		'theatrebase-theatrebase-production-duration-editor-script',
		'theatrebase-production-blocks',
		\plugin_dir_path( __FILE__ ) . 'languages'
	);

	\wp_set_script_translations(
		'theatrebase-theatrebase-production-premiere-editor-script',
		'theatrebase-production-blocks',
		\plugin_dir_path( __FILE__ ) . 'languages'
	);
	*/

	$assets = theatrebase_production_blocks__get_assets();
	foreach ( $assets as $asset ) {
		theatrebase_production_blocks__register_asset( $asset );
	}
}


function theatrebase_production_blocks__static_blocks_init() {

	$static_blocks = array(
		// '...',
	);

	foreach ( $static_blocks as $block ) {
		register_block_type( plugin_dir_path( __FILE__ ) . 'src/block-editor/blocks/' . $block);
	}
}




function theatrebase_production_blocks__dynamic_blocks_init() {

	$dynamic_blocks = array(
		'duration',
		'premiere',
		'targetgroup',
	);

	foreach ( $dynamic_blocks as $block ) {
		require( plugin_dir_path( __FILE__ ) . 'build/' . $block . '/index.php' );
	}
}





function theatrebase_production_blocks__get_assets()
{
	return array(
		'document-setting-panel',
		'shadow-terms',
		'shadow-related-query',
		'subsites-query',
	);
}
function theatrebase_production_blocks__register_asset( string $asset )
{
	$dir = __DIR__;

	$script_asset_path = "$dir/build/$asset.asset.php";
	if ( ! file_exists( $script_asset_path ) ) {
		throw new Error(
			"You need to run `npm start` or `npm run build` for the '$asset' block-asset first."
		);
	}
	$index_js     = "build/$asset.js";
	$script_asset = require( $script_asset_path );

	\wp_register_script( 
		"theatrebase-production-blocks--$asset",
		plugins_url( $index_js, __FILE__ ),
		$script_asset['dependencies'],
		$script_asset['version']
	);


	\wp_set_script_translations(
		"theatrebase-production-blocks--$asset",
		'theatrebase-production-blocks',
		\plugin_dir_path( __FILE__ ) . 'languages'
	);
}



function theatrebase_production_blocks__enqueue_assets()
{
	$assets = theatrebase_production_blocks__get_assets();
	foreach ( $assets as $asset ) {
		theatrebase_production_blocks__enqueue_asset( $asset );
	}
}

function theatrebase_production_blocks__enqueue_asset( string $asset )
{

	wp_enqueue_script(
		"theatrebase-production-blocks--$asset",
#		plugins_url( $index_js, __FILE__ ),
#		$script_asset['dependencies'],
#		$script_asset['version']
	);

}


/**
 * @todo Bring this to work!!
 *
 *
 * 
 * Fires after an object's terms have been set.
 *
 * @since 2.8.0
 *
 * @param int    $object_id  Object ID.
 * @param array  $terms      An array of object term IDs or slugs.
 * @param array  $tt_ids     An array of term taxonomy IDs.
 * @param string $taxonomy   Taxonomy slug.
 * @param bool   $append     Whether to append new terms to the old terms.
 * @param array  $old_tt_ids Old array of term taxonomy IDs.
 */
#\add_action( 'set_object_terms','theatrebase_production_blocks__set_object_terms', 10, 6 ); 
function theatrebase_production_blocks__set_object_terms( 
	int    $object_id, 
	array  $terms,     
	array  $tt_ids,    
	string $taxonomy,  
	bool   $append,    
	array  $old_tt_ids
)
{

	$block_name = 'core/query';



	if ( 'ft_production_shadow' !== $taxonomy )
		return;

	$updated_post = \get_post( $object_id );

	// bail
	// if there is no query-block, 
	// which is the base of our variation
	if ( ! \has_block( $block_name, $updated_post->post_content ))
		return;

	// we are prepared, lets start!
	

	// get all used terms of this post
	$tt_ids = ( $append ) ? array_merge( $old_tt_ids, $tt_ids ) : $tt_ids;

	// get query block
	$_all_blocks = \parse_blocks( $updated_post->post_content );

	// find our variation
	// using its CSS className
	
	/**
	 * Filters a list of objects, based on a set of key => value arguments.
	 *
	 * Retrieves the objects from the list that match the given arguments.
	 * Key represents property name, and value represents property value.
	 *
	 * If an object has more properties than those specified in arguments,
	 * that will not disqualify it. When using the 'AND' operator,
	 * any missing properties will disqualify it.
	 *
	 * When using the `$field` argument, this function can also retrieve
	 * a particular field from all matching objects, whereas wp_list_filter()
	 * only does the filtering.
	 *
	 * @since 3.0.0
	 * @since 4.7.0 Uses `WP_List_Util` class.
	 *
	 * @param array       $list     An array of objects to filter.
	 * @param array       $args     Optional. An array of key => value arguments to match
	 *                              against each object. Default empty array.
	 * @param string      $operator Optional. The logical operation to perform. 'AND' means
	 *                              all elements from the array must match. 'OR' means only
	 *                              one element needs to match. 'NOT' means no elements may
	 *                              match. Default 'AND'.
	 * @param bool|string $field    Optional. A field from the object to place instead
	 *                              of the entire object. Default false.
	 * @return array A list of objects or object fields.
	 */
	$_blocks = \wp_list_filter(
		$_all_blocks, 
		[
			'blockName' => $block_name,

		]
	);


}



