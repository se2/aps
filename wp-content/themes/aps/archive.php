<?php

$currTermId = intval(get_queried_object()->term_id);

if($currTermId <= 0) {
    wp_redirect(get_permalink(get_page_by_path('t-news')));
    exit;
}

get_header();

global $menuInfo, $staticContentMeta, $curLang;

$var = array('cateId' => $currTermId);

?>

<?php uiwp_get_template('template/t-news.php', $var) ?>

<?php get_footer(); ?>