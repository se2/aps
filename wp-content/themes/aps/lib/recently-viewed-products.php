<?php
/*
Plugin Name: WooCommerce - Recently Viewed Products
Plugin URL: http://remicorson.com/
Description: Adds a "recently viewed products" shortcode
Version: 1.0
Author: Remi Corson
Author URI: http://remicorson.com
Contributors: corsonr
Text Domain: rc_wc_rvp
Domain Path: languages
*/

/**
 * Register the [woocommerce_recently_viewed_products per_page="5"] shortcode
 *
 * This shortcode displays recently viewed products using WooCommerce default cookie
 * It only has one parameter "per_page" to choose number of items to show
 *
 * @access      public
 * @since       1.0
 * @return      $content
 */
function rc_woocommerce_recently_viewed_products( $atts, $content = null ) {

    // Get shortcode parameters
    extract(shortcode_atts(array(
        "per_page" => '5'
    ), $atts));

    // Get WooCommerce Global
    global $woocommerce;

    // Get recently viewed product cookies data
    $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
    $viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

    // If no data, quit
    if ( empty( $viewed_products ) )
        return __( 'You have not viewed any product yet!', 'rc_wc_rvp' );

    // Create the object
    ob_start();

    // Get products per page
    if( !isset( $per_page ) ? $number = 5 : $number = $per_page )

        // Create query arguments array
        $query_args = array(
            'posts_per_page' => $number,
            'no_found_rows'  => 1,
            'post_status'    => 'publish',
            'post_type'      => 'product',
            'post__in'       => $viewed_products,
            'orderby'        => 'rand'
        );

    // Add meta_query to query args
    $query_args['meta_query'] = array();

    // Check products stock status
    $query_args['meta_query'][] = $woocommerce->query->stock_status_meta_query();

    // Create a new query
    $r = new WP_Query($query_args);

    // If query return results
    if ( $r->have_posts() ) {

        $content = '';

        // Start the loop
        while ( $r->have_posts()) {
            $r->the_post();
            global $product;

            $content .= "<div id='product-" . $r->post->ID . "' class='desktop-2 tablet-1 mobile-half'>
                            <div class='image'>
                                <a href='' class='cy'>
                                    <img src='" . wp_get_attachment_image_src( get_post_thumbnail_id( $r->post->ID ), 'medium' )[0] . "' />
                                </a>
                            </div>
                        </div>";
        }

    }

    // Get clean object
    $content .= ob_get_clean();

    // Return whole content
    return $content;
}

// Register the shortcode
add_shortcode("woocommerce_recently_viewed_products", "rc_woocommerce_recently_viewed_products");