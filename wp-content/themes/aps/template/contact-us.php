<?php

// Template name: Contact Us

get_header();

global $menuInfo, $staticContentMeta, $curLang;
?>

    <div id="content" class="row">
        <div id="breadcrumb" class="desktop-12">
            <?php bcn_display() ?>
        </div>
        <div class="clear"></div>

        <h1 class="desktop-12">Contact Us</h1>

        <div class="desktop-6 mobile-3">
            <?php
            while ( have_posts() ) : the_post();
                echo the_content();
            endwhile;
            ?>
        </div>

        <div class="desktop-6 mobile-3">
            <?php echo do_shortcode('[contact-form-7 id="126" title="Contact form 1"]') ?>
        </div>
    </div>

<?php get_footer(); ?>


