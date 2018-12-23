<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
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
	exit;
}

if ( $related_products ) : ?>

	<div id="related" class="desktop-2 tablet-hide mobile-hide">

		<h4 style="text-align: center;"><?php esc_html_e( 'More in this Collection', 'woocommerce' ); ?></h4>

		<ul class="related-products desktop-12">

			<?php
			foreach ( $related_products as $related_product ) :
				$post_object = get_post( $related_product->get_id() );

				setup_postdata( $GLOBALS['post'] =& $post_object );

				// wc_get_template_part( 'content', 'product' );

				$image = wp_get_attachment_image_src( get_post_thumbnail_id( ) );
			?>
			<li>
				<div class="image">
					<a href="<?php echo get_permalink(); ?>" title="<?php echo get_the_title(); ?>">
						<img src="<?php echo $image[0]; ?>" alt=""/>
					</a>
				</div>
			</li>
		<?php endforeach; ?>

		</ul>

	</div>

<?php endif;

wp_reset_postdata();
