<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<?php
	/**
	 * woocommerce_before_single_product hook.
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="breadcrumb" class="desktop-12">
	<?php bcn_display() ?>
</div>

<div class="clear"></div>

<div id="product-<?php the_ID(); ?>" class="product-page" <?php post_class(); ?>>

	<?php
		/**
		 * woocommerce_before_single_product_summary hook.
		 *
		 * @hooked woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div id="product-right" class="desktop-7 tablet-3 mobile-3">
		<div id="product-description" class="desktop-10 tablet-6 mobile-3">

			<?php
				/**
				 * woocommerce_single_product_summary hook.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>

			<?php echo apply_filters( 'woocommerce_short_description', $product->description ) ?>

			<?php if ( get_field( 'shipping_returns', 'option' ) || get_field( 'size_chart', 'option' ) ) : ?>
			<div class="panel-group" id="accordion">
				<?php if ( get_field( 'shipping_returns', 'option' ) ) : ?>
				<div class="panel">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse-tab2" class="collapsed">
								<span>✈️&nbsp;Shipping &amp; Returns</span>
							</a>
						</h4>
					</div>
					<div id="collapse-tab2" class="panel-collapse collapse">
						<div class="panel-body"><?php the_field( 'shipping_returns', 'option' ); ?></div>
					</div>
				</div>
				<?php endif; ?>
				<?php if ( get_field( 'size_chart', 'option' ) ) : ?>
				<div class="panel">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapse-tab3" class="collapsed">
								<span>👚&nbsp;Size Chart</span>
							</a>
						</h4>
					</div>
					<div id="collapse-tab3" class="panel-collapse collapse">
						<div class="panel-body"><?php the_field( 'size_chart', 'option' ); ?></div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="desc">
				<div class="share-icons">
					<a href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink() ?>" class="facebook" target="_blank"><i class="fa fa-facebook fa-2x"></i></a>
					<a href="http://twitter.com/home?status=<?php echo get_permalink() ?>" title="Share on Twitter" target="_blank" class="twitter"><i class="fa fa-twitter fa-2x"></i></a>
					<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( ) );?>
					<a target="blank" href="http://pinterest.com/pin/create/button/?url=<?php echo get_permalink() ?>&amp;media=<?php echo $image[0] ?>" title="Pin This Product" class="pintrest"><i class="fa fa-pinterest fa-2x"></i></a>
				</div>
			</div>

		</div><!-- #product-description -->

		<?php
			/**
			 * woocommerce_after_single_product_summary hook.
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 * @hooked woocommerce_upsell_display - 15
			 * @hooked woocommerce_output_related_products - 20
			 */
			do_action( 'woocommerce_after_single_product_summary' );
		?>

		<?php do_action( 'woocommerce_after_single_product' ); ?>

	</div><!-- #product-right -->

</div><!-- .product-page -->

</div>

<div class="clear"></div>

<div id="looked-at" class="desktop-12 mobile-hide">
	<div id="recently-viewed-products" class="collection clearfix">
		<h2>
			<span>Recently Viewed Products</span>
		</h2>
		<?php echo do_shortcode('[woocommerce_recently_viewed_products per_page="6"]') ?>
	</div>
</div>
