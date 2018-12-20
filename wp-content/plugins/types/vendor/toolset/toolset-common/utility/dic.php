<?php
/**
 * Initialize the Auryn dependency injector and offer it through a toolset_dic filter.
 */

$dic = new \OTGS\Toolset\Common\Auryn\Injector();

// singletons...
//$toolset_repository = Toolset_Post_Type_Repository::get_instance();
//$dic->share( $toolset_repository );

add_filter( 'toolset_dic', function( /** @noinspection PhpUnusedParameterInspection */ $ignored ) use ($dic) {
	return $dic;
} );