<?php
/**
 * Plugin Name: ft-core-featuretable — CGB Gutenberg Block Plugin
 * Plugin URI: https://github.com/ahmadawais/create-guten-block/
 * Description: ft-core-featuretable — is a Gutenberg plugin created via create-guten-block.
 * Author: mrahmadawais, maedahbatool
 * Author URI: https://AhmadAwais.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * because we depend on an old ugly "class-a-like" PLugin,
 * with no autoloader
 * we just grab this old stuff here
 * for not having to refactor the old plugin completely
 * for just having its two relevant classes visible over here.
 */
$path_to_old_plugin = plugin_dir_path( __DIR__ ) . 'ft_coresites/';
require_once $path_to_old_plugin . 'includes/class-ft_coresites-shortcodes.php';
require_once $path_to_old_plugin . 'shortcodes/ft_coresites-shortcode__featurelist.php';


/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/block_featuretable.php';
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
