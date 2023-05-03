<?php

namespace Figuren_Theater\Coresites\Blocks;

/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function core_featuretable_ft_block_assets() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	\wp_register_style(
		'core_featuretable-ft-style-css', // Handle.
		\plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		\is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);
	\wp_enqueue_style( 'core_featuretable-ft-style-css' );


	// Register block editor script for backend.
	\wp_register_script(
		'core_featuretable-ft-block-js', // Handle.
		\plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	\wp_register_style(
		'core_featuretable-ft-block-editor-css', // Handle.
		\plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		// array( 'wp-edit-blocks', 'ft_coresites-dcf' ), // Dependency to include the CSS after it.
		null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	\wp_localize_script(
		'core_featuretable-ft-block-js',
		'cgbGlobal', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' => \plugin_dir_path( __DIR__ ),
			'pluginDirUrl'  => \plugin_dir_url( __DIR__ ),
			// Add more data here that you want to access from `cgbGlobal` object.
		]
	);

	// lets re-use the old shortcode handler as
	// serverSideRender of our block
	$_shortcode_handler = new block_featuretable;

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	\register_block_type(
		'ft/block-core-featuretable', array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style'         => 'core_featuretable-ft-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'core_featuretable-ft-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'core_featuretable-ft-block-editor-css',
			'render_callback' => [$_shortcode_handler,'handler'],
			'attributes' => [
				'availableTerms' => array(
					'type' => 'object',
				),
				'taxonomy' => array(
					'type' => 'string',
	#				'param' => 'taxonomy',
					'default' => 'category',
	#				'desc' => '',
	#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
				),
				'term' => array(
					'type' => 'string',
	#				'param' => 'term',
#					'default' => 'design',
	#				'desc' => '',
	#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
				),
				'align' => array(
					'type' => 'string',
				),
				'className' => array(
					'type' => 'string',
				),
				'backgroundColor' => array(
					'type' => 'string',
				),
				'textColor' => array(
					'type' => 'string',
				),
				'gradient' => array(
					'type' => 'string',
				)
			],
		),
	);
}

// Hook: Block assets.
\add_action( 'init', __NAMESPACE__ . '\\core_featuretable_ft_block_assets' );
