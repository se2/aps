<?php
if ( function_exists('acf_add_options_page') )
{
    acf_add_options_page(array(
        'page_title'    => 'Theme Options',
        'menu_title'    => 'Theme Options',
        'menu_slug'     => 'theme_options',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Homepage Options',
        'menu_title'    => 'Homepage Options',
        'menu_slug'     => 'homepage_options',
        'parent_slug'   => 'theme_options'
    ));

    acf_add_options_sub_page(array(
        'page_title'    => 'Shop Options',
        'menu_title'    => 'Shop Options',
        'menu_slug'     => 'shop_options',
        'parent_slug'   => 'theme_options'
    ));

}