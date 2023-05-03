<?php
/**
 * Main Plugin File for the 'theatrebase-production-subsites'
 * and all related stuff
 *
 * @package theatrebase-production-subsites
 * @version 0.1.0
 * @author  Carsten Bach
 */

declare(strict_types=1);

namespace TheatreBase\Production\Subsites;

use Figuren_Theater\inc\EventManager;
use Figuren_Theater\Network\Post_Types;


/**
 * Plugin Name:     TheatreBase Production Subsites
 * Plugin URI:      https://figuren.theater/
 * Description:     Enables the 'production-subsite' PT, which can be added to parent 'production' PT posts.
 * Author:          Carsten Bach
 * Author URI:      https://carsten-bach.de
 * Text Domain:     theatrebase-production-subsites
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Theatrebase_Production_Subsites
 */


/**
 * This class handles all the major use-cases for ...
 */
class Management implements EventManager\SubscriberInterface
{


	/**
	 * Returns an array of hooks that this subscriber wants to register with
	 * the WordPress plugin API.
	 *
	 * @return array
	 */
	public static function get_subscribed_events() : Array
	{
		return array(

			'plugins_loaded' => 'enable',
			// 'admin_menu'     => 'debug',
			
		);
	}



	public function enable() : void
	{

		// load i18n
		\load_plugin_textdomain( 
			'theatrebase-production-subsites', 
			false,
			dirname( \plugin_basename( __FILE__ ) ) . '/languages'
		);

		
		// register new Post Type
		\Figuren_Theater\API::get('PT')->add(
			Post_Types\Post_Type__tb_prod_subsite::NAME,
			Post_Types\Post_Type__tb_prod_subsite::get_instance()
		);


		// 
		\Figuren_Theater\FT::site()->EventManager->add_subscriber( 
			new Post_Types\Post_TypesTemplateLoader( 
				[
					'blank.php' => _x('Blank', 'Template Title', 'theatrebase-production-subsites')
				], 
				\plugin_dir_path( __FILE__ ) . 'templates/', 
				Post_Types\Post_Type__tb_prod_subsite::NAME 
			) 
		);
		// 
		\Figuren_Theater\FT::site()->EventManager->add_subscriber( 
			new Post_Types\Post_TypesTemplateLoader( 
				[
					'blank.php' => _x('Blank', 'Template Title', 'theatrebase-production-subsites')
				], 
				\plugin_dir_path( __FILE__ ) . 'templates/', 
				Post_Types\Post_Type__ft_production::NAME 
			) 
		);


	}

	

	public function enable__on_admin() : void {}






	public static function init()
	{
		static $instance;

		if ( NULL === $instance ) {
			$instance = new self;
		}

		return $instance;
	}

	public function debug()
	{

		#		\do_action( 'qm/debug', $this->get_urls() );
		
		#		\do_action( 'qm/info', '{fn}: {value}', [
		#		    'fn' => "get_taxonomy( 'link_category' )",
		#		    'value' => var_export( \get_taxonomy( 'link_category' ), true ),
		#		] );
		#
		
		#		\do_action( 'qm/info', '{fn}: {value}', [
		#		    'fn' => "\get_post_type( 'link' )",
		#		    'value' => var_export( \get_post_type( 'link' ), true ),
		#		] );

	}
}



// instantiate the loader
$loader = new \Figuren_Theater\Psr4AutoloaderClass;
// register the autoloader
$loader->register();
// register the base directories for the namespace prefix
$loader->addNamespace( 'Figuren_Theater', dirname( __FILE__ ) . '/inc', true );





// $management = new Management;
$management = Management::init();

// // 7.4. Register the Manager to our site
// if ( ! is_a( \Figuren_Theater\FT::site()->EventManager, 'EventManager' ))
// 	return;

// if ( ! method_exists( \Figuren_Theater\FT::site()->EventManager, 'add_subscriber'))
// 	return;

\Figuren_Theater\FT::site()->EventManager->add_subscriber( $management );

