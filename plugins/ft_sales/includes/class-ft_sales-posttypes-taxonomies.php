<?php

/**
 * Define all custom post_types and custom taxonomies
 *
 * ....
 *
 * @link       http://carsten-bach.de
 * @since      2020.02.27
 *
 * @package    Ft_sales
 * @subpackage Ft_sales/includes
 */

/**
 * ....
 *
 * ....
 *
 * @since      2020.02.27
 * @package    Ft_sales
 * @subpackage Ft_sales/includes
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_sales_Posttypes_and_Taxonomies {

	/**
	 * Register "Productions" custom post type
	 *
	 * @since 	2020.02.27
	 * @access 	public
	 * @uses 	register_post_type()
	 */
	public static function ft_features_post_type() {

	$labels = array(
		'name'                  => _x( 'Features', 'Post Type General Name', 'ft_SALES' ),
		'singular_name'         => _x( 'Feature', 'Post Type Singular Name', 'ft_SALES' ),
		'menu_name'             => __( 'Features', 'ft_SALES' ),
		'name_admin_bar'        => __( 'Feature', 'ft_SALES' ),
		'archives'              => __( 'Feature Archives', 'ft_SALES' ),
		'attributes'            => __( 'Feature Attributes', 'ft_SALES' ),
		'parent_item_colon'     => __( 'Parent Feature:', 'ft_SALES' ),
		'all_items'             => __( 'All Features', 'ft_SALES' ),
		'add_new_item'          => __( 'Add New Feature', 'ft_SALES' ),
		'add_new'               => __( 'Add New', 'ft_SALES' ),
		'new_item'              => __( 'New Feature', 'ft_SALES' ),
		'edit_item'             => __( 'Edit Feature', 'ft_SALES' ),
		'update_item'           => __( 'Update Feature', 'ft_SALES' ),
		'view_item'             => __( 'View Feature', 'ft_SALES' ),
		'view_items'            => __( 'ViewFeatures', 'ft_SALES' ),
		'search_items'          => __( 'Search Feature', 'ft_SALES' ),
		'not_found'             => __( 'Not found', 'ft_SALES' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'ft_SALES' ),
		'featured_image'        => __( 'Featured Image', 'ft_SALES' ),
		'set_featured_image'    => __( 'Set featured image', 'ft_SALES' ),
		'remove_featured_image' => __( 'Remove featured image', 'ft_SALES' ),
		'use_featured_image'    => __( 'Use as featured image', 'ft_SALES' ),
		'insert_into_item'      => __( 'Insert into Feature', 'ft_SALES' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Feature', 'ft_SALES' ),
		'items_list'            => __( 'Features list', 'ft_SALES' ),
		'items_list_navigation' => __( 'Features list navigation', 'ft_SALES' ),
		'filter_items_list'     => __( 'Filter Features list', 'ft_SALES' ),
	);
	$rewrite = array(
		'slug'                  => 'features',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Feature', 'ft_SALES' ),
		'description'           => __( 'All features of websites.fuer.figuren.theater', 'ft_SALES' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'excerpt', 'editor', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes' ),
		'taxonomies'            => array( 'ft_product', 'ft_milestone','post_tag','category' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-forms',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'features',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'page',
		'show_in_rest'          => true,
	);
	register_post_type( 'ft_feature', $args );

}




	/**
	 * Feature update messages.
	 *
	 * See /wp-admin/edit-form-advanced.php
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */

	public function ft_features_updated_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
		
		$messages['ft_features'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Feature updated.', 'ft_SALES' ),
			2  => __( 'Custom field updated.', 'ft_SALES' ),
			3  => __( 'Custom field deleted.', 'ft_SALES'),
			4  => __( 'Feature updated.', 'ft_SALES' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Feature restored to revision from %s', 'ft_SALES' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Feature published.', 'ft_SALES' ),
			7  => __( 'Feature saved.', 'ft_SALES' ),
			8  => __( 'Feature submitted.', 'ft_SALES' ),
			9  => sprintf(
				__( 'Feature scheduled for: <strong>%1$s</strong>.', 'ft_SALES' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'ft_SALES' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Feature draft updated.', 'ft_SALES' )
		);

			//you can also access items this way
			// $messages['post'][1] = "I just totally changed the Updated messages for standards posts";

			//return the new messaging 
		return $messages;
	}

	// Register Custom Taxonomy
	public function ft_milestone_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Milestones', 'Taxonomy General Name', 'ft_SALES' ),
			'singular_name'              => _x( 'Milestone', 'Taxonomy Singular Name', 'ft_SALES' ),
			'menu_name'                  => __( 'Milestone', 'ft_SALES' ),
			'all_items'                  => __( 'All Milestones', 'ft_SALES' ),
			'parent_item'                => __( 'Parent Milestone', 'ft_SALES' ),
			'parent_item_colon'          => __( 'Parent Milestone:', 'ft_SALES' ),
			'new_item_name'              => __( 'New Milestone Name', 'ft_SALES' ),
			'add_new_item'               => __( 'Add New Milestone', 'ft_SALES' ),
			'edit_item'                  => __( 'Edit Milestone', 'ft_SALES' ),
			'update_item'                => __( 'Update Milestone', 'ft_SALES' ),
			'view_item'                  => __( 'View Milestone', 'ft_SALES' ),
			'separate_items_with_commas' => __( 'Separate milestones with commas', 'ft_SALES' ),
			'add_or_remove_items'        => __( 'Add or remove milestones', 'ft_SALES' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'ft_SALES' ),
			'popular_items'              => __( 'Popular Milestones', 'ft_SALES' ),
			'search_items'               => __( 'Search Milestones', 'ft_SALES' ),
			'not_found'                  => __( 'Not Found', 'ft_SALES' ),
			'no_terms'                   => __( 'No milestones', 'ft_SALES' ),
			'items_list'                 => __( 'Milestones list', 'ft_SALES' ),
			'items_list_navigation'      => __( 'Milestones list navigation', 'ft_SALES' ),
		);
		$rewrite = array(
			'slug'                       => 'milestone',
			'with_front'                 => true,
			'hierarchical'               => true,
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => $rewrite,
			'show_in_rest'               => true,
		);
		register_taxonomy( 'ft_milestone', array( 'ft_feature', 'post' ), $args );

	}


	// Register Custom Taxonomy
	public function ft_product_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Products', 'Taxonomy General Name', 'ft_SALES' ),
			'singular_name'              => _x( 'Product', 'Taxonomy Singular Name', 'ft_SALES' ),
			'menu_name'                  => __( 'Product', 'ft_SALES' ),
			'all_items'                  => __( 'All Products', 'ft_SALES' ),
			'parent_item'                => __( 'Parent Product', 'ft_SALES' ),
			'parent_item_colon'          => __( 'Parent Product:', 'ft_SALES' ),
			'new_item_name'              => __( 'New Product Name', 'ft_SALES' ),
			'add_new_item'               => __( 'Add New Product', 'ft_SALES' ),
			'edit_item'                  => __( 'Edit Product', 'ft_SALES' ),
			'update_item'                => __( 'Update Product', 'ft_SALES' ),
			'view_item'                  => __( 'View Product', 'ft_SALES' ),
			'separate_items_with_commas' => __( 'Separate products with commas', 'ft_SALES' ),
			'add_or_remove_items'        => __( 'Add or remove products', 'ft_SALES' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'ft_SALES' ),
			'popular_items'              => __( 'Popular Products', 'ft_SALES' ),
			'search_items'               => __( 'Search Products', 'ft_SALES' ),
			'not_found'                  => __( 'Not Found', 'ft_SALES' ),
			'no_terms'                   => __( 'No products', 'ft_SALES' ),
			'items_list'                 => __( 'Products list', 'ft_SALES' ),
			'items_list_navigation'      => __( 'Products list navigation', 'ft_SALES' ),
		);
		$rewrite = array(
			'slug'                       => 'product',
			'with_front'                 => true,
			'hierarchical'               => true,
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'rewrite'                    => $rewrite,
			'show_in_rest'               => true,
		);
		register_taxonomy( 'ft_product', array( 'ft_feature', 'post' ), $args );

	}


} // Ft_sales_Posttypes_and_Taxonomies