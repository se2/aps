<?php

/**
 * Parse the video uri/url to determine the video type/source and the video id
 */
function parse_video_uri( $url ) {

    $parse = parse_url( $url );

    $video_type = '';
    $video_id = '';

    if ( $parse['host'] == 'youtu.be' ) {
        $video_type = 'youtube';
        $video_id = ltrim( $parse['path'], '/' );
    }

    if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {

        $video_type = 'youtube';
        parse_str( $parse['query'] );
        $video_id = $v;

        if ( !empty( $feature ) ){
            $video_id = end( explode( 'v=', $parse['query'] ) );
        }

        if ( strpos( $parse['path'], 'embed' ) == 1 ) {
            $video_id = end( explode( '/', $parse['path'] ) );
        }
    }

    // Url is http://www.vimeo.com
    if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {
        $video_type = 'vimeo';
        $video_id = ltrim( $parse['path'],'/' );
    }

    $host_names = explode(".", $parse['host'] );
    $rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');

    // Url is an oembed url wistia.com
    if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {
        $video_type = 'wistia';
        if ( strpos( $parse['path'], 'medias' ) == 1 || strpos( $parse['path'], 'embed' ) == 1 ) {
            $video_id = end( explode( '/', $parse['path'] ) );
        }
    }

    // If recognised type return video array
    if ( !empty( $video_type ) ) {
        $video_array = array(
            'type' => $video_type,
            'id' => $video_id
        );

        return $video_array;
    } else {
        return false;
    }
}

function weekdayToVietnamese($weekday) {

	$weekday = strtolower($weekday);
	switch($weekday) {
		case 'monday':
			$weekday = 'Thứ hai';
			break;
		case 'tuesday':
			$weekday = 'Thứ ba';
			break;
		case 'wednesday':
			$weekday = 'Thứ tư';
			break;
		case 'thursday':
			$weekday = 'Thứ năm';
			break;
		case 'friday':
			$weekday = 'Thứ sáu';
			break;
		case 'saturday':
			$weekday = 'Thứ bảy';
			break;
		default:
			$weekday = 'Chủ nhật';
			break;
	}
	return $weekday;
}

function removeTagHTML($input_data){
	$result_content = $input_data;
	if(is_int($input_data)){
		$result_content = intval($input_data);
	}else{
		if(!(get_magic_quotes_gpc())) {
			$result_content = addslashes(str_replace("-", " ", trim($input_data)));
		}
		$result_content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $result_content);
		$result_content = mysql_real_escape_string(strip_tags($result_content));
		$result_content = str_replace(array('<','>','%','"'), '', $result_content);
	}
	return $result_content;
}

function check_is_ajax() {
  $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  if($isAjax) {
	 return true;    
  }
  return false;
}

function getClientIP() {

    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];
        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}

function getYouTubeId($url) {
	// Format all domains to http://domain for easier URL parsing
	str_replace('https://', 'http://', $url);
	if (!stristr($url, 'http://') && (strlen($url) != 11)) {
		$url = 'http://' . $url;
	}
	$url = str_replace('http://www.', 'http://', $url);
	
	if (strlen($url) == 11) {
		$code = $url;
	}else if (preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches) ) {
		$code = substr($matches[0], 0, 11);
	} else if (preg_match('/http:\/\/youtu.be/', $url)) {
		$url = parse_url($url, PHP_URL_PATH);
		$code = substr($url, 1, 11);
	} else if (preg_match('/watch/', $url)) {
		$arr = parse_url($url);
		parse_str($url);
		$code = isset($v) ? substr($v, 0, 11) : false;
	} else if (preg_match('/http:\/\/youtube.com\/v/', $url)) {
		$url = parse_url($url, PHP_URL_PATH);
		$code = substr($url, 3, 11);
	} else if (preg_match('/http:\/\/youtube.com\/embed/', $url, $matches)) {
		$url = parse_url($url, PHP_URL_PATH);
		$code = substr($url, 7, 11);
	}else {
		$code = false;
	}

	if ($code && (strlen($code) < 11)) {
		$code = false;
	}

	return $code;
}
function freshcookie($itemid, $cookiename, $remove=false) {
	$isupdate = 1;
	if(empty($_COOKIE)) { 
		$isupdate = 1;
	} else {
		$old = empty($_COOKIE[$cookiename])?0:trim($_COOKIE[$cookiename]);
		$old = trim($old,"_");
		$itemidarr = explode('_', $old);
		if (!$remove){			
			if(in_array($itemid, $itemidarr)) {
				$isupdate = 0;
			} else {
				$itemidarr[] = trim($itemid);
				setcookie($cookiename, implode('_', $itemidarr),time()+3600*60*60);
			}
		}
		else{			
			foreach($itemidarr as $key=>$value){
				if ($itemid==$value){
					unset($itemidarr[$key]);
					setcookie($cookiename, implode('_', $itemidarr),time()+3600*60*60);	
					break;
				}
					
			}			
		}
	}
	return $isupdate;
}
function saddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = saddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}
function copydir($source,$destination)
{
	if(!is_dir($destination)){
	$oldumask = umask(0); 
	mkdir($destination); // so you get the sticky bit set 
	umask($oldumask);
	}
	$dir_handle = @opendir($source) or die("Unable to open");
	while ($file = readdir($dir_handle)) 
	{
	if($file!="." && $file!=".." && !is_dir("$source/$file")) //if it is file
	copy("$source/$file","$destination/$file");
	if($file!="." && $file!=".." && is_dir("$source/$file")) //if it is folder
	copydir("$source/$file","$destination/$file");
	}
	closedir($dir_handle);
}

function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
function formhash(){
	$ip = $_SERVER['REMOTE_ADDR'];
	$token = md5($ip.uniqid(rand(), true)); 
	$_SESSION['token'] = $token; 
	return $_SESSION['token'];
}
function substring($string, $length = 80, $etc = ' ...',$break_words = false, $middle = false)
{ 
	if ($length == 0 || strlen($string)<=0)
	return '';	
	$string = stripcslashes(strip_tags($string));
	$patterns = array("/(<br>|<br \/>|<br\/>)\s*/i", "/(\r\n|\r|\n)/", "/\s+?(\S+)?$/");
	if (strlen($string) > $length) {
		$length -= min($length, strlen($etc));
		if (!$break_words && !$middle) {
			$string = substrs($string, 0, $length+1);
			$string = preg_replace($patterns, ' ', $string);
		}
		if(!$middle) {
			return substrs($string, 0, $length) . $etc;
		} else {
			return substrs($string, 0, $length/2) . $etc . substrs($string, -$length/2);
		}
	} else {
		return $string;
	}
}
function substrs($string, $start, $length){		
	return mb_substr($string, $start, $length,"UTF-8");
}
function stripUnicode($str){
	if(!$str) return false;
	$unicode = array(
	'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
	'd'=>'đ',
	'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
	'i'=>'í|ì|ỉ|ĩ|ị',
	'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
	'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
	'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
	);
	foreach($unicode as $nonUnicode=>$uni) $str = preg_replace("/($uni)/i",$nonUnicode,$str);
	return $str;
}
function str2url($str  = NULL, $sperator="-"){
	if(!$str) return NULL;
	//$str = mb_strtolower($str,'utf-8');
	$str  = stripUnicode($str);
	$str = preg_replace('/[^0-9a-z]/is',' ',$str);
	$str = trim($str);
	$str = preg_replace('/\s+/',$sperator,$str);
	return str_replace(' ',$sperator,$str);
}

function format_date($date_system,$date_format = 'Y-m-d'){
	return date($date_format, strtotime($date_system));
}

function print_flash_image($file,$w=0,$h=0,$url = NULL){
	if(substr($file,'-4')=='.swf'){
		$str = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="'.$w.'" height="'.$h.'" title="Homepage Banner">
                    <param name="movie" value="'.$file.'" />
                    <param name="quality" value="high" />
                    <embed src="'.$file.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$w.'" height="'.$h.'"></embed>
       	  	      </object>';
	}else{
		$str = '';
		if($url) $str .= '<a href="'.$url.'">';
		$str .= '<img src="'.$file.'"'.($w?' width="'.$w.'"':'').' '.($h?'height="'.$h.'"':'').' />';
		if($url) $str .= '</a>';
	}
	return $str;
}

function cutnwords($str,$n = 20){
	$tmp = explode(' ',$str);
	$count = count($tmp);
	if($count <= $n) return $str;
	for($i=$n;$i<$count;$i++) unset($tmp[$i]);
	return implode(' ',$tmp).'...';
}
	
function data2xml($data,$parent = 0,$parents = '',$sep = '/',$dir,$ext = ''){
	$str = '';
	if(count($data[$parent])){
		$str .= '<ul>';
		if($parents) $parents .= $sep.$parent;
		else $parents = $dir;
		foreach($data[$parent] as $rs){
			$str .= '<li><a href="'.$parents.$sep.$rs['id'].$ext.'">'.$rs['name'].'</a></li>';
			$str .= data2xml($data,$rs['id'],$parents,$sep,$dir,$ext);
		}
		$str .= '</ul>';
	}
	return $str;
}

function upload($tagname,$upload_dir,$ex_name=NULL,$allow_ext=NULL ){
	$arr = array();
	$arr['error'] = 0;
	if(!is_writable($upload_dir)){
		$arr['error'] = 1;
		$arr['msg'] =  'You have no <strong>WRITE</strong> access for folder <strong>'.$upload_dir.'</strong>';
		return $arr;
	}

	$filesize=$_FILES[$tagname]["size"];
	$fileerror=$_FILES[$tagname]["error"];
	$filename = basename($_FILES[$tagname]["name"]);
	$file_tmp = $_FILES[$tagname]["tmp_name"];

	if($filename && $fileerror){
		$arr['error'] = 1;
		$arr['msg'] =  'The file size is so large to upload ( Max file size: <strong>'.ini_get('upload_max_filesize ').'</strong>)';
		return $arr;
	}
	$continue = 0;
	if($filename && is_array($allow_ext)) for($i=0; $i<count($allow_ext);){
		if(strtolower(substr($filename,-1*strlen($allow_ext[$i]))) == $allow_ext[$i]){
			$continue = 1;
			$i = count($allow_ext)  + 1;
		}else{
			$i++;
		}
	}else{
		$continue = 1;
	}

	if(!$continue){
		$arr['error'] = 1;
		$arr['msg'] =  'Not allow extentions file, you can only upload file '.implode(', ',$allow_ext);
		return $arr;
	}

	$upload_file = preg_replace('/[^a-z0-9\.\-_]/is','',$filename);

	if(file_exists($upload_dir.$upload_file)) $upload_file =  substr(basename($upload_file),0,-4).'-'.$ex_name.str_shuffle('123456789').substr($upload_file,-4);
	if (@move_uploaded_file($file_tmp, $upload_dir.$upload_file) ){
		@chmod ($upload_file,0777);
		$arr['filename'] = $upload_file;
		return  $arr;
	}
	if(!$continue){
		$arr['error'] = 1;
		$arr['msg'] =  'Server cannot upload this file, please try again';		
	}
	return $arr;
}