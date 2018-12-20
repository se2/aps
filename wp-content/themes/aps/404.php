<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

	<div class="wrapper">
		<div class="container">
			<div class="search-page">
				<span class="shadow-main"></span>

				<div class="page-header">
					<h1 class="page-title"><?php _e( 'Not Found', 'twentythirteen' ); ?></h1>
				</div>

				<div class="page-wrapper">
					<div class="page-content">
						<?php uiwp_get_template('template/shop-by-category.php') ?>
					</div><!-- .page-content -->
				</div><!-- .page-wrapper -->
			</div>
		</div>
	</div>

<?php get_footer(); ?>