<?php
include_once('inc/inc.php');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// define the woocommerce_cart_item_quantity callback
function filter_woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
    $product_quantity = str_replace('<label for="quantity">Quantity</label>', '', $product_quantity);
    return $product_quantity;
};
add_filter( 'woocommerce_cart_item_quantity', 'filter_woocommerce_cart_item_quantity', 10, 3 );

if ( ! function_exists( 'vg_recently_viewed_product' ) ) {
    function vg_recently_viewed_product() {
        if ( ! is_singular( 'product' ) ) {
            return;
        }

        global $post;

        if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
            $viewed_products = array();
        else
            $viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

        if ( ! in_array( $post->ID, $viewed_products ) ) {
            $viewed_products[] = $post->ID;
        }

        if ( sizeof( $viewed_products ) > 15 ) {
            array_shift( $viewed_products );
        }

        // Store for session only
        wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
    }
    add_action( 'template_redirect', 'vg_recently_viewed_product' );
}

// remove Payment form on check out page
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

// remove the "woocommerce_get_sidebar" action
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

// remove the "woocommerce_get_sidebar" action
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// remove the "woocommerce_get_sidebar" action
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// remove the "woocommerce_result_count" action
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

function change_post_status($post_id, $status) {
	$current_post = get_post( $post_id, 'ARRAY_A' );
	$current_post['post_status'] = $status;

	wp_update_post($current_post);
}

// add event when remove product
function reEstimateShippingFee() {

}
add_filter('woocommerce_cart_item_removed', 'reEstimateShippingFee', 10);


if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}

function vgAlertMe($arrData) {

    $to = 'giangcamvinh@gmail.com';
    $subject = '[] ALERT';

    if(is_user_logged_in()) {
        $userInfo = wp_get_current_user();

        $arrData['username'] = $userInfo->user_login;
        $arrData['email'] = $userInfo->user_email;
    }

	$arrData['ip'] = getClientIP();

	$body = getTemplateEmail($arrData, '/email/alert.html');
	$headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail( $to, $subject, $body, $headers );
}

/**
 * Auto login after register
 * */
/*function auto_login_new_user( $user_id ) {
	wp_set_current_user($user_id);
	wp_set_auth_cookie($user_id);
}
add_action( 'user_register', 'auto_login_new_user' );*/


function vg_upload_dir( $dirs ) {
	$dirs['subdir'] = '/import';
	$dirs['path']   = $dirs['basedir'] . '/import';
	$dirs['url']    = $dirs['baseurl'] . '/import';

	return $dirs;
}

function my_cust_filename( $dir, $name, $ext ) {
	$newFileName = date( 'Ymdhis' ) . rand( 100, 999 ) . '.xls';

	return $newFileName;
}

/**
 * Add setting page
 * */
/*add_action( 'admin_menu', 'vg_custom_admin_menu' );
function vg_custom_admin_menu() {
	add_options_page(
		'Shop Setting',
		'Shop Setting',
		'manage_options',
		'vg_option',
		'vg_option_page'
	);
}

function vg_option_page() {
	uiwp_get_template('settings/options.php');
}

if ( is_admin() ){ // admin actions
	add_action( 'admin_init', 'register_mysettings' );
}
function register_mysettings() { // whitelist options
	register_setting( 'vg_option_group', 'fuelsurcharge' );
	register_setting( 'vg_option_group', 'profit' );
	register_setting( 'vg_option_group', 'exchangerate' );
	register_setting( 'vg_option_group', 'remoteareasurcharge' );
}*/

/** END add setting page */


/**
 * remove the register link from the wp-login.php script
 */
add_filter('option_users_can_register', function($value) {
	$script = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));

	if ($script == 'wp-login.php') {
		$value = false;
	}

	return $value;
});


add_action('wp_enqueue_scripts', 'vinhgiang_style');
function vinhgiang_style() {

	wp_register_style('stylesheet-style', THEME_URL . 'css/stylesheet.css');
	wp_enqueue_style('stylesheet-style');

	wp_register_style('font-awesome-style', THEME_URL . 'css/font-awesome.css');
	wp_enqueue_style('font-awesome-style');

	wp_register_style('queries-style', THEME_URL . 'css/queries.css');
	wp_enqueue_style('queries-style');

	wp_register_style('additional-checkout-buttons-style', THEME_URL . 'css/additional-checkout-buttons.css');
	wp_enqueue_style('additional-checkout-buttons-style');

    wp_register_style('lato-google-font', '//fonts.googleapis.com/css?family=Lato:300,400,700');
    wp_enqueue_style('lato-google-font');

    wp_register_style('montserrat-google-font', '//fonts.googleapis.com/css?family=Montserrat:400,700');
    wp_enqueue_style('montserrat-google-font');

    wp_register_style('roboto-google-font', '//fonts.googleapis.com/css?family=Roboto:400,500,300');
    wp_enqueue_style('roboto-google-font');

    wp_register_style('josefin-google-font', '//fonts.googleapis.com/css?family=Josefin+Sans:400,600,700,300');
    wp_enqueue_style('josefin-google-font');

    wp_register_style('source-google-font', '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,300,700');
    wp_enqueue_style('source-google-font');

	global $wp_styles;

	wp_enqueue_style( 'gridlock', THEME_URL . 'css/gridlock.ie.css' );
	$wp_styles->add_data( 'gridlock', 'conditional', 'LTE IE 8' );

	wp_enqueue_style( 'font', THEME_URL . 'css/font-awesome-ie7.css' );
	$wp_styles->add_data( 'font', 'conditional', 'IE 7' );

	// Embed after Jquery

	wp_register_script('theme-script', THEME_URL . 'js/theme.js', array('jquery'), '1.0', false);
	wp_enqueue_script('theme-script');

	wp_register_script('option_selection-script', THEME_URL . 'js/option_selection.js', array('jquery'), '1.0', false);
	wp_enqueue_script('option_selection-script');

	wp_register_script('handlebars-script', THEME_URL . 'js/handlebars.js', array('jquery'), '1.0', false);
	wp_enqueue_script('handlebars-script');

	wp_register_script('shifter-script', THEME_URL . 'js/jquery.fs.shifter.js', array('jquery'), '1.0', false);
	wp_enqueue_script('shifter-script');

	wp_register_script('instafeed-script', THEME_URL . 'js/instafeed.js', array('jquery'), '1.0', false);
	wp_enqueue_script('instafeed-script');

    wp_enqueue_style( 'html5shim', '//html5shim.googlecode.com/svn/trunk/html5.js' );
    $wp_styles->add_data( 'html5shim', 'conditional', 'lt IE 9' );

}

// Change default jQuery
add_action('init', 'modify_jquery');
function modify_jquery() {
	if (!is_admin()) {
		// comment out the next two lines to load the local copy of jQuery
		wp_deregister_script('jquery');
		//wp_register_script('jquery', THEME_URL . '/js/jquery.js', false, '2.1.4');
		wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', false);
		wp_enqueue_script('jquery');
	}
}

// Your theme does not declare WooCommerce support
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

/**
 * Add image size
 */
/*add_action( 'after_setup_theme', 'themeSetupImgSize' );
function themeSetupImgSize() {
	add_image_size( 'product-thumb', 200, 220, true ); // (cropped)
	add_image_size( 'product-xs-thumb', 80, 80, true ); // (cropped)
}*/

/**
 * Remove menu
 */
/*add_action( 'admin_menu', 'remove_menus' );
function remove_menus(){
    remove_menu_page( 'index.php' );                  //Dashboard
    //remove_menu_page( 'edit.php' );                   //Posts
    remove_menu_page( 'upload.php' );                 //Media
    //remove_menu_page( 'edit.php?post_type=page' );    //Pages
    remove_menu_page( 'edit-comments.php' );          //Comments
    //remove_menu_page( 'themes.php' );                 //Appearance
    //remove_menu_page( 'plugins.php' );                //Plugins
    //remove_menu_page( 'users.php' );                  //Users
    //remove_menu_page( 'tools.php' );                  //Tools
    //remove_menu_page( 'options-general.php' );        //Settings
}*/

/**
 * Hide donate form Post Types Order
 */
add_action('admin_head', 'custom_colors');
function custom_colors() {
    echo '<style type="text/css">
            #cpt_info_box {
                display: none;
            }
         </style>';
}

// Get attachment data
function vg_get_attachment( $attachment_id ) {
    $attachment = get_post( $attachment_id );
    return array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'caption' => $attachment->post_excerpt,
        'description' => $attachment->post_content,
        'href' => get_permalink( $attachment->ID ),
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    );
}


// add_action("admin_menu", "setup_theme_admin_menus");
// function setup_theme_admin_menus() {
//     // add_submenu_page('themes.php',
//     //     'Front Page Elements', 'Front Page', 'manage_options',
//     //     'front-page-elements', 'theme_front_page_settings');
//     add_theme_page( 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
// }
// function theme_front_page_settings() {
//     echo "Hello, world!";
// }

/**
 * Add background controller to theme setting
*/
// $defaults = array(
// 	'default-color'          => '',
// 	'default-image'          => '',
// 	'wp-head-callback'       => '_custom_background_cb',
// 	'admin-head-callback'    => '',
// 	'admin-preview-callback' => ''
// );
// add_theme_support( 'custom-background', $defaults );


/*add_action('after_setup_theme', 'theme_setup_language_directory');
function theme_setup_language_directory (){
    load_theme_textdomain('scvivocity', get_template_directory(). '/languages');
}*/

/**
 * Register session
 */
if ( !session_id() )
    add_action( 'init', 'session_start' );


/**
 * Admin login page logo
*/
add_action( 'login_enqueue_scripts', 'my_login_logo' );
function my_login_logo() {
	?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo THEME_URL ?>/images/logo.png);
			background-size: 100%;
			/*height: 170px;*/
			width: 220px;
			/*margin: -60px auto -27px;*/
        }
    </style>
	<?php
}

/**
 * Remove Wordpress Logo
*/
add_action('wp_before_admin_bar_render', 'vg_admin_bar_remove', 0);
function vg_admin_bar_remove() {
	global $wp_admin_bar;

	/* Remove their stuff */
	$wp_admin_bar->remove_menu('wp-logo');
}

/**
 *  Remove plugin update
 */
//remove_action('load-update-core.php','wp_update_plugins');
//add_filter('pre_site_transient_update_plugins','__return_null');

/**
 * Remove woocommerce breadcrumbs
*/
add_action( 'init', 'vg_remove_wc_breadcrumbs' );
function vg_remove_wc_breadcrumbs() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}


/**
 * Add menu to theme
*/
add_theme_support('menus');
if ( function_exists( 'register_nav_menus' ) ) {
	register_nav_menus(
		array(
			'primary' => 'Primary Navigation',
			'secondary' => 'Secondary Navigation',
			'tertiary' => 'Tertiary Navigation'
		)
	);
}

/**
 * Get Menu name by location
*/
if ( ! function_exists( 'get_menu_name_by_location' ) ) {
    function get_menu_name_by_location($location) {
        $locations = get_nav_menu_locations();
        $menu = wp_get_nav_menu_object($locations[$location]);
        return $menu->name;
    }
}

/**
 * Get Menu by name
*/
if ( ! function_exists( 'get_menu_by_name' ) ) {
    function get_menu_by_name($name) {
        $menu = wp_get_nav_menu_items($name);
        $result = buildTree($menu);
        return $result;
    }
}

function buildTree( $ar, $pid = 0 ) {
    $op = array();
    foreach( $ar as $item ) {
        $item = (array) $item;
        if( $item['menu_item_parent'] == $pid ) {
            $op[$item['ID']] = array(
                'title' => $item['title'],
                'menu_item_parent' => $item['menu_item_parent'],
                'url' => $item['url']
            );
            // using recursion
            $children =  buildTree( $ar, $item['ID'] );
            if( $children ) {
                $op[$item['ID']]['children'] = $children;
            }
        }
    }
    return $op;
}

/**
 * Get email template
 *
 * @param array $arrData
 * @param string $filePath
 *
 * @return mixed|string
 */
function getTemplateEmail($arrData , $filePath = '/email/contact.html'){
    $strContent = "";

	if(file_exists(get_template_directory().$filePath)){
		$fp = fopen(get_template_directory().$filePath,"r");
		$strContent = fread($fp, filesize(get_template_directory().$filePath));
		fclose($fp);

		if(count($arrData) > 0) {
			foreach ($arrData as $key => $value) {
				$strContent = str_replace('{'.$key.'}', $value, $strContent);
			}
		}
	}
	return $strContent;
}

if( !function_exists( 'uiwp_get_template' ) ) {
	/**
	* Retrieve a template file.
	*
	* @param string $path
	* @param mixed $var
	* @param bool $return
	* @return void
	* @since 1.0.0
	*/
	function uiwp_get_template( $path, $var = null, $return = false ) {
		$located = get_theme_root().'/'.get_template().'/'.$path;
		if ( $var && is_array( $var ) )
			extract( $var );

		if( $return )
			{ ob_start(); }

	// include file located
		include( $located );

		if( $return )
			return ob_get_clean();
	}
}