<?php
/**
 * Plugin Name:       Theme Demo Link
 * Plugin URI:        https://websites.fuer.figuren.theater
 * Description:       Shows a link to the live demo of this theme.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            carstenbach
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ft-theme-demo-link
 * Domain Path:       /languages
 * Update URI:        https://github.com/figuren-theater/ft-theme-demo-link
 *
 * @package           figurentheater
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function figurentheater_ft_theme_demo_link_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'figurentheater_ft_theme_demo_link_block_init' );
