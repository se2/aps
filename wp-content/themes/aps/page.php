<?php get_header(); ?>

<div id="content" class="row">
	<div id="breadcrumb" class="desktop-12">
		<?php bcn_display() ?>
	</div>
	<div class="clear"></div>

	<div id="page" class="desktop-12 tablet-6 mobile-3">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</div>

<?php get_footer(); ?>