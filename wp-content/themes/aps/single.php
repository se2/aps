<?php
get_header();
global $menuInfo;
?>

<?php
    while ( have_posts() ) :
        the_post();

        setPostViews(get_the_ID());

        if(get_post_type() == 'post'){
            uiwp_get_template( 'template/t-news-detail.php', $atts );
        }
        else{
            echo the_content();
        }
?>

<?php endwhile; ?>

<?php get_footer(); ?>