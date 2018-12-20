<?php
/**
 * Created by PhpStorm.
 * User: vinhgiang
 * Date: 02/06/2015
 * Time: 6:22 CH
 */

function lang_object_ids($ids_array, $type = 'page') {

	$url = explode('?', 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

    $arrayMenuName = array(
        'front-page' => 'Trang Chủ',
        'intro' => 'Giới Thiệu',
        'news' => 'Tin Tức',
        'contact' => 'Liên Hệ',
        'location' => 'Vị Trí',
        'facility' => 'Tiện Ích',
        'product' => 'Sản Phẩm',
        'gallery' => 'Thư Viện Tài Liệu',
        'over_view' => 'Tổng Quan',
        'owner' => 'Chủ Đầu Tư'
    );

    $arrID = array();
    if(function_exists('icl_object_id')) {

        foreach ($ids_array as $name => $id) {
            //$xlat = icl_object_id($id, $type, true);
			$xlat = apply_filters( 'wpml_object_id', $id, $type );
            if(!is_null($xlat)) $arrID[$name] = $xlat;
        }

    } else {
        $arrID = $ids_array;
    }

    $res = array();
    foreach ($arrID as $name => $id) {
        if($id == 0) {
            $res[$name] = array(
                'id' => $id,
                'url' => home_url(),
                //'class' => is_front_page() ? 'active' : '',
                'class' => is_front_page() ? 'active' : '',
                'menu_name' => $arrayMenuName[$name],
            );
        }else{
            $res[$name] = array(
                'id' => $id,
                'url' => get_permalink($id),
                //'class' => is_page($id) ? 'active' : '',
                'class' => url_to_postid($url[0]) == $id ? 'active' : '',
                'menu_name' => $arrayMenuName[$name]
            );
        }
    }

    return $res;
}

global $menuInfo;
$arrMenu = array(
    'front-page' => '0',
    'intro' => 15,
    'contact' => 29,
    'location' => 31,
    'facility' => 34,
    'product' => 36,
    'gallery' => 38,
    'over_view' => 40,
    'owner' => 42
);


$menuInfo = lang_object_ids($arrMenu);