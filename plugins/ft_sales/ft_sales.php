<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://carsten-bach.de/
 * @since             1.0.0
 * @package           Ft_sales
 *
 * @wordpress-plugin
 * Plugin Name:       figuren.theater SALES
 * Plugin URI:        https://websites.fuer.figuren.theater
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Carsten Bach
 * Author URI:        https://carsten-bach.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ft_sales
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FT_SALES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ft_sales-activator.php
 */
function activate_ft_sales() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ft_sales-activator.php';
	Ft_sales_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ft_sales-deactivator.php
 */
function deactivate_ft_sales() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ft_sales-deactivator.php';
	Ft_sales_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ft_sales' );
register_deactivation_hook( __FILE__, 'deactivate_ft_sales' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ft_sales.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ft_sales() {

	$plugin = new Ft_sales();
	$plugin->run();

}
run_ft_sales();
