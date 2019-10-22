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

    // Localizations
    $mode_section = esc_html__( 'KCSG Kartra Page Template Mode', 'kcsg-kartra-pages' );
    $source_or_url = esc_html__( 'Source script or URL', 'kcsg-kartra-pages' );
    $apply = esc_html__( 'Apply', 'kcsg-kartra-pages' );
    $save_first = esc_js( __( 'Please save draft or publish first.', 'kcsg-kartra-pages' ) );
    $processing = esc_js( __( 'Processing request...', 'kcsg-kartra-pages' ) );
    $request_failed = esc_js( __( 'Request failed', 'kcsg-kartra-pages' ) );

    $esc_url = esc_url($url); // Might be necesary in the future?
    echo <<<HTML
<p>$mode_section</p>
<div id='kcsg_kp_page_modes'>$page_modes</div>
<p><label for='kcsg_kp_source'>$source_or_url</label></p>
<p><input type='text' id='kcsg_kp_source' name='kcsg_kp_source' value='$esc_url' style='width: 90%;'></p>
<p><button id='kcsg_kp_apply' style='margin-right: 1em;'>$apply</button> <span id='kcsg_kp_message'></span></p>
<script>
function kcsgKpApplied (jr) {
    // console.log('Reply is ' + jr);
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

    /*
     * WordFence gets nervous about seeing HTML in a POST parameter unless
     * you whitelist the call, so fix <script> sources on the client side
     * in order to work out of the box in more places with less effort.
     */
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
	  __("Page %s not found", 'kcsg-kartra-pages' ),
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

    $new_mode = isset( $_POST[ 'kcsg_kp_page_mode' ] ) ? $_POST[ 'kcsg_kp_page_mode' ] : '';
    $new_source = isset( $_POST[ 'kcsg_kp_source' ] ) ? $_POST[ 'kcsg_kp_source' ] : '';

    // Validate the template page mode
    switch ( $new_mode ) {
    case 'blank':
    case 'script':
    case 'cache':
	break;

    default:
	kcsg_kp_return_fail( __( 'Please select a template mode', 'kcsg-kartra-pages' ) );
    }

    if ( empty( $new_source ) ) {
	if ( 'blank' != $new_mode ) {
	    // We need a source for script/live or cache/download mode
	    kcsg_kp_return_fail( __( 'Please supply a source', 'kcsg-kartra-pages' ) );
	} else {
	    $new_url = '';
	}
    } else if ( preg_match( '/https:[_a-z0-9\/.-]+/i', $new_source, $matches ) ) {
	// We have simplistic validation of a non-empty source
	$new_url = $matches[ 0 ];
    } else {
	kcsg_kp_return_fail( __( 'Invalid source', 'kcsg-kartra-pages' ) );
    }

    // Refresh or clear the cache
    if ( 'cache' == $new_mode ) {
	$new_cache = kcsg_kp_fetch_page( $new_url );
	if ( empty( $new_cache ) ) {
	    kcsg_kp_return_fail( sprintf( __('No contents found at %s', 'kcsg-kartra-pages' ), $new_url ) );
	}
	update_post_meta( $post_id, 'kcsg_kp_cache', kcsg_kp_meta_encode( $new_cache ) );
    } else {
	delete_post_meta( $post_id, 'kcsg_kp_cache' );
    }

    update_post_meta( $post_id, 'kcsg_kp_url', $new_url );
    update_post_meta( $post_id, 'kcsg_kp_page_mode', $new_mode );
    kcsg_kp_return_done( $new_mode, $new_url, __( 'Request complete', 'kcsg-kartra-pages' ) );
}

/*
 * Return post_meta-safe encoding (decode with rawurldecode)
 * % => %25, \ => %5C
 * so that "strings \"like this\"" don't become "strings "like this""
 * in MySQL and on the final page (ouch).
 */
function kcsg_kp_meta_encode( $text ) {
    return str_replace( array( '%', '\\' ), array( '%25', '%5C' ), $text );
}

// Return an AJAX success status
function kcsg_kp_return_done( $mode, $url, $text ) {
    $modes = kcsg_kp_page_modes( $mode );
    echo json_encode( array( 'status' => 'done', 'pageModes' => $modes, 'source' => $url, 'message' => esc_html( $text ) ) );
    wp_die();
}

// Return an AJAX error status
function kcsg_kp_return_fail( $text ) {
    echo json_encode( array( 'status' => 'fail', 'message' => "<span style='color: red;'>" . esc_html( $text ) . "</span>" ) );
    wp_die();
}

// Fetch the page contents by page loader URL
function kcsg_kp_fetch_page( $loader_url ) {
    // Step 1: Fetch the page loader (JavaScript) if the URL is valid
    if ( false == strpos( $loader_url, '.kartra.com/page/embed/' ) ) return '';
    $script = @file_get_contents( $loader_url );

    // Step 2: Fetch the page HTML if we find a valid URL in the page loader
    if ( ! preg_match( "/= '(https:[_a-z0-9\/.-]+)'/i", $script, $matches ) ) return '';
    $url = $matches[ 1 ];
    if ( false == strpos( $url, '.kartra.com/page_embed' ) ) return '';
    $page = @file_get_contents( $url );

    /*
     * Kartra always includes one shortcut icon link referencing the Kartra-
     * brand favicon (except for Kartra-served pages configured for a custom
     * domain URL and favicon). For here, we'll display the page with the
     * locally-configured icons.
     */
    return preg_replace( '/<link[^>]+rel=.(?:shortcut )?icon[^>]+>/', kcsg_kp_site_icons(), $page );
}

// Capture the site icon tags as a string
function kcsg_kp_site_icons() {
    ob_start();
    wp_site_icon();
    return trim( ob_get_clean() );
}

# END
