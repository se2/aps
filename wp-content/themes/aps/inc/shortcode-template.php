<?php

add_shortcode('show_typical_construction_page' , 'showHtmlTypicalConstructionPage');
function showHtmlTypicalConstructionPage( $atts, $content = null ) {
	$atts = shortcode_atts( array('content' => $content ), $atts );
	ob_start();
	uiwp_get_template( 'template/construction.php', $atts );
	//return apply_filters( 'uiwp_billing_address_html', ob_get_clean() );
}

add_shortcode('show_typical_production_page', 'showTypicalProductionPage');
function showTypicalProductionPage( $atts, $content = null ){
	$atts = shortcode_atts( array('content' => $content), $atts );
	ob_start();
	uiwp_get_template( 'template/production.php', $atts );
}

add_shortcode('show_test' , 'showTest');
function showTest( $atts, $content = null ) {
    $atts = shortcode_atts( array('content' => $content ), $atts );
    ob_start();
    uiwp_get_template( 'template/construction.php', $atts );
    //return apply_filters( 'uiwp_billing_address_html', ob_get_clean() );
}