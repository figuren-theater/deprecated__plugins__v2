<?php
namespace Figuren_Theater\Coresites\Shortcodes;

use Figuren_Theater;

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
class Ft_coresites_Shortcode__featurelist extends Ft_coresites_Shortcodes {

	protected function set_required_css_and_js() {
		return array('ft_coresites-dcf');
	}

	/**
	 * 'ui_field' is the needed field of "Shortcake - Shortcode UI" Plugin
	 * docs are available inside dev Plugin
	 * https://github.com/wp-shortcake/Shortcake/blob/master/dev.php
	 */
	protected function default_atts() {
		//
		return array(
			array(
				'param' => 'taxonomy',
				'default' => 'category',
				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
			array(
				'param' => 'term',
				'default' => null,
				'desc' => '',
#				'ui_field' => array(), // TODO // temp disabled because shortcake isn't used yet
			),
		);
	}



	protected function prepare_output() {

		$_term_id_or_slug = is_numeric($this->atts['term']);
		$field_to_query_from = ( $_term_id_or_slug ) ? 'id' : 'slug';

		// WP_Query arguments
		$args = array(
			'post_type'              => array( 'ft_feature' ),
			'post_status'            => array( 'publish' ),
			'nopaging'               => true,
			'posts_per_page'         => '-1',
			'order'                  => 'ASC',
			'orderby'                => 'menu_order'
		);

		//
		if ($this->atts['taxonomy'] && $this->atts['term']) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $this->atts['taxonomy'],
					'field'    => $field_to_query_from,
					'terms'    => $this->atts['term'],
				)
			);
		}

		// The Query
		$this->output = new \WP_Query( $args );

		//
		if ($this->atts['taxonomy'] && $this->atts['term']) {
			$this->output->tax = \get_term_by( $field_to_query_from, $this->atts['term'], $this->atts['taxonomy'] );
		}

		// get all products
		$this->output->products = \get_terms( array(
			'taxonomy' => 'ft_product',
			'hide_empty' => false,
			'orderby' => 'count',
		) );


	}


	protected function render( $atts ) {
#var_export($this->output);
#		if (!is_array($this->output) || 0 == $this->output->post_count)
#			return;


		$r = '
			<table class="dcf-table dcf-table-responsive dcf-table-bordered dcf-table-striped dcf-w-100%">
			<!--caption>All FEATURES</caption-->
			<thead>

				  <tr>
						<th scope="col" data-label="TOPIC">TOPIC</th>
						<th scope="col" data-label="FEATURE">FEATURE</th>';

		foreach ($this->output->products as $product) {
			$p_title 		= $product->name;

#DISABLED			$p_title_attr 	= sprintf(__('Show Details of \'%s\' Feature.','ft_SALES'),$p_title);
#DISABLED			$p_url 			= get_term_link( $product );
#DISABLED			$p_link = '<a href="'.$p_url.'" title="'.$p_title_attr.'">'.$p_title.'</a>';
$p_link = $p_title;

			$r .= '
						<th scope="col" data-label="'.$p_title.'">'.$p_link.'</th>';
		}

		$r .= '
						<th scope="col" data-label="Milestone">Milestone</th>
				   </tr>

			</thead>
			<tbody>';


		foreach ($this->output->posts as $index => $feature) {
			$data  = '';

			if (0 == $index) {
				$data .= '
					<th rowspan="'.$this->output->post_count.'" scope="row" data-label="TOPIC">'.$this->output->tax->name.'</th>';
			}
			$f_title 		= \get_the_title( $feature->ID );

			$f_content 		= \get_the_content( null, false, $feature->ID );
			if ($f_content) {
				$f_title_attr 	= \sprintf(__('Show Details of \'%s\' Feature.','ft_SALES'),$f_title);
				$f_url 			= \get_the_permalink( $feature->ID );

				$f_link = '<a href="'.$f_url.'" title="'.$f_title_attr.'">'.$f_title.'</a>';
			} else {
				$f_link = $f_title;
			}

			if (\has_excerpt( $feature->ID )) {
				$f_excerpt = \get_the_excerpt( $feature->ID );
				$f_data = '
							<details>
								<summary>'.$f_link.'</summary>
								<p>'.$f_excerpt.'</p>
							</details>
				';
			} else {
				$f_data = $f_link;
			}

			// get milestone data
			$__has_reached_ms = false;
			$ms_obj_list = \get_the_terms( $feature->ID, 'ft_milestone' );
			if (false !== $ms_obj_list) {

				$ms_ = $ms_obj_list[0];

				// actually released
				if (\version_compare($ms_->name, Figuren_Theater\get_platform_version(), '<=')) {
					$ms_title 		= $ms_->name;
					$ms_title_attr 	= \sprintf(__('Show all Features of milestone \'%s\'.','ft_SALES'),$ms_title);
					$ms_url 		= \get_term_link( $ms_ );

					$ms_data = '<a href="'.$ms_url.'" title="'.$ms_title_attr.'">'.$ms_title.'</a>';
					$__has_reached_ms = true;

				// planned for the future
				} else {
					$ms_data = $ms_->name;
				}

			// no milkestones set
			} else {
				$ms_data = '';
			}

			// show availability of feature by using twentytwenty default CSS class
			$__has_reached_ms = ($__has_reached_ms) ? '' : 'opacity-40';

			$data .= '
					<td data-label="FEATURE" class="'.$__has_reached_ms.'">'.$f_data.'</td>';

			foreach ($this->output->products as $product) {
/*
var_export($feature);
?>
<script>
	console.log(<?php echo json_encode($feature); ?>);
	console.log(<?php echo json_encode($product); ?>);
</script>
<?php
*/
				$data .= '
					<td data-label="'.$product->name.'" class="'.$__has_reached_ms.'">'.(( \has_term( $product->term_id, 'ft_product', $feature->ID ) )?'✅':'❌').'</td>';
			}



			// re-use early prepared milestone data
			$data .= '
				<td data-label="Milestone" class="'.$__has_reached_ms.'">'.$ms_data.'</td>
			';

			$r .= '<tr>'.$data.'</tr>';
		}

		$r .= '
			</tbody>
			</table>';

		return $r;
	}

} // Ft_coresites_Shortcode__featurelist
