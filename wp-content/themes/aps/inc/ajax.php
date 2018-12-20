<?php

/**

 * Created by PhpStorm.

 * User: vinhgiang

 * Date: 20/03/2015

 * Time: 2:14 CH

 */

add_action('wp_ajax_nopriv_request_cancel_order', 'requestCancelOrder');
add_action('wp_ajax_request_cancel_order', 'requestCancelOrder');
function requestCancelOrder() {

    $orderId = intval($_POST['order']);
    $submitted_value = $_POST['_wpnonce'];
    $msg = htmlentities(strip_tags(trim($_POST['msg'])), ENT_QUOTES, 'UTF-8' );
    $reason = htmlentities(strip_tags(trim($_POST['reason'])), ENT_QUOTES, 'UTF-8' );

    $result = array(
        'code' => 0,
        'msg' => 'There are something wrong, please try again later. Thank you.'
    );

    if(wp_verify_nonce( $submitted_value, 'protectRequestCancelOrder' )){

        if($orderId > 0) {

            $current_user = wp_get_current_user();
            $userId = intval(get_current_user_id());
            $ownerId = get_post_meta($orderId, '_customer_user', true);

            if($userId <= 0) {
                $result['msg'] = 'Please login to use this feature. Thank you.';
            }
            else if($userId != $ownerId) {
                $result['msg'] = 'Invalid order.';
            }
            else if($msg == '') {
                $result['msg'] = 'Error code: 0x013';
            }
            else {
                $data = array(
                    'comment_author' => $current_user->user_login,
                    'comment_post_ID' => $orderId,
                    'comment_content' => 'Request Cancel - ' . $reason . ' - ' . $msg,
                    'user_id' => $userId,
                    'comment_author_IP' => getClientIP(),
                    'comment_approved' => 1,
                    'comment_type' => 'order_note'
                );

                wp_insert_comment($data);

                $result['code'] = 1;
            }
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_nopriv_add_to_wish_list', 'addToWishList');
add_action('wp_ajax_add_to_wish_list', 'addToWishList');
function addToWishList() {

    $proId = intval($_POST['proId']);
    $submitted_value = $_POST['_wpnonce'];

    $result = array(
        'code' => 0,
        'msg' => 'There are something wrong, please try again later. Thank you.'
    );

    if(wp_verify_nonce( $submitted_value, 'protectAddToWishList' )){
        $userId = intval(get_current_user_id());

        if($proId > 0) {

            if($userId <= 0) {
                $result['msg'] = 'Please login to use this feature. Thank you.';
            } else {

                global $wpdb;

                $isProExist = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $wpdb->prefix" . "vg_wishlist WHERE user_id = '%d' AND pro_id = '%d'", array($userId, $proId) ));
                if($isProExist) {
                    $result['msg'] = 'This product have already existed in your wish list.';
                }
                else {
                    $arrData = array(
                        'user_id' => $userId,
                        'pro_id' => $proId,
                        'created' => current_time('Y-m-d h:i:s'),
                        'ip' => getClientIP()
                    );

                    $wpdb->insert($wpdb->prefix . 'vg_wishlist', $arrData);
                    $wishId = $wpdb->insert_id;

                    $result['code'] = 1;
                }
            }
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_nopriv_edit_profile', 'editProfile');
add_action('wp_ajax_edit_profile', 'editProfile');
function editProfile() {

    $firstName = strip_tags(trim($_POST['first-name']));
    $lastName = strip_tags(trim($_POST['last-name']));
    $phone  =   wp_strip_all_tags( $_POST['phone'] );
    $phoneCountryCode  =   wp_strip_all_tags( $_POST['phone-country-code'] );
    $submitted_value = $_POST['_wpnonce'];

    $result = array(
        'code' => 0,
        'msg' => 'There are something wrong, please try again later. Thank you.'
    );

    if ( ! $firstName) {
        $response['message'] = 'Error code: 0x049';
    }
    if ( ! $lastName) {
        $response['message'] = 'Error code: 0x050';
    }

    if( ! $phone) {
        $response['message'] = 'Error code: 0x005';
    }
    if( ! $phoneCountryCode) {
        $response['message'] = 'Error code: 0x032';
    }
    else if($phoneCountryCode <= 0) {
        $response['message'] = 'Error code: 0x033';
    }

    if(empty($response['message'])) {

        if ( wp_verify_nonce( $submitted_value, 'edit_profile' ) ) {

            $userId = get_current_user_id();

            update_user_meta( $userId, 'first_name', $firstName );
            update_user_meta( $userId, 'last_name', $lastName );

            update_user_meta( $userId, 'billing_first_name', $firstName );
            update_user_meta( $userId, 'billing_last_name', $lastName );

            update_user_meta( $userId, 'shipping_first_name', $firstName );
            update_user_meta( $userId, 'shipping_last_name', $lastName );

            update_user_meta( $userId, 'billing_phone', '+' . $phoneCountryCode . '-' . $phone );

            $result['code'] = 1;
        }
    }

    die(json_encode($result));
}

add_action('wp_ajax_nopriv_estimate_shipping', 'estimateShipping');
add_action('wp_ajax_estimate_shipping', 'estimateShipping');
function estimateShipping() {

    $country = intval($_POST['countryId']);
    $zip = trim($_POST['zip']);

    $submitted_value = $_POST['_wpnonce'];

    $result = array(
        'code' => 0,
        'msg' => 'There are something wrong, please try again later. Thank you.'
    );

    if(wp_verify_nonce( $submitted_value, 'estimate_shipping' )){

        global $woocommerce, $exchangeRate;

        $countryInfo = get_post($country);
        $countryName = $countryInfo->post_title;

        $zone = wp_get_post_terms($country, 'zone');

        if(count($zone) <= 0) {

            $result['msg'] = 'Zone not found';
        }
        else {
            $packages = get_field('package', 'zone_' . $zone[0]->term_id);

            if(count($packages) <= 0) {

                $result['msg'] = 'Package not found';
            }
            else {
                $arrPrices = array();
                foreach ($packages as $package) {
                    $arrPrices[$package['kgs']] = $package['prices'];
                }

                ksort($arrPrices);

                $totalCartWeight = $woocommerce->cart->cart_contents_weight;
                $totalCartWeight = $totalCartWeight < 1 ? 1 : $totalCartWeight;
                foreach ($arrPrices as $weight => $prices) {
                    if($totalCartWeight <= $weight) {
                        $shippingCost = $prices;
                        break;
                    }
                }

                $profit = get_option('profit');
                $shippingCost += $shippingCost * $profit / 100;
                //$shippingCost = $shippingCost / $exchangeRate;
                $shippingCost = $shippingCost / 9.7;
                $shippingCost = round($shippingCost, 2);

                $result = array(
                    'code' => 1,
                    'price' => $shippingCost
                );

                $_SESSION['countryName'] = $countryName;
                $_SESSION['countryId'] = $country;
                $_SESSION['zip'] = $zip;
                $_SESSION['estimateShipping'] = $shippingCost;

                $countries_obj = new WC_Countries();
                $allowedCountries = $countries_obj->get_allowed_countries();

                $_SESSION['countryCode'] = array_search($countryName, $allowedCountries);

                /*global $woocommerce;

                //set it
                $woocommerce->customer->set_shipping_postcode( $zip );
                $woocommerce->customer->set_shipping_country( 'czech republic' );*/

                $_SESSION['priceTable'] = $arrPrices;

                die(json_encode($result));
            }
        }
    }
    else {
        write_log('_wpnonce not match');
    }

    die(json_encode($result));
}

add_action('wp_ajax_nopriv_vg_logout', 'submitLogout');
add_action('wp_ajax_vg_logout', 'submitLogout');
function submitLogout() {

    $submitted_value = $_GET['_wpnonce'];

    if(wp_verify_nonce( $submitted_value, 'security_log_out' )){

        wp_logout();
        wp_redirect( home_url() );
    }
}

add_action('wp_ajax_nopriv_vg_login', 'submitLogin');
add_action('wp_ajax_vg_login', 'submitLogin');
function submitLogin() {

    $submitted_value = $_POST['_wpnonce'];

    if(wp_verify_nonce( $submitted_value, 'vg_login' )){

        $response = array(
            'status' => 0
        );

        $username   =   sanitize_user( $_POST['user-name'] );
        $password   =   esc_attr( $_POST['password'] );

        if (4 > strlen($username)) {
            //$reg_errors->add('username_length', 'Username too short. At least 4 characters is required');
            $response['message'] = 'Error code: 0x048';
        }

        if ( $password ) {
            $response['message'] = 'Error code: 0x029';
        }

        // process login automatically
        $loginResult = wp_signon();
        if($loginResult->get_error_code()){
            $response['message'] = 'Your username or password is not correct.';
        }else {
            $response['status'] = 1;
        }

        print_r(json_encode($response)); exit;
    }
}

add_action('wp_ajax_nopriv_vg_registration', 'submitRegistration');
add_action('wp_ajax_vg_registration', 'submitRegistration');
function submitRegistration(){

    $submitted_value = $_POST['_wpnonce'];

    if(wp_verify_nonce( $submitted_value, 'protectVgRegistration' )){

        $response = array(
            'status' => 0
        );

        $first_name =   wp_strip_all_tags( $_POST['first-name'] );
        $last_name =   wp_strip_all_tags( $_POST['last-name'] );
        $username   =   sanitize_user( wp_strip_all_tags($_POST['user-name']) );
        $email      =   sanitize_email( $_POST['email'] );
        $gender =   intval( $_POST['gender'] );
        $password   =   esc_attr( $_POST['password'] );
        $countryCode    =   wp_strip_all_tags( $_POST['country-code'] );
        $phone  =   wp_strip_all_tags( $_POST['phone'] );
        $phoneCountryCode  =   wp_strip_all_tags( $_POST['phone-country-code'] );
	    $subscription = isset( $_POST['subscription'] ) ? '1' : '0';

        // Use error class to manage error
        /*global $reg_errors;
        $reg_errors = new WP_Error*/;

        if ( ! $first_name) {
            $response['message'] = 'Error code: 0x049';
        }
        if ( ! $last_name) {
            $response['message'] = 'Error code: 0x050';
        }
        if (4 > strlen($username)) {
            //$reg_errors->add('username_length', 'Username too short. At least 4 characters is required');
            $response['message'] = 'Error code: 0x048';
        }
        if (username_exists($username)) {
            $response['message'] = 'Sorry, that username already exists!';
        }
        if ( ! validate_username( $username ) ) {
            $response['message'] = 'Error code: 0x048';
        }
        if ( ! is_email( $email ) ) {
            $response['message'] = 'Error code: 0x007';
        }
        if ( email_exists( $email ) ) {
            $response['message'] = 'Sorry, that email address already exists!';
        }
        if ( 8 > strlen( $password ) ) {
            $response['message'] = 'Error code: 0x029';
        }
        if( ! $countryCode) {
            $response['message'] = 'Error code: 0x025';
        }
        if( ! $phone) {
            $response['message'] = 'Error code: 0x005';
        }
        if( ! $phoneCountryCode) {
            $response['message'] = 'Error code: 0x032';
        }
        else if($phoneCountryCode <= 0) {
            $response['message'] = 'Error code: 0x033';
        }

        $countries_obj = new WC_Countries();
        $allowedCountries = $countries_obj->get_allowed_countries();

        if($allowedCountries[$countryCode] == '') {
            $response['message'] = 'sorry, it seems your country is not supported by us.';

            $arrData = array(
                'from' => 'register from',
                'alert_msg' => $response['message'],
                'username' => $username,
                'email' => $email,
            );
            vgAlertMe($arrData);
        }

        /*if(is_plugin_active('vg-captcha/vg-captcha.php')){
            if($captcha != $_SESSION['vg_captcha']){
                $response['message'] = 'Your captcha code is not valid.'.$captcha.' - '.$_SESSION['vg_captcha'];
                $response['status'] = -1;
            }
        }*/

        // check all data valid or not by wp_error class
        /*if ( is_wp_error( $reg_errors ) ) {
            $error = $reg_errors->get_error_messages();
        }*/

        if(empty($response['message'])){

            $userData = array(
                'first_name'    =>   $first_name,
                'last_name'     =>   $last_name,
                'user_login'    =>   $username,
                'user_email'    =>   $email,
                'user_pass'     =>   $password,
                'country'       =>	 $countryCode,
            );
            $userId = wp_insert_user( $userData );

            if($userId > 0){

                update_user_meta( $userId, 'gender', $gender );
	            update_user_meta( $userId, 'subscription', $subscription );

                update_user_meta( $userId, 'billing_first_name', $first_name );
                update_user_meta( $userId, 'billing_last_name', $last_name );
                update_user_meta( $userId, 'billing_country', $countryCode );
                update_user_meta( $userId, 'billing_email', $email );
                update_user_meta( $userId, 'billing_phone', '+' . $phoneCountryCode . '-' . $phone );

                update_user_meta( $userId, 'shipping_first_name', $first_name );
                update_user_meta( $userId, 'shipping_last_name', $last_name );
                update_user_meta( $userId, 'shipping_country', $countryCode );

                $response['status'] = 1;

                $userData['gender'] = $gender;
                $userData['phone'] = '+' . $phoneCountryCode . '-' . $phone;
	            $userData['subscription'] = $subscription;

                $to = 'giangcamvinh@gmail.com';
                $subject = '[Technese] New Member';
                $body = getTemplateEmail($userData, '/email/new-member.html');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $subject, $body, $headers );
            }else{
                $response['message'] = 'System error! Please try again.';
            }
        }
        print_r(json_encode($response)); exit;
    }
}

add_action('wp_ajax_nopriv_vg_contact', 'submitContact');
add_action('wp_ajax_vg_contact', 'submitContact');
function submitContact(){

    $submitted_value = $_POST['_wpnonce'];

    if(wp_verify_nonce( $submitted_value, 'vg_contact' )) {

        $response = array(
            'status' => 0
        );

        $firstName = wp_strip_all_tags($_POST['first-name']);
        $lastName = wp_strip_all_tags($_POST['last-name']);
        $email = wp_strip_all_tags($_POST['email']);
        $msg = wp_strip_all_tags($_POST['msg']);

        if( ! $firstName) {
            $response['message'] = 'Error code: 0x049';
        }

        if( ! $lastName) {
            $response['message'] = 'Error code: 0x050';
        }

        if( ! $email) {
            $response['message'] = 'Error code: 0x010';
        }
        else if( ! is_email($email)) {
            $response['message'] = 'Error code: 0x007';
        }

        if( ! $msg) {
            $response['message'] = 'Error code: 0x013';
        }

        /*if(is_plugin_active('vg-captcha/vg-captcha.php')){
            if($captcha != $_SESSION['vg_captcha']){
                $response['message'] = 'Your captcha code is not valid.'.$captcha.' - '.$_SESSION['vg_captcha'];
                $response['status'] = -1;
            }
        }*/

        if(empty($response['message'])) {

            $arrData = array(
                'first_name' => $firstName,
                'last_name' => $lastName,
				'email' => $email,
                'message' => $msg,
                'ip' => getClientIP()
            );

            global $wpdb;

            $wpdb->insert($wpdb->prefix . 'contact', $arrData);
            $contactId = $wpdb->insert_id;
            if($contactId > 0) {
                $response['status'] = 1;

				$arrData['website'] = site_url();

				$to = 'sandra@technese.net';
				$subject = '[Technese - Contact] New message';
				$body = getTemplateEmail($arrData, '/email/contact.html');
				$headers = array(
					'Content-Type: text/html; charset=UTF-8',
					'BCC: giangcamvinh@gmail.com'
				);

				wp_mail( $to, $subject, $body, $headers );
            }
            else{
                $response['message'] = 'System error! Please try again.';
            }
        }
        print_r(json_encode($response)); exit;
    }
}

add_action('wp_ajax_nopriv_vg_order', 'submitOrder');
add_action('wp_ajax_vg_order', 'submitOrder');
function submitOrder(){

    if(wp_verify_nonce($_POST['order_form'], 'order')){

        $response = array(
            'status' => 0
        );

        $name = wp_strip_all_tags($_POST['fullname']);
        $phone = wp_strip_all_tags($_POST['cellphone']);
		$email = wp_strip_all_tags($_POST['email']);
        $address = wp_strip_all_tags($_POST['address']);
		$district = wp_strip_all_tags($_POST['district']);
		$province = wp_strip_all_tags($_POST['province']);
        $quantity = intval($_POST['quantity']) + intval($_SESSION['cart']);

        if(!$name){
            $response['message'] = 'Please enter your name.';
        }else if(strlen($name) > 200){
            $response['message'] = 'Your name must be less than 200 character.';
        }

        if(strlen($phone) > 13){
            $response['message'] = 'Your phone number must be less than 13 character.';
        }

		if(!$email){
			$response['message'] = 'Please enter your email address.';
		}else if(strlen($email) > 200){
			$response['message'] = 'Your email must be less than 200 character.';
		}else if(!is_email($email)) {
			$response['message'] = 'Your email is not valid.';
		}

        if(!$address){
            $response['message'] = 'Please enter your address.';
        }else if(strlen($address) > 200){
            $response['message'] = 'Your address must be less than 200 character.';
        }

		if(!$district){
			$response['message'] = 'Please enter your district.';
		}
		if(!$province){
			$response['message'] = 'Please select your province.';
		}

        if($quantity <= 0) {
            $response['message'] = 'Your cart is empty.';
        }


        if(empty($response['message'])){

            $arrData = array(
                'post_type' => 'order-type',
                'post_title' => $name
            );

            $orderId = wp_insert_post($arrData);

            update_field('name', $name, $orderId);
            update_field('phone', $phone, $orderId);
			update_field('email', $email, $orderId);
            update_field('address', $address, $orderId);
			update_field('district', $district, $orderId);
			update_field('province', $province, $orderId);
            update_field('quantity', $quantity, $orderId);
			update_field('ip', getClientIP(), $orderId);

            if($orderId > 0){

                $arrOrderData = array(
					'id' => $orderId,
                    'name' => $name,
                    'website' => site_url(),
                    'phone' => $phone,
					'email' => $email,
                    'address' => $address,
					'district' => $district,
					'province' => $province,
                    'quantity' => $quantity,
                );

				$_SESSION['orderInfo'] = $arrOrderData;

                $to = 'support@myvitajoint.vn';
                $subject = '[order] New order';
                $body = getTemplateEmail($arrOrderData, '/email/order.html');
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $subject, $body, $headers );

                $_SESSION['cart'] = 0;

                $response['status'] = 1;
            }else{
                $response['message'] = 'System error! Please try again.';
            }
        }
        print_r(json_encode($response)); exit;
    }
}

add_action('wp_ajax_nopriv_vg_distributor_ajax_fillter', 'distributorsAjaxFilter');
add_action('wp_ajax_vg_distributor_ajax_fillter', 'distributorsAjaxFilter');
function distributorsAjaxFilter(){
    uiwp_get_template( 'template/distributors-ajax.php' ); exit;
}
