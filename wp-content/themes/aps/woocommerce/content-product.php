<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$featuredImage = wp_get_attachment_image_src( get_post_thumbnail_id( ), 'full' );

$attachment_ids = $product->get_gallery_attachment_ids();
?>

<div class="product-index desktop-3 tablet-half mobile-half <?php post_class(); ?>" data-alpha="PIMP DUSTER - BLUE" data-price="10995">
    <div class="prod-container">
        <div class="slide-product-image">
            <a class="image-1" href="<?php echo get_permalink() ?>" title="<?php echo the_title() ?>">
                <img id="<?php echo get_the_id() ?>-1" src="<?php echo $featuredImage[0] ?>" alt="<?php echo the_title() ?>"/>
            </a>
            <?php $index = 2; ?>
            <?php foreach( $attachment_ids as $attachment_id ) : ?>
                <?php $image_link = wp_get_attachment_url( $attachment_id ); ?>
                <a class="image-<?php echo $index ?>" href="<?php echo get_permalink() ?>" title="<?php echo the_title() ?>">
                    <img id="<?php echo get_the_id() . '-' . $index ?>" src="<?php echo $image_link ?>" alt="<?php echo the_title() ?>"/>
                </a>
                <?php $index++; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="product-info">
        <a href="<?php echo get_permalink() ?>">
            <h3><?php echo the_title() ?></h3>
        </a>
        <div class="price">
            <?php if ( $price_html = $product->get_price_html() ) : ?>
            <div class="prod-price"><?php echo $price_html; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
