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
class Ft_coresites_Shortcode__streams_preview extends Ft_coresites_Shortcodes {

	/**
	 * 'ui_field' is the needed field of "Shortcake - Shortcode UI" Plugin
	 * docs are available inside dev Plugin
	 * https://github.com/wp-shortcake/Shortcake/blob/master/dev.php
	 */
	protected function default_atts() {
		//
		return array();
	}



	protected function prepare_output() {

		$args = array(
			'post_type'              => 'event',
			'suppress_filters'       => false,

			'post_status'            => array( 'publish' ),
			'posts_per_page'         => 3,
			'orderby'                => 'eventend',
			'order'                  => 'ASC',

			'event_start_after'=>'now',
			'event_end_before'=>'+5 days',

			'tax_query'=>array( array(
				 'taxonomy'=>'event-category',
				 'operator' => 'IN',
				 'field'=>'slug',
				 'terms'=>array('live')
				 ))
		);

		// The Query
		$this->output = new \WP_Query( $args );

		// if we have less than 3, get some more
		if (3 > (int)$this->output->found_posts) {
			$args = array(
				'post_type'              => 'event',
				'suppress_filters'       => false,

				'post_status'            => array( 'publish' ),
				'posts_per_page'         => (3 - (int)$this->output->found_posts),
				'orderby'                => 'eventend',
				'order'                  => 'ASC',

				'event_start_before'=>'now',
				'event_end_before'=>'+5 days',

				'tax_query'=>array( array(
					 'taxonomy'=>'event-category',
					 'operator' => 'IN',
					 'field'=>'slug',
					 'terms'=>array('on-demand')
					 ))
			);

			// The Query
			$__odstreams_query = new \WP_Query( $args );
			$this->output->posts = array_merge($this->output->posts, $__odstreams_query->posts);

		}

		//
		shuffle($this->output->posts);

	}



	protected function render( $atts ) {

		$r = '
			<!-- wp:columns {"align":"wide"} -->
			<div class="wp-block-columns alignwide">';



		foreach ($this->output->posts as $stream) {

			$s_date  = \eo_get_next_occurrence_of($stream->ID);
			if (!$s_date) {
				// already started
				$s_date  = \eo_get_current_occurrence_of($stream->ID);
			}
			$__date_today = new \DateTime("now");

			$is_playing_now = false;
			if ($s_date['start'] < $__date_today && $__date_today < $s_date['end']) {
				$time_to_wait_string = __('Jetzt ansehen','ft_streams');
				$is_playing_now = true;
			}

#$r .= '<pre>'.var_dump($__date_today->format( 'd.m.Y H:i' )).'</pre>';
#$r .= '<pre>'.var_dump($s_date['start']->format( 'd.m.Y H:i' )).'</pre>';


			if ( \has_term( 'live', 'event-category', $stream ) ) {
				$s_type = 'Live';
				$time_to_wait = \human_time_diff( (int)$__date_today->format( 'U' ), (int)$s_date['start']->format( 'U' ) );
				$time_to_wait_string = \sprintf(__('in %s verfügbar','ft_streams'),$time_to_wait);
			} elseif ( \has_term( 'on-demand', 'event-category', $stream ) ) {
				$s_type = 'Stream';
				$time_to_end = \human_time_diff( (int)$__date_today->format( 'U' ), (int)$s_date['end']->format( 'U' ) );
				$time_to_wait_string = \sprintf(__('nur noch %s verfügbar','ft_streams'),$time_to_end);
			}


			$s_title = \get_the_title( $stream->ID );
			$s_title_attr = \the_title_attribute( array(
				'before' => '',
				'after'  => '',
				'echo'   => false,
				'post'   => $stream,
			) );
			// borrowed from the_permalink() function
			$s_url = \esc_url( \apply_filters( 'the_permalink', \get_permalink( $stream ), $stream ) );

			$stream_author = $stream->_ft_streams_author;
			$s_name = $stream_author['name'];
			$s_name  = ($s_name) ? \sprintf(__('von %s','ft_streams'),'<strong>'.$s_name.'</strong>') : '';

			$s_data = '<a href="'.$s_url.'" title="'.$s_title_attr.'">'.$s_title.'</a>';


			$s_button__class = \implode(' ', array(
				'button',
				'ft-button',
				'ft-button--stream-type',
				'wp-block-button__link',
			));
			switch ($s_type) {
				case 'Live':
					$s_button__class .= ' has-accent-background-color';
					$s_button__class .= ' ft-button--stream-type__live';
					# code...
					break;
				
				case 'Stream':
				default:
					$s_button__class .= ' has-primary-background-color';
					$s_button__class .= ' ft-button--stream-type__stream';
					# code...
					break;
			}

			if ($is_playing_now) {
				$s_button = \get_post_meta( $stream->ID, 'ft_streams_url', true );
			} else {
				$s_button = $s_url;
			}
			$s_button  = ($s_button) ? '<a class="button '.$s_button__class.'" href="'.$s_button.'">'.$s_type.'<p class="no-margin has-text-align-center" style="text-transform: none;font-weight: normal;"><em>'.$time_to_wait_string.'</em></p></a>' : '';


		$r .= '
			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:group -->
			<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:cover {"url":"'.\get_the_post_thumbnail_url( $stream, 'normal').'","id":143,"dimRatio":70,"focalPoint":{"x":"0.30","y":"0.10"},"minHeight":399,"customGradient":"radial-gradient(rgba(0,0,0,0) 0%,rgb(10,10,10) 80%)"} -->
			<div class="wp-block-cover has-background-dim-70 has-background-dim has-background-gradient" style="background-image:url('.\get_the_post_thumbnail_url( $stream, 'normal').');background-position:30% 10%;min-height:399px"><span aria-hidden="true" class="wp-block-cover__gradient-background" style="background:radial-gradient(rgba(0,0,0,0) 0%,rgb(10,10,10) 80%)"></span><div class="wp-block-cover__inner-container"><!-- wp:buttons {"align":"center"} -->
			<div class="wp-block-buttons aligncenter"><!-- wp:button -->
			<div class="wp-block-button">'.$s_button.'</div>
			<!-- /wp:button --></div>
			<!-- /wp:buttons -->

			</div></div>
			<!-- /wp:cover --></div></div>
			<!-- /wp:group -->

			<!-- wp:heading {"level":3} -->
			<h3>'.$s_data.'</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p>'.$s_name.'</p>
			<!-- /wp:paragraph --></div>
			<!-- /wp:column -->';





		}

		$r .= '
			</div>
			<!-- /wp:columns -->';


		return $r;
	}

} // Ft_coresites_Shortcode__streams_preview
