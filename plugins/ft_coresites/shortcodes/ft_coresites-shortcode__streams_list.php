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
class Ft_coresites_Shortcode__streams_list extends Ft_coresites_Shortcodes {

	protected function set_required_css_and_js() {
		return array('ft_coresites-stream-list');
	}

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
/*
		// WP_Query arguments
		$args = array(
			'post_type'              => array( 'ft_stream' ),
			'post_status'            => array( 'publish' ),
			'nopaging'               => true,
			'posts_per_page'         => '-1',

			'meta_key' => 'ft_streams_start_date',
			'orderby' => 'meta_value',
			'order' => 'ASC',
			'meta_query' => array(
				array(
					'key' => 'ft_streams_start_date',
					'value' => date("Y-m-d H:i:s"), // date format error
					'compare' => '>='
				)
			 )
		);
*/
		// WP_Query arguments
		$args = array(
			'post_type'              => array( 'event' ),
			'suppress_filters'       => false,

			'post_status'            => array( 'publish' ),
			'nopaging'               => true,
			'posts_per_page'         => -1,
			'orderby'                => 'eventend',
			'order'                  => 'ASC',

#			'event_start_after'=>'now',
			'event_end_after'=>'now',


			'tax_query'=>array( array(
				 'taxonomy'=>'event-category',
				 'operator' => 'IN',
				 'field'=>'slug',
				 'terms'=>array('streams')
				 ))
		);

		// The Query
		$this->output = new \WP_Query( $args );

		// disables the loading of css and js
		if (!$this->output->found_posts)
			$this->required_css_and_js = [];
	}



	protected function render( $atts ) {

		if (!$this->output->found_posts)
			return '';

		$r = '
		<div class="ft-coresites ft-coresites--table ft-coresites--table-dcf ft-coresites--table-streams-list">
			<table class="dcf-table dcf-table-responsive dcf-table-bordered dcf-table-striped dcf-w-100%">
			<caption></caption>
			<thead>

				<tr>
					<th scope="col" data-label="Datum & Zeit">Datum & Zeit</th>
					<th scope="col" data-label="Titel">Titel</th>
					<th scope="col" data-label="Art">Art</th>
				</tr>

			</thead>
			<tbody>';
		foreach ($this->output->posts as $stream) {

			if ( \has_term( 'live', 'event-category', $stream ) ) {
				$s_type = 'Live';
			} elseif ( \has_term( 'on-demand', 'event-category', $stream ) ) {
				$s_type = 'Stream';
			}

			$s_date  = \eo_get_next_occurrence_of($stream->ID);
			if (!$s_date) {
				// already started
				$s_date  = \eo_get_current_occurrence_of($stream->ID);
			}

			$s_date_start  = $s_date['start']->format( 'c' );
			$s_date_end    = $s_date['end']->format( 'c' );
			$s_start  = \wp_date( \get_option( 'date_format' ), \strtotime( $s_date_start ) );

			if ('Live' == $s_type) {
				$s_start .= ' '.\wp_date( \get_option( 'time_format' ), \strtotime( $s_date_start ) );
			} elseif ('Stream' == $s_type) {
				$s_start .= ' – '.\wp_date( \get_option( 'date_format' ), \strtotime( $s_date_end ) );
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

			$s_data = '
						<a class="font-size-xl" href="'.$s_url.'" title="'.$s_title_attr.'">'.$s_title.'</a> 
						<br>'.$s_name;


			$s_button__class = \implode(' ', array(
				'button',
				'ft-button',
				'ft-button--stream-type',
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
			$s_button = \get_post_meta( $stream->ID, 'ft_streams_url', true );
			$s_button  = ($s_button) ? '<a class="button '.$s_button__class.'" href="'.$s_button.'">'.$s_type.'</a>' : '';

			$r .= '
			<tr>
				<td data-label="Datum & Zeit">'.$s_start.'</td>
				<td data-label="Titel">'.$s_data.'</td>
				<td data-label="Art">'.$s_button.'</td>
			</tr>';


#$r .= '<pre>'.var_dump($s_date).'</pre>';

		}

		$r .= '
			</tbody>
			</table>
		</div><!--.ft-coresites-->';


		return $r;
	}

} // Ft_coresites_Shortcode__streams_list
