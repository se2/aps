<?php
	function short_code_get_menu(){
		$arr = array(
			'menu' => 'Primary Navigation',
			'theme_location' => 'primary',
			'container' => '',
			'container_id'    => '',
			'container_class' => '',
			'menu_class' => 'primary-nav',
			'menu_id' => '',
			'before' => '',
			'after' => '',
		);
		wp_nav_menu($arr);
	}

	function short_code_get_secondary_menu(){
		$arr = array(
			'menu' => 'Secondary Navigation',
			'theme_location' => 'secondary',
			'container' => '',
			'menu_class' => 'list_sub'
		);
		wp_nav_menu($arr);
	}

	add_filter('wp_nav_menu','add_menuclass');
	function add_menuclass($ulItem) {
		return preg_replace('/<a /', '<a class="itemTxt easing"', $ulItem, -1);
	}

//	add_filter('wp_nav_menu_items', 'my_nav_menu_link', 10, 2);
//	function my_nav_menu_link($items, $args){
//		if($args->theme_location == 'primary') {
//			$html = str_get_html($items);
//
//			foreach($html->find('ul[class=sub-menu]') as $element){
//				$oldSubMenu = $element;
//				$newSubMenu = "";
//				$subMenu = $element->find('li');
//				$index = 0;
//				$numColumn = ceil(count($subMenu) / 6);
//
//				foreach ($subMenu as $subMenuItem) {
//					if($index == 0){
//						$newSubMenu = "<div class='submenu_wrapper column".$numColumn."'><ul class='sub-menu'>".$subMenuItem;
//					}else if($index % 5 == 0){
//						$newSubMenu .= $subMenuItem."</ul><ul class='sub-menu'>";
//					}else{
//						$newSubMenu .= $subMenuItem;
//					}
//					$index++;
//				}
//				$newSubMenu .= "</ul></di>";
//				$html = str_replace($oldSubMenu, $newSubMenu, $html);
//			}
//			return $html;
//		}else{
//			return $items;
//		}
//	}

	add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
	function special_nav_class($classes, $item){
		$classes[] = 'menuItem';
		if( in_array('current-menu-item', $classes) ){
			$classes[] = 'active ';
		}

//		if((is_tag() || (is_single() && !is_product()) || is_category()) && ($item->title == 'Service')){
//			$classes[] = "active";
//		}else if((is_product() || is_product_category()) && ($item->title == 'Sản phẩm' || $item->title == 'Shop')){
//			$classes[] = "active";
//		}

		if( in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes) ){
			$classes[] = 'active ';
		}
		return $classes;
	}

	function remove_parent_classes($class){
		return ($class == 'current_page_item' || $class == 'current_page_parent' || $class == 'current_page_ancestor'  || $class == 'current-menu-item') ? FALSE : TRUE;
	}

//	add_filter('nav_menu_css_class', 'add_class_to_wp_nav_menu');
//	function add_class_to_wp_nav_menu($classes){
//		switch (get_post_type())
//		{
//			case 'the-collection':
//			$classes = array_filter($classes, "remove_parent_classes");
//			if (in_array('menu-item-111', $classes)){
//				$classes[] = 'active collection';
//			}
//			break;
//			case 'celebritys':
//			$classes = array_filter($classes, "remove_parent_classes");
//			if (in_array('menu-item-115', $classes)){
//				$classes[] = 'active';
//			}
//			break;
//		}
//		if( in_array('current-menu-item', $classes) ){
//			$classes[] = 'active ';
//		}
//		return $classes;
//	}

?>