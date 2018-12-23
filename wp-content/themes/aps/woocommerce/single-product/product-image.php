<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
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
 * @version 3.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;
$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
$image_title       = get_post_field( 'post_excerpt', $post_thumbnail_id );
$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );

$attachment_ids = $product->get_gallery_attachment_ids();
?>

<div id="mob-product-images" class="owl-carousel desktop-hide tablet-hide mobile-3">
	<div class="mthumb">
		<img src="<?php echo $full_size_image[0] ?>" alt="<?php echo $image_title ?>">
	</div>
	<?php foreach( $attachment_ids as $attachment_id ) : ?>
		<?php $image_link = wp_get_attachment_url( $attachment_id ); ?>
		<?php $gallery_image_title = get_post_field( 'post_excerpt', $attachment_id ); ?>
		<div class="mthumb">
			<img src="<?php echo $image_link ?>" alt="<?php echo $gallery_image_title ?>">
		</div>
	<?php endforeach ?>
</div>

<div id="product-photos" class="desktop-5 tablet-3 mobile-hide">
	<div class="bigimage desktop-12 tablet-5">
		<img src="<?php echo $full_size_image[0] ?>" alt='<?php echo $image_title ?>' title="<?php echo $image_title ?>"/>
	</div>
	<a href="<?php echo $full_size_image[0] ?>" class="clicker">
		<img class="thumbnail desktop-3 tablet-1" src="<?php echo $full_size_image[0] ?>" alt="<?php echo $image_title ?>"/>
	</a>
	<?php foreach( $attachment_ids as $attachment_id ) : ?>
		<?php $image_link = wp_get_attachment_url( $attachment_id ); ?>
		<?php $gallery_image_title = get_post_field( 'post_excerpt', $attachment_id ); ?>
		<a href="<?php echo $image_link ?>" class="clicker">
			<img class="thumbnail desktop-3 tablet-1" src="<?php echo $image_link ?>" alt="<?php echo $gallery_image_title ?>"/>
		</a>
	<?php endforeach ?>
</div>

<script>
	$('.bigimage').zoom();
	$('.clicker').click(function () {
		var newImage = $(this).attr('href');
		$('.bigimage img').attr({src: newImage});
		return false;
	});
</script>