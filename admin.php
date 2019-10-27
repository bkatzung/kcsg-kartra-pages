<?php
/*
 * KCSG Kartra Pages plugin admin support
 *
 * Author: Brian Katzung, Kappa Computer Solutions, LLC <briank@kappacs.com>
 * License: GPL3 or later
 * Copyright 2019 by Brian Katzung and Kappa Computer Solutions, LLC
 */

add_action( 'add_meta_boxes', 'kcsg_kp_add_meta_boxes' );
add_action( 'wp_ajax_set_kcsg_kp', 'kcsg_kp_ajax_set' );

// Register our meta box
function kcsg_kp_add_meta_boxes() {
    add_meta_box( 'kcsg_kp', __( 'KCSG Kartra Pages', 'kcsg-kartra-pages' ), 'kcsg_kp_render_meta', 'page' );
}

// Render our meta box
function kcsg_kp_render_meta( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'kcsg_kp' );
    $id = $post->ID;
    $mode = get_post_meta( $id, 'kcsg_kp_page_mode', true );
    $page_modes = kcsg_kp_page_modes( $mode );
    $url = get_post_meta( $id, 'kcsg_kp_url', true );

    // Localizations (HTML)
    $mode_section = esc_html__( 'KCSG Kartra Page Template Mode', 'kcsg-kartra-pages' );
    $source_or_url = esc_html__( 'Page Embed code or URL (copy and paste from Kartra)', 'kcsg-kartra-pages' );
    $apply = esc_html__( 'Apply', 'kcsg-kartra-pages' );

    // Localizations (HTML via JS)
    $save_first = esc_js( esc_html__( 'Please save draft or publish first.', 'kcsg-kartra-pages' ) );
    $processing = esc_js( esc_html__( 'Processing request...', 'kcsg-kartra-pages' ) );
    $request_failed = esc_js( esc_html__( 'Request failed', 'kcsg-kartra-pages' ) );

    // Escape URL in input value attribute
    $esc_url = esc_attr( esc_url( $url ) );

    echo <<<HTML
<p>$mode_section</p>
<div id='kcsg_kp_page_modes'>$page_modes</div>
<p><label for='kcsg_kp_source'>$source_or_url</label></p>
<p><input type='text' id='kcsg_kp_source' name='kcsg_kp_source' value='$esc_url' style='width: 90%;'></p>
<p><button id='kcsg_kp_apply' style='margin-right: 1em;'>$apply</button> <span id='kcsg_kp_message'></span></p>
<script>
function kcsgKpApplied (jr) {
    r = JSON.parse(jr);
    if (r.pageModes) {
	jQuery('#kcsg_kp_page_modes').html(r.pageModes);
    }
    if (r.source) {
	jQuery('#kcsg_kp_source').val(r.source);
    }
    jQuery('#kcsg_kp_message').html(r.message ? r.message : '');
    jQuery('#kcsg_kp_apply').attr('disabled', false);
}

function kcsgKpErrorText (t) {
    return '<span style="color: red;">' + t + '</span>';
}

jQuery('#kcsg_kp_apply').click(function () {
    var postId = wp.data.select('core/editor').getCurrentPostId();
    if (null === postId) {
	jQuery('#kcsg_kp_message').html(kcsgKpErrorText('$save_first'));
	return;
    }

    // Extract URLs from page-loader <script> client-side
    var source = jQuery('#kcsg_kp_source').val();
    var matches = source.match(/https:[_0-9a-z\/.-]+/i);
    if (matches && matches[0] != source) {
	jQuery('#kcsg_kp_source').val(source = matches[0]);
    }

    jQuery('#kcsg_kp_apply').attr('disabled', true);
    jQuery('#kcsg_kp_message').html('$processing');
    jQuery.post(ajaxurl, {
	'action': 'set_kcsg_kp',
	'post_id': postId,
	'kcsg_kp': jQuery('#kcsg_kp [name=kcsg_kp]').val(),
	'kcsg_kp_page_mode': jQuery('#kcsg_kp [name=kcsg_kp_page_mode]:checked').val(),
	'kcsg_kp_source': jQuery('#kcsg_kp [name=kcsg_kp_source]').val(),
      }).done(kcsgKpApplied).fail(function () {
	jQuery('#kcsg_kp_apply').attr('disabled', false);
	jQuery('#kcsg_kp_message').html(kcsgKpErrorText('$request_failed'));
    });
});
</script>
HTML;
}

// Return rendering for template page modes
function kcsg_kp_page_modes( $mode ) {
    $opt_blank = esc_html__( 'WordPress', 'kcsg-kartra-pages' );
    $opt_script = esc_html__( 'Kartra Live', 'kcsg-kartra-pages' );
    $opt_cache = esc_html__( 'Kartra Download (Apply to refresh)', 'kcsg-kartra-pages' );

    $blank_c = $script_c = $cache_c = '';
    switch ( $mode ) {
    case 'blank':	# WordPress
	$blank_c = ' checked';
	break;

    case 'cache':	# Kartra Download
	$cache_c = ' checked';
	break;

    case 'script':	# Kartra Live
	$script_c = ' checked';
	break;
    }

    return (
      "<label><input type='radio' name='kcsg_kp_page_mode' value='blank'$blank_c> $opt_blank</label> " .
      "<label><input type='radio' name='kcsg_kp_page_mode' value='script'$script_c> $opt_script</label> " .
      "<label><input type='radio' name='kcsg_kp_page_mode' value='cache'$cache_c> $opt_cache</label> "
    );
}

// Handle updates when the settings are applied
function kcsg_kp_ajax_set() {
    $post_id = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;

    // Look up the page
    $query = new WP_Query( array( 'post_type' => 'page', 'page_id' => $post_id ) );
    if ( ! $query->have_posts() ) {
	kcsg_kp_return_fail( sprintf(
	  __( 'Page %s not found', 'kcsg-kartra-pages' ),
	  $post_id ) );
    }
    $query->the_post();

    // Verify permissions
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
	kcsg_kp_return_fail( __( 'Permission denied', 'kcsg-kartra-pages' ) );
    }

    // Validate the nonce
    if ( ! isset( $_POST[ 'kcsg_kp' ] ) || !wp_verify_nonce( $_POST[ 'kcsg_kp' ], plugin_basename( __FILE__ ) ) ) {
	kcsg_kp_return_fail( __( 'Please refresh the page and try again', 'kcsg-kartra-pages' ) );
    }

    // Validate and sanitize the requested template page mode
    $new_mode = ( isset( $_POST[ 'kcsg_kp_page_mode' ] ) && in_array( $_POST[ 'kcsg_kp_page_mode' ], array( 'blank', 'script', 'cache' ) ) ) ? sanitize_text_field( $_POST[ 'kcsg_kp_page_mode' ] ) : '';
    if ( '' === $new_mode ) {
	kcsg_kp_return_fail( __( 'Please select a template mode', 'kcsg-kartra-pages' ) );
    }

    // Sanitize and validate the source URL
    $new_url = isset( $_POST[ 'kcsg_kp_source' ] ) ? sanitize_kartra_page_url( $_POST[ 'kcsg_kp_source' ] ) : '';

    if ( '' === $new_url ) {
	if ( 'blank' != $new_mode ) {
	    // We need a source for script/live or cache/download mode
	    kcsg_kp_return_fail( __( 'Please supply a source', 'kcsg-kartra-pages' ) );
	}
    } else if ( is_kartra_page_url( $new_url ) ) {
	if ( false === strpos( $new_url, '/page/embed/' ) ) {
	    /*
	     * Change page URLs to embedded-page URLs, but leave embed-script
	     * URLs alone.
	     */
	    $new_url = str_replace( '/page/', '/page_embed/', $new_url );
	}
    } else {
	kcsg_kp_return_fail( __( 'A kartra.com page URL is required. For custom-domain pages, use the page Embed code.' ) );
    }

    // Refresh or clear the cache and page mode
    if ( 'blank' === $new_mode ) {
	// No meta info needed for native WP content
	delete_post_meta( $post_id, 'kcsg_kp_cache' );
	delete_post_meta( $post_id, 'kcsg_kp_page_mode' );
    } else {
	$new_cache = kcsg_kp_fetch_page( $new_url, $new_mode );
	if ( '' === $new_cache ) {
	    kcsg_kp_return_fail( sprintf(
	      __('No contents found at %s', 'kcsg-kartra-pages' ),
	      $new_url ) );
	}
	update_post_meta( $post_id, 'kcsg_kp_cache', meta_encode( $new_cache ) );
	update_post_meta( $post_id, 'kcsg_kp_page_mode', $new_mode );
    }

    // Update or clear the source URL
    if ( '' === $new_url ) {
	delete_post_meta( $post_id, 'kcsg_kp_url' );
    } else {
	update_post_meta( $post_id, 'kcsg_kp_url', $new_url );
    }

    kcsg_kp_return_done( $new_mode, $new_url, __( 'Request complete', 'kcsg-kartra-pages' ) );
}

if ( ! function_exists( 'sanitize_kartra_page_url' ) ) {
    function sanitize_kartra_page_url( $url ) {
	// Accept a subset of valid URL characters (no queries or fragments)
	return preg_replace( '/[^_a-z0-9:\/.-]/i', '', sanitize_text_field( $url ) );
    }
}

if ( ! function_exists( 'is_kartra_page_url' ) ) {
    function is_kartra_page_url( $url ) {
	// Must look like a page, embedded-page, or page-loader URL
	return preg_match( '/^https:\/\/[_a-z0-9-]+\.kartra\.com\/page(?:_embed)?\//i', $url );
    }
}

/*
 * Return post_meta-safe encoding (decode with rawurldecode)
 * % => %25, \ => %5C
 * so that "strings \"like this\"" don't become "strings "like this""
 * in MySQL and break the final page.
 */
if ( ! function_exists( 'meta_encode' ) ) {
    function meta_encode( $text ) {
	return str_replace( array( '%', '\\' ), array( '%25', '%5C' ), $text );
    }
}

// Return an AJAX success status
function kcsg_kp_return_done( $mode, $url, $text ) {
    $modes = kcsg_kp_page_modes( $mode );
    echo json_encode( array( 'status' => 'done', 'pageModes' => $modes, 'source' => $url, 'message' => esc_html( $text ) ) );
    wp_die();
    // No return
}

// Return an AJAX error status
function kcsg_kp_return_fail( $text ) {
    echo json_encode( array( 'status' => 'fail', 'message' => "<span style='color: red;'>" . esc_html( $text ) . "</span>" ) );
    wp_die();
    // No return
}

// Fetch whatever we'll need to display the page
function kcsg_kp_fetch_page( $given_url, $mode ) {
    if ( false !== strpos( $given_url, '.kartra.com/page/embed/' ) ) {
	// Fetch the page loader if given the page-loader URL
	$loader = '';
	// NB: PHP-recommended urlencode does NOT work!
	$loader = @file_get_contents( esc_url_raw( $given_url ) );

	// Extract/sanitize/validate the embedded-page URL from the page loader
	if ( false === $loader || ! preg_match( "/= '(https:[_a-z0-9\/.-]+)'/i", $loader, $matches ) ) return '';
	$url = sanitize_kartra_page_url( $matches[ 1 ] );
	if ( ! is_kartra_page_url( $url ) ) return '';
    } else {
	// Proceed with the given URL if it's not a page-loader URL
	$url = $given_url;
    }

    // By this point, we need the embedded-page URL specifically
    if ( false === strpos( $url, '.kartra.com/page_embed/' ) ) return '';

    if ( 'script' === $mode ) {
	/*
	 * We'll be using our custom page-loader script, so all we need
	 * to save is the embedded-page URL.
	 */
	return "LOAD $url";
    }

    /*
     * Fetch the Kartra page HTML for Kartra Download mode. No validation.
     * No sanitization. No escaping. No holodeck safety protocols. Just the
     * raw HTML they would get directly from Kartra... with one exception.
     */
    $page = '';
    $page = @file_get_contents( esc_url_raw( $url ) );
    if ( false === $page ) return '';

    // Return the page with locally-configured WordPress site icons
    return preg_replace( '/<link[^>]+rel=.(?:shortcut )?icon[^>]+>/', kcsg_kp_site_icons(), $page );
}

// Capture the site icon tags as a string
function kcsg_kp_site_icons() {
    ob_start();
    wp_site_icon();
    return trim( ob_get_clean() );
}

# END
