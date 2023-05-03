<?php 

if ( 'ft_theme' !== \get_post_type() )
	return;

// prepare some defaults
$buttons    = [];
$theme_name = \get_post()->post_name;

// details link (post single)
if ( ! \is_singular( 'ft_theme' ) ) {
	$details_link = \get_permalink();
}

// demo link
if ( \has_term( 'ft_theme-has-demo-site', 'hm-utility' ) ) {
	$demo_link = \site_url( 'demos/' . $theme_name . '/' );
}

// registration link
if ( \has_term( 'ft_theme-register-with', 'hm-utility' ) ) {
	// $registration_link       = 'https://mein.figuren.theater/?722f94eebb6b=' . $theme_name;
	$_coresites       = array_flip( FT_CORESITES );
	$_uid_theme_field = '722f94eebb6b';
	$registration_link       = add_query_arg(
		$_uid_theme_field,
		rawurlencode( $theme_name ),
		get_site_url( $_coresites['mein'], '', 'https' )
	);
}

if ( isset($demo_link) ) {
	$buttons[] = '<!-- wp:button {"backgroundColor":"background","textColor":"primary","style":{"typography":{"textDecoration":"underline"}},"className":"is-style-fill","fontSize":"small"} -->
<div class="wp-block-button has-custom-font-size is-style-fill has-small-font-size" style="text-decoration:underline"><a class="wp-block-button__link has-primary-color has-background-background-color has-text-color has-background wp-element-button" href="' . $demo_link . '">Demo</a></div>
<!-- /wp:button -->';
}

if ( isset($details_link) ) {
	$buttons[] = '<!-- wp:button {"backgroundColor":"background","textColor":"primary","style":{"typography":{"textDecoration":"underline"}},"className":"is-style-fill","fontSize":"small"} -->
<div class="wp-block-button has-custom-font-size is-style-fill has-small-font-size" style="text-decoration:underline"><a class="wp-block-button__link has-primary-color has-background-background-color has-text-color has-background wp-element-button" href="' . $details_link . '">Details</a></div>
<!-- /wp:button -->';
}

if ( isset($registration_link) ) {
	$buttons[] = '<!-- wp:button -->
<div class="wp-block-button"><a href="' . $registration_link . '" class="wp-block-button__link wp-element-button">Auswählen &amp; los</a></div>
<!-- /wp:button -->';
}

if ( empty($buttons)) {
	return;
}
echo \do_blocks( '
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"typography":{"textTransform":"uppercase"}},"fontSize":"small"} -->
<div class="wp-block-buttons  has-small-font-size" style="text-transform:uppercase">' . join( ' ', $buttons ) . '</div>
<!-- /wp:buttons -->
	');
