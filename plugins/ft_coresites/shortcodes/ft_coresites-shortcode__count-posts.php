<?php
namespace Figuren_Theater\Coresites\Shortcodes;

/**
 * Define the Shortcodes used by this plugin
 *
 *
 * @link       https://carsten-bach.de
 * @since      1.0.0
 *
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 */

/**
 * Display the total number of published posts (all post_types) using the shortcode [ft_cp]
 *
 * @since      1.0.0
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 * @author     Carsten Bach <mail+theatre_base@carsten-bach.de>
 */
class Ft_coresites_Shortcode__count_posts extends Ft_coresites_Shortcodes {

	/**
	 * 'ui_field' is the needed field of "Shortcake - Shortcode UI" Plugin
	 * docs are available inside dev Plugin
	 * https://github.com/wp-shortcake/Shortcake/blob/master/dev.php
	 */
	protected function default_atts() {
		//
		return array(

			array(
				'param' => 'type',
				'default' => 'ft_feature',
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
			array(
				'param' => 'taxonomy',
				'default' => 'category',
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
			array(
				'param' => 'term',
				'default' => null,
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
			array(
				'param' => 'blog_id',
				'default' => 5 // websites.fuer.figuren.(theater|test)
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
		);
	}



	protected function prepare_output() {

		$_current_blog_id = \get_current_blog_id();

		//
		if ($_current_blog_id !== (int)$this->atts['blog_id'])
			\switch_to_blog( $this->atts['blog_id'] );

		// v2
		$query_args = array(
			'post_type' => $this->atts['type']
		);

		if (!empty($this->atts['term'])) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $this->atts['taxonomy'],
					'field' => 'slug',
					'terms' => $this->atts['term']
				)
			);
		}

		$the_query = new \WP_Query( $query_args );
		$tp = $the_query->found_posts;

		//
		if ($_current_blog_id !== (int)$this->atts['blog_id'])
			\restore_current_blog();

		$this->output = $tp;
	}


} // Ft_coresites_Shortcode__featurelist
