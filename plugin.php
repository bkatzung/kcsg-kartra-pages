<?php
/*
 * Plugin Name: KCSG Kartra Pages
 * Plugin URI: http://t1.kappacs.com/kcsg-kp
 * Description: Display Kartra pages on your WordPress site
 * Version: 0.1.2
 * Author: Brian Katzung, Kappa Computer Solutions, LLC <briank@kappacs.com>
 * Copyright: 2019 by Brian Katzung and Kappa Computer Solutions, LLC
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: kcsg-kartra-pages
 * Domain Path: /languages
 */

if ( is_admin() ) {
    include( dirname( __FILE__ ) . '/admin.php' );
}

/*
 * Hook in our special page template.
 */
add_action( 'plugins_loaded', 'kcsg_kp_init' );

function kcsg_kp_init () {
    add_filter( 'theme_page_templates', 'kcsg_kp_add_template' );
    add_filter( 'template_include', 'kcsg_kp_include_template' );
    load_plugin_textdomain( 'kcsg-kartra-pages', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

function kcsg_kp_add_template( $templates ) {
    return array_merge( $templates, array( 'kcsg-kartra-page.php' => __( 'KCSG Kartra Page', 'kcsg-kartra-pages' ) ) );
}

function kcsg_kp_include_template( $template ) {
    if ( is_singular() ) {
	$assigned = get_post_meta( get_the_ID(), '_wp_page_template', true );
	if ('kcsg-kartra-page.php' == $assigned ) {
	    return wp_normalize_path( plugin_dir_path( __FILE__ ) . '/' . $assigned );
	}
    }

    return $template;
}
