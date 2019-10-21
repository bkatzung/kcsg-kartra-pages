<?php
/*
 * KCSG Kartra Page template
 *
 * Include either the loader <script> in an absolutely bare-bones
 * HTML page or the cached full-page content AS IS.
 *
 * Author: Brian Katzung, Kappa Computer Solutions, LLC <briank@kappacs.com>
 * License: GPL3 or later
 * Copyright 2019 by Brian Katzung and Kappa Computer Solutions, LLC
 */
$kcsg_kp_sent_head = false;

function kcsg_kp_send_head() {
    global $kcsg_kp_sent_head;

    if ( $kcsg_kp_sent_head ) return;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ( ! get_theme_support( 'title-tag' ) ) { ?>
    <title><?php wp_title(); ?><title>
<?php }
    wp_head();
?></head>
<body><?php
    $kcsg_kp_sent_head = true;
}

while ( have_posts() ) {
    the_post();
    $id = get_the_ID();
    $mode = get_post_meta( $id, 'kcsg_kp_page_mode', true );
    switch ( $mode ) {
    case 'script':
	/*
	 * Include the Kartra page-loader embed <script>. It will
	 * set up our title and all meta tags and load the page
	 * content into an iframe.
	 */
	$url = get_post_meta( $id, 'kcsg_kp_url', true );
	if ( ! empty( $url ) ) {
?><!doctype html>
<html><head>
<script src="<?php echo $url; ?>"></script>
</head><body></body></html>
<?php
	    continue 2;
	}
	break;

    case 'cache':
	/*
	 * Bypass the page-loader javascript/iframe step and directly
	 * display the page HTML we pre-fetched and cached. This is
	 * equivalent to the Kartra "download page files" option, but
	 * is much faster, easier, and doesn't include downloading
	 * scores of support files that actually still load from
	 * their normal Kartra/CloudFlare/CDN servers anyway!
	 * rawurldecode reverses our post_meta protections (see admin).
	 */
	$content = get_post_meta( $id, 'kcsg_kp_cache', true );
	if ( ! empty( $content ) ) {
	    echo rawurldecode( $content );
	    continue 2;
	}
	break;
    }

    // Fall back to a "Blank Slate"-ish, native page output.
    kcsg_kp_send_head();
    the_content();
}

if ( $kcsg_kp_sent_head ) {
    wp_footer();
?></body></html><?php
}

# END
