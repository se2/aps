<?php
/**
 * Created by PhpStorm.
 * User: vinhgiang
 * Date: 20/03/2015
 * Time: 3:17 CH
 */

/**
 * Vinh Giang
 * Add feature to editor in CMS
 */
add_filter("mce_buttons_3", "ilc_mce_buttons");
function ilc_mce_buttons($buttons){
	array_push($buttons,
		"backcolor",
		"anchor",
		"hr",
		"sub",
		"sup",
		"fontselect",
		"fontsizeselect",
		"styleselect",
		"cleanup"
	);
	return $buttons;
}

/**
 * Vinh Giang
 * Get meta content format
 */
add_filter( 'meta_content', 'wptexturize'        );
add_filter( 'meta_content', 'convert_smilies'    );
add_filter( 'meta_content', 'convert_chars'      );
add_filter( 'meta_content', 'wpautop'            );
add_filter( 'meta_content', 'shortcode_unautop'  );
add_filter( 'meta_content', 'prepend_attachment' );

/**
 * Vinh Giang
 * Add Query vars
 */
add_filter( 'query_vars', 'add_query_vars_filter' );
function add_query_vars_filter( $vars ){
    $vars[] = "category";
    $vars[] = "nation";
    $vars[] = "id";
    $vars[] = "var";
    return $vars;
}

add_filter( 'posts_where', 'title_like_posts_where', 10, 2 );
function title_like_posts_where( $where, &$wp_query ) {
    global $wpdb;
    if ( $post_title_like = $wp_query->get( 'post_title_like' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $post_title_like ) ) . '%\'';
    }
    return $where;
}

/**
 * Modify the breadcrumb
 */
// add_filter( 'woocommerce_breadcrumb_defaults', 'vg_woocommerce_breadcrumbs' );
// function vg_woocommerce_breadcrumbs() {
// 	return array(
// 		'delimiter'   => ' &#47; ',
// 		'wrap_before' => '<div class="bread-wrap">',
// 		'wrap_after'  => '</div>',
// 		'before'      => '',
// 		'after'       => '',
// 		'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
// 		);
// }


/**
 * Admin login page logo link
 */
add_filter('login_headerurl','loginpage_custom_link');
function loginpage_custom_link() {
    return SITE_URL;
}


/**
 * Admin login page logo title
 */
add_filter('login_headertitle', 'change_title_on_logo');
function change_title_on_logo() {
    return get_bloginfo();
}


/**
 * Search only returns the custom post type.
 * put the '-' before post type to excludes the custom post type
 * ex: -post
 */
//add_filter('pre_get_posts','SearchFilter');
//function SearchFilter($query) {
//    if ($query->is_search) {
//        $query->set('post_type', 'post');
//    }
//    return $query;
//}

/**
 * Override search form
 */
/*add_filter( 'get_search_form', 'my_search_form' );
function my_search_form( $form ) {

    //$lang = '<input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'">';
    $lang = '';

    $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
        <div>
            <input class="textfield_style search-txt" name="s" id="s" type="text" lang="search" placeholder="'.__('Từ khóa', 'vg').'" value="' . get_search_query() . '" >
            ' . $lang . '
            <span class="searchBtn easing" onclick="document.getElementById(\'searchform\').submit();"></span>
            <div id="searchBtn">
                TÌM KIẾM
            </div>
        </div>
	</form>';

    return $form;
}*/


/**
 * =================== Woo Commerce Filter ==================
*/


/**
 * WooCommerce
 * Override page title
 */
add_filter('woocommerce_show_page_title', 'override_page_title');
function override_page_title() {
    return false;
}

/**
 * Woo Commerce
 * Modify the price HTML
 */
//add_filter( 'woocommerce_get_price_html', 'my_price_html', 100, 2 );
//function my_price_html( $price, $product ){
//	$price = str_replace('<del><span class="amount">', '<span class="old-price">', $price);
//	$price = str_replace('</span></del>', '</span>', $price);
//	$price = str_replace('<ins><span class="amount">', '<span class="new-price">', $price);
//	$price = str_replace('</span></ins>', '</span>', $price);
//    return $price;
//}


/**
 * Woo Commerce
 * Display 24 products per page. Goes in functions.php
 */
add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );