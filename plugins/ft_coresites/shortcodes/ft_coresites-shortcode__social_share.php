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
 * Define the Shortcodes used by this plugin
 *
 * @since      1.0.0
 * @package    Ft_coresites
 * @subpackage Ft_coresites/includes
 * @author     Carsten Bach <mail+theatre_base@carsten-bach.de>
 */
class Ft_coresites_Shortcode__social_share extends Ft_coresites_Shortcodes {

	/**
	 * 'ui_field' is the needed field of "Shortcake - Shortcode UI" Plugin
	 * docs are available inside dev Plugin
	 * https://github.com/wp-shortcake/Shortcake/blob/master/dev.php
	 */
	protected function default_atts() {
		//
		return array(
			array(
				'param' => 'plattforms',
				'default' => join(',',array(
					'facebook',
					'twitter',
#					'instagram',
#					'whatsapp',
#					'linkedin'
				)),
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
			array(
				'param' => 'url',
				'default' => \get_permalink(),
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
			array(
				'param' => 'title',
				'default' => \get_the_title(),
#				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
		);
	}



	protected function prepare_output() {

		// Get current page URL.
		$share_url = \urlencode($this->atts['url']);

		// Get current page title.
		$share_title = \htmlspecialchars(urlencode(\html_entity_decode($this->atts['title'], ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');

		// Get Post Thumbnail for pinterest
#		$share_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

		// Create Array with Social Sharing links.
		# https://gist.github.com/delucks1/083f03128598c44e5620
		# https://crunchify.com/list-of-all-social-sharing-urls-for-handy-reference-social-media-sharing-buttons-without-javascript/
		# https://css-tricks.com/simple-social-sharing-links/
		$share_links = array(
			'facebook' => array(
				'url'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url . '&t=' . $share_title,
				'text' => 'Facebook',
			),
			'twitter' => array(
				'url'  => 'https://twitter.com/intent/tweet?text=' . $share_title . '&url=' . $share_url,
				'text' => 'Twitter',
			),
#			'' => array(
#				'url'  => '' . $share_url . '' . $share_title,
#				'text' => '',
#			)
		);

		foreach (\explode(',', $this->atts['plattforms']) as $plattform) {
			$this->output .= \sprintf( '<li><a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s%3$s</a></li>',
				\esc_url( $share_links[$plattform]['url'] ),
				$this->get_icon( $share_links[$plattform]['url'] ),
				\esc_html( $share_links[$plattform]['text'] )
			);

		}

		$this->output = \sprintf('<ul style="list-style:none" class="reset-list-style social-icons">%s</ul>',$this->output);
	}


#	protected function render( $atts ) {
#		return $this->output;
#	}

	private function get_icon($url) {
		$svg = '';
		if (\class_exists('TwentyTwenty_SVG_Icons')&&\function_exists('twentytwenty_get_theme_svg')) {
			$svg = \TwentyTwenty_SVG_Icons::get_social_link_svg( $url );
			if ( empty( $svg ) ) {
				$svg = \twentytwenty_get_theme_svg( 'link' );
			}
		}
		return $svg;
	}

} // Ft_coresites_Shortcode__social_share
