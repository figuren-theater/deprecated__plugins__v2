<?php
declare(strict_types=1);

namespace Figuren_Theater\Network\Post_Types;

use Figuren_Theater\inc\EventManager;
use Figuren_Theater\SiteParts;

// use Figuren_Theater\Network\Features;
// use Figuren_Theater\Network\Taxonomies;


/**
 * Responsible for registering the 'tb_prod_subsite' post_type
 *
 * This special post_type acts like endpoint for the "ft_production" PT.
 * So we get URLs like /produktionen/faust/videos
 *
 * This was really tricky!
 * And it needs the following setup to work properly:
 *
 * 1. Register 'tb_prod_subsite' PT as
 *       'has_archive' => false,
 * 	     'hierachical' => true,
 * 	         This is needed for nested URLs on the FE and also for the listing of 'ft_production' posts in BE
 * 	     'rewrite' => true(ish) BUT, do set 'slug' => '' as empty
 * 	         This is needed for pretty permalinks to work. 
 * 	         Do not set this to 'produktionen' directly, because it leads to 404 errors for both:
 * 	         - /produktionen/faust ('ft_production' singular)
 * 	         - /produktionen       ('ft_production' archive)
 *
 * 2. Register 'ft_production' PT as
 * 	     'hierachical' => true,
 * 	         This is needed for nested URLs on the FE and also for the listing of 'ft_production' posts in BE
 *
 * 3. Filter 'tb_prod_subsite' PT permalinks to 
 *    replace the default rewrite-slug of 'tb_prod_subsite'
 *    with the rewrite_base of the 'ft_production' PT
 *
 * 4. Add a rewrite rule to tell WordPress, that the second part
 *    behind 'produktionen' could be a 'tb_prod_subsite' post.
 *    This sets the important query-variables which we're going 
 *    to use and unset inside our coming 'pre_get_posts' filter. 
 * 
 * 5. Filter the main query if viewing a singular 'tb_prod_subsite'
 *    to avoid 404, because the normal query would end up in zero results,
 *    because get_page_by_path() returns nothing by default.
 *    It looks up the URL path '/produktionen/faust/videos' 
 *    and checks if all of that is of type 'tb_prod_subsite',
 *    which is wrong. 
 *
 * @see https://wordpress.stackexchange.com/questions/94517/custom-post-type-nest-under-a-normal-wordpress-page
 * @see https://gist.github.com/dtbaker/5311512
 * 
 * @see https://wordpress.stackexchange.com/questions/61105/nested-custom-post-types-with-permalinks
 * @see https://wordpress.stackexchange.com/questions/39500/how-to-create-a-permalink-structure-with-custom-taxonomies-and-custom-post-types
 *
 * @package Theatrebase_Production_Subsites
 * 	
 */
class Post_Type__tb_prod_subsite extends Post_Type__Abstract implements EventManager\SubscriberInterface, SiteParts\Data__CanAddYoastTitles__Interface, SiteParts\Data__CanAddYoastVariables__Interface
{


	/**
	 * The Class Object
	 */
	static private $instance = null;

	/**
	 * Our growing up post_type
	 */
	const NAME    = 'tb_prod_subsite';

	const SLUG    = self::NAME;

	const ACTION  = self::NAME.'_as_draft';
	
	const NONCE   = self::ACTION.'_nonce';

	const PROD_PT_NAME = Post_Type__ft_production::NAME; 


	/**
	 * The permalink-structure 
	 * of the 'ft_production' PT
	 *
	 * By default, it's only 'produktionen'
	 * but could be changed by user (in the future).
	 * 
	 * @var string
	 */
	protected $prod_pt_permastruct = null;



	/**
	 * Returns an array of hooks that this subscriber wants to register with
	 * the WordPress plugin API.
	 *
	 * @return array
	 */
	public static function get_subscribed_events() : Array
	{
		return array(

			// FRONTEND & ADMIN


			//
			'generate_rewrite_rules' => 'generate_rewrite_rules',

			//
			'post_type_link' => ['post_type_link', 10, 2],

			//
			'pre_get_posts' => 'pre_get_posts',
			


			// ADMIN ONLY


			// 
			'admin_menu' => 'posttype_as_posttype_submenu',

			//
			'posts_where'    => ['production_admin_list__posts_where', 10, 2],
			// whatever this does
			// it takes almost 1 sec !!!! in mysql
			// 'posts_distinct' => ['production_admin_list__posts_distinct', 10, 2],


			//
			'admin_action_'.self::ACTION => 'admin_action_subsite_as_draft',
			//
			'page_row_actions'           => ['row_actions', 10, 2],
			// 
			'wp_before_admin_bar_render' => 'admin_bar_render',

			// NOT AVAILABLE IN GUTENBERG
			// 'post_updated_messages' => 'post_updated_messages',
			
			// Remove "Add New" Button from Admin List View
			'admin_head-edit.php' => 'admin_head',


			// 
			// 'init' => ['debug', 1000 ],
		);
	}



	/**
	 * Defines the desired use of Yoast SEO Variables.
	 * 
	 * Returns 'wpseo_titles' sub-options
	 * for this particular data-type.
	 *
	 * This sets the defaults used in meta-tags 
	 * like <title> and <meta type="description" ...>
	 * or in opengraph related ones.
	 *
	 * @see       https://trello.com/c/D7lFumgs/137-yoast-seo 
	 * @see       https://yoast.com/help/list-available-snippet-variables-yoast-seo/
	 *
	 * @package POST_TYPE__FT_PRODUCTION
	 * @version 2022.04.15
	 * @author  Carsten Bach
	 *
	 * @example for post_types   
		return = [
			'title'                        => '%%title%% %%page%% %%sep%% %%sitename%%',
			'metadesc'                     => '%%excerpt%%',
			'display-metabox'              => true,  // show some metabox for this data
			'noindex'                      => false, // prevent robots indexing
			'maintax'                      => 0,
			'schema-page-type'             => 'WebPage',
			'schema-article-type'          => 'None',
			'social-title'                 => '%%title%% %%sep%% %%sitename%%',
			'social-description'           => '%%excerpt%%',
			'social-image-url'             => '',
			'social-image-id'              => 0,
			'title-ptarchive'              => '%%archive_title%% %%page%% %%sep%% %%sitename%%',
			'metadesc-ptarchive'           => '',
			'bctitle-ptarchive'            => '',
			'noindex-ptarchive'            => false,
			'social-title-ptarchive'       => '%%archive_title%% %%sep%% %%sitename%%',
			'social-description-ptarchive' => '',
			'social-image-url-ptarchive'   => '',
			'social-image-id-ptarchive'    => 0,
		];
	 *
	 *
	 * @return  Array       list of 'wpseo_titles' definitions 
	 *                      for this posttype or taxonomy
	 */
	public static function get_wpseo_titles() : Array
	{
		return [
			'title'                        => '%%title%% %%sep%% %%parent_title%% %%sep%% %%ft_parent_pt%% %%sep%% %%sitename%%',

			// NOT WORKING
			// 'social-title'                 => '%%title%% %%sep%% %%pt_single%% %%sep%% %%sitename%%',
			// 'twitter-title'                 => '%%title%% %%sep%% %%pt_single%% %%sep%% %%sitename%%',

		];
	}


	/**
	 * [get_wpseo_variables description]
	 *
	 * @see     https://make.wordpress.org/core/2021/02/10/introducing-new-post-parent-related-functions-in-wordpress-5-7/
	 *
	 * @package project_name
	 * @version version
	 * @author  Carsten Bach
	 *
	 * @return  [type]       [description]
	 */
	public static function get_wpseo_variables() : Array
	{
		return [
			[
				'%%ft_parent_pt%%',
				// self::get_yoast_replace_var(),
				function()
				{
					if ( ! \has_post_parent( \get_the_ID() ) )
						return '';
					$_pt = \get_post_type_object( self::PROD_PT_NAME );
					return ( $_pt instanceof \WP_POST_TYPE ) ? $_pt->labels->singular_name : '';
				},
				// 'basic',
				'advanced',
				'Shows the post_type of the parent post.'
			],
		];

	}



	protected function prepare_pt() : void {}


	protected function prepare_labels() : Array
	{
		return $this->labels = array(

			# Override the base names used for labels:
			'singular' => __('Production-Subsite','theatrebase-production-subsites'),
			'plural'   => __('Production-Subsites','theatrebase-production-subsites'),
			'slug'     => self::SLUG, // must be string

		);
	}

	protected function register_post_type__default_args() : Array
	{
		return array(
			'capability_type'     => 'post',
			// 'capability_type'     => ['tb_prod_subsite','tb_prod_subsites'],
			'supports'            => array(
				'title',
				'editor',
				// 'author',
				'thumbnail',
				'excerpt',
				'custom-fields',
				// 'trackbacks',
				// 'comments',
				'revisions',
				// 'page-attributes',
				// 'post-formats',
				'ft_sub_title',
			),
			'public'              => true, // 'TRUE' enables editable post_name, called 'permalink|slug'

			// 'menu_icon'           => 'dashicons-book-alt',
			// 'menu_position'       => 50,

			'show_ui'             => true,
			'show_in_menu'        => false, // is added as submenu of 'ft_production' PT
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => false,
			

			// 'publicly_queryable'  => true,  // was TRUE for long, lets see
			// 'query_var'           => false, // If false, a post type cannot be loaded at ?{query_var}={post_slug}.

			'show_in_rest'        => true, // this in combination with  'supports' => array('editor') enables the Gutenberg editor
			'hierarchical'        => true, // important for rewriting to work with PT 'ft_production'
			'description'         => '',
			//'taxonomies'          => [
			//	// Features\UtilityFeaturesManager::TAX,
			//	// Taxonomies\Taxonomy__ft_site_shadow::NAME, # must be here to allow setting its terms, even when hidden
			//	'link_category',
			//],

			'rewrite' => [
				'slug' => self::SLUG,          // Defaults to the $post_type value. Should be translatable.
				'with_front' => true, // Defaults to true
				'feeds' => false,      // Defaults to has_archive
				'pages' => false,      // Defaults to true
				// 'ep_mask' => 'EP_NONE',       // defaults to EP_PERMALINK
	
			],

			#
			'has_archive' => false,

			#
			'can_export' => true,


			/**
			 * Localiced Labels
			 * 
			 * ExtendedCPTs generates the default labels in English for your post type. 
			 * If you need to allow your post type labels to be localized, 
			 * then you must explicitly provide all of the labels (in the labels parameter) 
			 * so the strings can be translated. There is no shortcut for this.
			 *
			 * @source https://github.com/johnbillion/extended-cpts/pull/5#issuecomment-33756474
			 * @see https://github.com/johnbillion/extended-cpts/blob/d6d83bb41eba9a3603929244c71f3f806c2a14d8/src/PostType.php#L152
			 */
			# fallback
			'label'         => $this->labels['plural'],
			'labels'                => [
				'name'                  => __( 'Production-Subsites', 'theatrebase-production-subsites' ),
				'singular_name'         => __( 'Production-Subsite', 'theatrebase-production-subsites' ),
				'all_items'             => __( 'All Production-Subsites', 'theatrebase-production-subsites' ),
				'archives'              => __( 'Production-Subsite Archives', 'theatrebase-production-subsites' ),
				'attributes'            => __( 'Production-Subsite Attributes', 'theatrebase-production-subsites' ),
				'insert_into_item'      => __( 'Insert into Production-Subsite', 'theatrebase-production-subsites' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Production-Subsite', 'theatrebase-production-subsites' ),
				'featured_image'        => _x( 'Image', 'tb_prod_subsite', 'theatrebase-production-subsites' ),
				'set_featured_image'    => _x( 'Set image', 'tb_prod_subsite', 'theatrebase-production-subsites' ),
				'remove_featured_image' => _x( 'Remove image', 'tb_prod_subsite', 'theatrebase-production-subsites' ),
				'use_featured_image'    => _x( 'Use as image', 'tb_prod_subsite', 'theatrebase-production-subsites' ),
				'filter_items_list'     => __( 'Filter Production-Subsites list', 'theatrebase-production-subsites' ),
				'items_list_navigation' => __( 'Production-Subsites list navigation', 'theatrebase-production-subsites' ),
				'items_list'            => __( 'Production-Subsites list', 'theatrebase-production-subsites' ),
				'new_item'              => __( 'New Production-Subsite', 'theatrebase-production-subsites' ),
				'add_new'               => __( 'Add New', 'theatrebase-production-subsites' ),
				'add_new_item'          => __( 'Add New Production-Subsite', 'theatrebase-production-subsites' ),
				'edit_item'             => __( 'Edit Production-Subsite', 'theatrebase-production-subsites' ),
				'view_item'             => __( 'View Production-Subsite', 'theatrebase-production-subsites' ),
				'view_items'            => __( 'View Production-Subsites', 'theatrebase-production-subsites' ),
				'search_items'          => __( 'Search Production-Subsites', 'theatrebase-production-subsites' ),
				'not_found'             => __( 'No Production-Subsites found', 'theatrebase-production-subsites' ),
				'not_found_in_trash'    => __( 'No Production-Subsites found in trash', 'theatrebase-production-subsites' ),
				'parent_item_colon'     => __( 'Production:', 'theatrebase-production-subsites' ),
				'menu_name'             => __( 'Production-Subsites', 'theatrebase-production-subsites' ),
			],
			// 'template'      => '',
			// 'template_lock'      => '',
		);
	}

	protected function register_extended_post_type__args() : Array
	{
		return array(

			# The "Featured Image" text used in various places
			# in the admin area can be replaced with
			# a more appropriate name for the featured image
			'featured_image' => _x( 'Image', 'tb_prod_subsite', 'theatrebase-production-subsites' ),

			#
			'enter_title_here' => __('Subsite Title','theatrebase-production-subsites'),

			#
			'quick_edit' => false,

			# Add the post type to the site's main RSS feed:
			'show_in_feed' => false,

			# Add the post type to the 'Recently Published' section of the dashboard:
			'dashboard_activity' => true,

			# An entry is added to the "At a Glance"
			# dashboard widget for your post type by default.
			'dashboard_glance' => false,

			# Add some custom columns to the admin screen:
			//'admin_cols' => [
			//	// The default Title column:
			//	'title',
			//],

			# Add some dropdown filters to the admin screen:
			//'admin_filters' => [
			//	'link_category' => [
			//		'title'    => __('All Link Categories','theatrebase-production-subsites'),
			//		'taxonomy' => 'link_category'
			//	],
			//],

		);
	}



/**
 * Remove "Add New" Button from Admin List View
 *
 * @package project_name
 * @version 2022.04.11
 * @author  Carsten Bach
 *
 * @see     /wp-admin/edit.php#L408
 * @see     https://developer.wordpress.org/reference/hooks/admin_head-hook_suffix/
 *
 */
public function admin_head()
{
	global $typenow;

	if ( self::NAME !== $typenow )
		return;

    // Output <head> content here, e.g.:
    echo '<style type="text/css">'
         .'a.page-title-action { display: none !important; }'
         .'</style>';
}

/**
 * Subsite-specific update messages.
 *
 * @package project_name
 * @version 2022.04.11
 * @author  Carsten Bach
 *
 * @see     /wp-admin/edit-form-advanced.php
 * @see     https://developer.wordpress.org/reference/hooks/post_updated_messages/
 *
 * @since   WP 3.0.0
 *
 * @param   array   $messages   Existing post & page update messages.
 * 
 * @return  array               Amended post update messages with new CPT update messages.
 *//*
public function post_updated_messages( Array $messages ) : Array
{
    $post             = get_post();
    $post_type        = get_post_type( $post );
    $post_type_object = get_post_type_object( $post_type );
 
    $messages[ self::NAME ] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => __( 'Subsite updated.', 'theatrebase-production-subsites' ),
        2  => __( 'Custom field updated.', 'theatrebase-production-subsites' ),
        3  => __( 'Custom field deleted.', 'theatrebase-production-subsites' ),
        4  => __( 'Subsite updated.', 'theatrebase-production-subsites' ),
         // translators: %s: date and time of the revision 
        5  => isset( $_GET['revision'] ) ? sprintf( __( 'Subsite restored to revision from %s', 'theatrebase-production-subsites' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6  => __( 'Subsite published.', 'theatrebase-production-subsites' ),
        7  => __( 'Subsite saved.', 'theatrebase-production-subsites' ),
        8  => __( 'Subsite submitted.', 'theatrebase-production-subsites' ),
        9  => sprintf(
            __( 'Subsite scheduled for: <strong>%1$s</strong>.', 'theatrebase-production-subsites' ),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( 
            	__( 'M j, Y @ G:i', 'theatrebase-production-subsites' ), 
            	strtotime( $post->post_date ) 
            )
        ),
        10 => __( 'Subsite draft updated.', 'theatrebase-production-subsites' ),
    );
 
    if ( $post_type_object->publicly_queryable ) {
        $permalink = get_permalink( $post->ID );
 
        $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View subsite', 'theatrebase-production-subsites' ) );
        $messages[ self::NAME ][1] .= $view_link;
        $messages[ self::NAME ][6] .= $view_link;
        $messages[ self::NAME ][9] .= $view_link;
 
        $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
        $preview_link      = sprintf( '<a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview subsite', 'theatrebase-production-subsites' ) );
        $messages[ self::NAME ][8] .= $preview_link;
        $messages[ self::NAME ][10] .= $preview_link;
    }
 
    return $messages;
}*/
 

	
	/**
	 * Retrieve & persist rewrite-base of 'ft_production' PT
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.10
	 * @author  Carsten Bach
	 *
	 * @return  string       the URL part, that indicates our PT, by default it's: 'produktionen'
	 */
	protected function get_prod_pt_permastruct() : string
	{
		if ( $this->prod_pt_permastruct )
			return $this->prod_pt_permastruct;


		global $wp_rewrite;

		// get something like "produktionen/%ft_productions%" 
		// or even changed to "stücke/kinder/%ft_productions%"
		$_prod_pt_permastruct = $wp_rewrite->get_extra_permastruct( self::PROD_PT_NAME );

		// could be string|false :: https://developer.wordpress.org/reference/classes/wp_rewrite/get_extra_permastruct/#return
		$_prod_pt_permastruct = ($_prod_pt_permastruct) ? $_prod_pt_permastruct : '';

		// remove the singular-struct of the 'ft_production' PT
		$_prod_pt_permastruct = str_replace( '/%'.self::PROD_PT_NAME.'%', '', $_prod_pt_permastruct );

		// in case that the 'ft_production' has a changed permastruct
		// from the default, get the used struct
		return $this->prod_pt_permastruct = $_prod_pt_permastruct;
	}



	/**
     * Fires after the rewrite rules are generated.
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.11
	 * @author  Carsten Bach
	 *
     * @since   WP 1.5.0
	 * @see     https://developer.wordpress.org/reference/hooks/generate_rewrite_rules/
	 *
     * @param   WP_Rewrite   $wp_rewrite   Current WP_Rewrite instance (passed by reference).
	 */
	public function generate_rewrite_rules( \WP_Rewrite $wp_rewrite )
	{
		
		$_endpoints_to_exclude = join('|', [
			$wp_rewrite->comments_base,
			$wp_rewrite->pagination_base,
			$wp_rewrite->comments_pagination_base,
			//$wp_rewrite->feed_base,
			//'rdf',
			//'rss',
			//'rss2',
			//'atom',
			'trackback',
			'embed',
		]);


		// original, ignoring EP_PERMALINK endpoints
		// $_url   = $this->get_prod_pt_permastruct() . '/([^/]*)/([^/]*)/?$';

		// exclude EP_PERMALINK endpoints
		// https://regex101.com/r/iIYoHa/1
		// $_url   = $this->get_prod_pt_permastruct() . '/([^/]*)/((?!'. $_endpoints_to_exclude .').[^/]*)/?$';
		$_url   = $this->get_prod_pt_permastruct() . '/((?!'. $wp_rewrite->pagination_base .').[^/]*)/((?!'. $_endpoints_to_exclude .').[^/]*)/?$';

		$_match_args = [
			self::NAME           => '$matches[2]',
			self::PROD_PT_NAME   => '$matches[1]'
		];
		
		//
		$_match = \add_query_arg( 
			$_match_args, 
			'index.php'
		);

		$subsite_rules = array(
			// 'produktionen/([^/]*)/((?!feed|trackback|...).[^/]*)/?$' => 'index.php?tb_prod_subsite=$matches[2]&ft_production=$matches[1]'
			$_url => $_match
		);

		$wp_rewrite->rules = $subsite_rules + $wp_rewrite->rules;
		return $wp_rewrite->rules;
	}
 


	/**
	 * Change permalink to use the same rewrite-base as 'ft_prodution' PT
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.10
	 * @author  Carsten Bach
	 *
	 * @param   string       $permalink URL of the current post
	 * @param   WP_Post      $post      current Post aka Production-Subsite
	 * 
	 * @return  string                  URL of the current post
	 */
	public function post_type_link( string $permalink, \WP_Post $post ) : string
	{
		global $pagenow;

		if ( self::NAME !== $post->post_type )
			return $permalink;
		
		// Disable this filter inside the block-editor
		// to allow the normal 'post_name' input to be accessible
		// otherwise, it would be removed by the existence of this filter
		if ( 'post.php' === $pagenow || 'post-new.php' === $pagenow )
			return $permalink;
		
		return str_replace( self::NAME, $this->get_prod_pt_permastruct(), $permalink );
	}



	/**
	 * Filter the main query to 
	 * successfully return a subsite-post 
	 * when queried by URL 
	 *
	 * @example domain.tld/produktionen/faust/bilder
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.11
	 * @author  Carsten Bach
	 *
	 * @see     https://wordpress.stackexchange.com/a/383691
	 *
	 * @param   WP_Query    $query [description]
	 */
	public function pre_get_posts( \WP_Query $query )
	{

		//
		if ( ! $query->is_main_query() )
			return;

		//
		if ( ! isset( $query->query_vars['post_type'] ) )
			return;
		
		//
		if ( ! isset( $query->query_vars[ self::NAME ] ) )
			return;

		//
		if ( self::NAME !== $query->query_vars['post_type'] )
			return;
		
		//
		if ( ! isset( $query->query_vars[ self::PROD_PT_NAME ] ) )
			return;
/**
 * @see https://make.wordpress.org/core/2020/06/26/wordpress-5-5-better-fine-grained-control-of-redirect_guess_404_permalink/
 * @see https://core.trac.wordpress.org/ticket/16557
 */
\add_filter('do_redirect_guess_404_permalink', '__return_false');

		$production_query = new \WP_Query( array( 
			'post_name__in' => [ $query->query_vars[ self::PROD_PT_NAME ] ],
			'post_type' => self::PROD_PT_NAME,
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'posts_per_page' => 1,
		) );

		// guard clausing
		if ( \is_wp_error( $production_query ) )
			return;
		// guard clausing
		if ( ! property_exists( $production_query, 'post') )
			return;
		// guard clausing
		if ( ! is_a( $production_query->post, 'WP_Post') )
			return;

		
		$subsite_query = new \WP_Query( array( 
			'post_name__in' => [ $query->query_vars[ self::NAME ] ],
			'post_type' => self::NAME,
			// 'post_parent' // this can be tricky
			'post_parent__in' => [ $production_query->post->ID ],
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'posts_per_page' => 1,
		) );

		// guard clausing
		if ( \is_wp_error( $subsite_query ) )
			return;
		// guard clausing
		if ( ! property_exists( $subsite_query, 'post') )
			return;
		// guard clausing
		if ( ! is_a( $subsite_query->post, 'WP_Post') )
			return;


		$query->query['p'] = $subsite_query->post->ID;
		$query->query['post_type'] = self::NAME;
		$query->query_vars['p'] = $subsite_query->post->ID;
		$query->query_vars['post_type'] = self::NAME;

		unset( $query->query['name'] );
		unset( $query->query['pagename'] );
		unset( $query->query[ self::NAME ] );
		unset( $query->query[ self::PROD_PT_NAME ] );

		unset( $query->query_vars['name'] );
		unset( $query->query_vars['pagename'] );
		unset( $query->query_vars[ self::NAME ] );
		unset( $query->query_vars[ self::PROD_PT_NAME ] );

		// some defaults
		// because we are only viewing is_singular()
		$query->query['no_found_rows'] = true;
		$query->query_vars['no_found_rows'] = true;
		$query->query['update_post_meta_cache'] = false;
		$query->query_vars['update_post_meta_cache'] = false;
		$query->query['update_post_term_cache'] = false;
		$query->query_vars['update_post_term_cache'] = false;
		$query->query['posts_per_page'] = 1;
		$query->query_vars['posts_per_page'] = 1;

	}
			
	

	/**
	 * Add "Production Subsites" PT to the list of
	 * "Production" post_types hierachically listed
	 * below their respective post_parent.
	 *
	 * To make this work at least the 
	 * - 'ft_production' PT
	 *    and
	 * - 'tb_prod_subsite' PT
	 * needs to be registered as
	 *   'hierachical' => true
	 *
	 * Filters the WHERE clause of the main query 
	 * on an admin-page request of this kind:
	 *
	 * @example   wp-admin/edit.php?post_type=ft_production
	 *  
	 * @package   Theatrebase_Production_Subsites
	 * @version   2022.04.05
	 * @author    Carsten Bach
	 *  
	 * @since     WP 1.5.0
	 * @see       https://developer.wordpress.org/reference/hooks/posts_where/
	 * @see 	  https://core.trac.wordpress.org/browser/tags/5.9/src/wp-includes/class-wp-query.php#L2625
	 *  
	 * @param     string     $where The WHERE clause of the query.
	 * @param     WP_Query   $query The WP_Query instance (passed by reference).
	 *   
	 * @return    string     $where The WHERE clause of the query.
	 */
	public static function production_admin_list__posts_where( string $where, \WP_Query $query ) : string
	{
		global $pagenow;
		
		// DEBUG a FILTER ;)
		// die(var_dump($where));

		//
		if ( 'edit.php' !== $pagenow )
			return $where;

		// only the default listing
		// which has this invisible (default)
		// orderby query var
		// 'menu_order title' === $query->query_vars['orderby']
		if ( 'menu_order title' !== \get_query_var( 'orderby' ) )
			return $where;	    

		//
		if ( ! isset( $query->query_vars['post_type'] ) )
			return $where;

		//
		if ( self::PROD_PT_NAME !== $query->query_vars['post_type'] )
			return $where;

		// if all of the above guard clauses
		// went fine, go on and
		// add our post_type to the current query
		$stringToReplace = "_posts.post_type = '".self::PROD_PT_NAME."'";
		$replaceWith     = "_posts.post_type IN ('".self::PROD_PT_NAME."', '".self::NAME."')";
		$where = str_replace(
			$stringToReplace,
			$replaceWith,
			$where
		);

		return $where;
	}



	/**
	 * Filters the DISTINCT clause of the query.
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.05
	 * @author  Carsten Bach
	 * 
	 * @since   WP 2.1.0
	 * @see     https://developer.wordpress.org/reference/hooks/posts_distinct/
	 * @see 	https://core.trac.wordpress.org/browser/tags/5.9/src/wp-includes/class-wp-query.php#L2803
	 *
	 * @param   string       $distinct The DISTINCT clause of the query.
	 * @param   WP_Query     $query    The WP_Query instance (passed by reference).
	 * 
	 * @return  string       $distinct The DISTINCT clause of the query.
	 *//*
	public static function production_admin_list__posts_distinct( string $distinct, \WP_Query $query ) : string
	{
		global $pagenow;
		// die(var_dump($distinct)); // DEBUG a FILTER ;)

		if ( 
			\is_admin() 
			&& 
			'edit.php' === $pagenow
			&&
			empty( \get_query_var('orderby') )
			&&
			isset( $_GET['post_type'] )
			&& 
			self::PROD_PT_NAME === $_GET['post_type']
		) {
			return "DISTINCT";
		}
		return $distinct;
	}*/



	/*
	 * Handles the "New Production-Subsite" Action
	 * and creates a new "Production-Subsite" as draft.
	 * 
	 * Automatically sets post_parent based on 
	 * the requesting production-post ID.
	 * 
	 * On Success, user is redirected to the edit screen.
	 *
	 * The FUNCTIONNAME is important as it is part of the 
	 * called admin_{hook}. Be carefull on change!
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.06
	 * @author  Carsten Bach
	 *
	 */
	public static function admin_action_subsite_as_draft()
	{

		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && self::ACTION == $_REQUEST['action'] ) ) )
		{
			// \wp_die('No post to prod_subsite has been supplied!');
			//
			\do_action( 'qm/error', 'Production-Subsite creation failed because there was no production ID.' );

			// bye bye
			return;
		}
	 
		
		// Nonce verification
		if ( !isset( $_GET[ self::NONCE ] ) || !\wp_verify_nonce( $_GET[ self::NONCE ], basename( __FILE__ ) ) )
			return;
		
		// get the original post id
		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );
		
		// and all the original post data then
		$post = \get_post( $post_id );

		//
		if ( ! is_a( $post, 'WP_Post' ) )
		{
			// \wp_die('Post creation failed, could not find original post: ' . $post_id);
			\do_action( 'qm/error', 'Production-Subsite creation failed, could not find original production with ID: {post_id}', [
				'post_id' => $post_id,
			] );

			// bye bye
			return;
		}

		// if post data exists, 
		// create the post prod_subsite

		// new post data array
		// Note: post_title and post_content are required
		$args = [

			// 'post_author'    => $new_post_author, // Default is the current user ID.
			// 'post_title'     => sprintf( 
			// 	__('New … for %s','Title of new draft Subsite for Productions (%s)', 'theatrebase-production-subsites'),
			// 	$post->post_title
			// ), // a pre-filled title prevents the pattern-modal to trigger
			'post_title'     => '', // required
			'post_content'   => ' ', // required

			'post_status'    => 'draft',
			'post_parent'    => $post_id,
			'post_type'      => self::NAME,

			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		];

		//
		$new_post_id = \wp_insert_post( $args );

		//
		$_wp_redirect_url_args = [
			'action' => 'edit',
			'post'   => $new_post_id
		];
		
		//
		$_wp_redirect_url = \add_query_arg( 
			$_wp_redirect_url_args, 
			\admin_url( 'post.php' )
		);
 
		
		// finally, redirect to the edit post screen for the new draft
		// \wp_redirect( \admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		\wp_redirect( $_wp_redirect_url );
		exit;
	}



	/**
	 * Add a "New Production-Subsite" Button
	 * to the action list next to 'Quickedit'.
	 *
	 * Filters the array of row action links on the Pages list table.
	 *
	 * The filter is evaluated only for hierarchical post types.
	 *
	 * @since   WP 2.8.0
	 * @uses    page_row_actions
	 *
	 * @see 	https://core.trac.wordpress.org/browser/tags/5.9/src/wp-admin/includes/class-wp-posts-list-table.php#L1521
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.09
	 * @author  Carsten Bach
	 *
	 * @param   string[]     $actions An array of row action links. Defaults are
	 *                                'Edit', 'Quick Edit', 'Restore', 'Trash',
	 *                                'Delete Permanently', 'Preview', and 'View'.
	 * @param   WP_Post      $post    The post object.
	 * 
	 * @return  Array                 List of Actions, now available to this PT on the edit.php
	 */
	public function row_actions( Array $actions, \WP_Post $post ) : Array
	{
		
		//
		if ( ! \current_user_can('edit_posts') )
			return $actions;		

		//
		if ( self::PROD_PT_NAME !== \get_post_type( $post ) )
			return $actions;		
			
		//
		$actions[ self::ACTION ] = sprintf(
			'<a href="%1$s" title="%2$s">%3$s</a>',
			$this->get_add_new_url( $post ),
			__('New Production-Subsite','theatrebase-production-subsites'),
			__('New Production-Subsite','theatrebase-production-subsites'),
		);

		return $actions;
	}



	/**
	 * Get an nonced Admin-URL to create a new 
	 * "Production Subsite" based on a Production-post-ID
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.09
	 * @author  Carsten Bach
	 *
	 * @param   WP_Post     $post should be "Production"
	 * 
	 * @return  string            Admin-URL 
	 */
	protected function get_add_new_url( \WP_Post $post ) : string
	{
		//
		$_wp_action_url_args = [
			'action' => self::ACTION,
			'post'   => $post->ID
		];
		
		//
		$_wp_action_url = \add_query_arg( 
			$_wp_action_url_args, 
			\admin_url( 'admin.php' )
		);

		//
		$_wp_nonce_url = \wp_nonce_url(
			$_wp_action_url, 
			basename( __FILE__ ),
			self::NONCE
		);

		return $_wp_nonce_url;
	}
 


	/**
	 * This adds a "New Subsite" Link to the "+ New" menu of the Admin_Bar
	 * if the currently viewed URL is a singular production.
	 * 
	 * The wp_before_admin_bar_render action allows developers 
	 * to modify the $wp_admin_bar object 
	 * before it is used to render the Toolbar to the screen.
	 *
	 * Please note that you must declare the $wp_admin_bar global object, 
	 * as this hook is primarily intended to give you direct access 
	 * to this object before it is rendered to the screen.
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version 2022.04.09
	 * @author  Carsten Bach
	 *
	 * @see     https://developer.wordpress.org/reference/hooks/wp_before_admin_bar_render/
	 *
	 */
	public function admin_bar_render()
	{
		global $wp_admin_bar, $post;

		//
		if ( ! is_a( $post, 'WP_Post' ) )
			return;

		//
		if ( ! \current_user_can('edit_posts') )
			return;		

		//
		if ( self::PROD_PT_NAME !== \get_post_type( $post ) )
			return;

		// we can add a submenu item too
		$wp_admin_bar->add_menu( array(
			'parent' => 'new-content',
			'id'     => 'new_'.self::NAME,
			'title'  => __('Production-Subsite','theatrebase-production-subsites'),
			'href'   => $this->get_add_new_url( $post )
		) );
	}



	/**
	 * [posttype_as_posttype_submenu description]
	 *
	 * @package Theatrebase_Production_Subsites
	 * @version version
	 * @author  Carsten Bach
	 *
	 * @see     https://developer.wordpress.org/reference/functions/add_submenu_page/
	 * @see     https://github.com/WordPress/wordpress-develop/blob/5.9/src/wp-admin/includes/plugin.php#L1375-L1465
	 *
	 */
	public static function posttype_as_posttype_submenu(){
		\add_submenu_page(
			'edit.php?post_type=' . self::PROD_PT_NAME,
			__('Show All Production-Subsites','theatrebase-production-subsites'),
			__('All Production-Subsites','theatrebase-production-subsites'),
			'edit_posts',
			'edit.php?post_type=' . self::NAME,
			null,
			2
		);
	}



	public static function get_instance()
	{
		if ( null === self::$instance )
			self::$instance = new self;
		return self::$instance;
	}



	public function debug()
	{
		global $wp_rewrite;
		\do_action( 'qm/info', \get_post_type_object( self::NAME ) );
		// \do_action( 'qm/info', $wp_rewrite );
	}
}
