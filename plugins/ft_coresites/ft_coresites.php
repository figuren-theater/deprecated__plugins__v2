<?php
namespace Figuren_Theater\Coresites;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://carsten-bach.de
 * @since             1.0.0
 * @package           Ft_coresites
 *
 * @wordpress-plugin
 * Plugin Name:       figuren.theater coresites
 * Plugin URI:        https://figuren.theater
 * Description:       Functionality esp. shortcodes for all core-sites: figuren.theater, meta.figuren.theater, websites.fuer.figuren.theater, mein.figuren.theater
 * Version:           1.0.0
 * Author:            Carsten Bach
 * Author URI:        https://carsten-bach.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ft_coresites
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
define( 'FT_coresites_VERSION', '1.3.2' ); // until 1.3.2 this was in sync with FT_PLATTFORM_VERSION, but not needed at all :(

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ft_coresites-activator.php
 */
function activate_ft_coresites() {
	require_once \plugin_dir_path( __FILE__ ) . 'includes/class-ft_coresites-activator.php';
	Ft_coresites_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ft_coresites-deactivator.php
 */
function deactivate_ft_coresites() {
	require_once \plugin_dir_path( __FILE__ ) . 'includes/class-ft_coresites-deactivator.php';
	Ft_coresites_Deactivator::deactivate();
}

#\register_activation_hook( __FILE__, 'activate_ft_coresites' );
#\register_deactivation_hook( __FILE__, 'deactivate_ft_coresites' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require \plugin_dir_path( __FILE__ ) . 'includes/class-ft_coresites.php';


/*
require MUPLUGINDIR . '/Figuren_Theater/Psr4AutoloaderClass.php';
// instantiate the loader
$loader = new \Figuren_Theater\Psr4AutoloaderClass;
// register the autoloader
$loader->register();
// register the base directories for the namespace prefix
$loader->addNamespace( __NAMESPACE__, dirname( __FILE__ ) . '/src', true );
#$loader->addNamespace( __NAMESPACE__, dirname( __FILE__ ) . '/tests', true );

*/

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ft_coresites() {

	$plugin = new Ft_coresites();
	$plugin->run();

}
run_ft_coresites();
