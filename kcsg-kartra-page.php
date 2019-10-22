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
    case 'script':	# Kartra Live
    case 'cache':	# Kartra Download
	/*
	 * Display either the cached custom page loader (script/live mode)
	 * or the cached final page HTML (cache/download mode).
	 * rawurldecode reverses our post_meta protections (see admin).
	 */
	$content = get_post_meta( $id, 'kcsg_kp_cache', true );
	if ( ! empty( $content ) ) {
	    echo rawurldecode( $content );
	    continue 2;
	}
	break;
    }

    // Fall back to a "Blank Slate"-ish, native WordPress page output.
    kcsg_kp_send_head();
    the_content();
}

if ( $kcsg_kp_sent_head ) {
    wp_footer();
?></body></html><?php
}

# END
