<?php
$keyword = get_search_query();

if($keyword == '') {
    wp_redirect(site_url());
    exit;
}

get_header();

?>

    <div id="content" class="row">
        <div id="breadcrumb" class="desktop-12">
            <?php bcn_display() ?>
        </div>
        <div class="clear"></div>

        <div id="page" class="desktop-12 tablet-6 mobile-3">
            <h2>Search the store</h2>

            <div id="search-bar" class="desktop-12 mobile-3">
                <form action="<?php echo SITE_URL ?>" method="get">
                    <p>Your search for "<?php echo $keyword ?>" revealed the following:</p>
                    <input type="text" name="s" placeholder="SEARCH">
                </form>
            </div>

            <?php woocommerce_product_loop_start(); ?>

            <?php woocommerce_product_subcategories(); ?>

            <?php while ( have_posts() ) : the_post(); ?>

                <?php
                /**
                 * woocommerce_shop_loop hook.
                 *
                 * @hooked WC_Structured_Data::generate_product_data() - 10
                 */
                do_action( 'woocommerce_shop_loop' );
                ?>

                <?php wc_get_template_part( 'content', 'product' ); ?>

            <?php endwhile; // end of the loop. ?>

            <?php woocommerce_product_loop_end(); ?>

            <div id="pagination" class="desktop-12 tablet-6 mobile-3">
                <?php
                echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
                    'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
                    'format'       => '',
                    'add_args'     => false,
                    'current'      => max( 1, get_query_var( 'paged' ) ),
                    'total'        => $wp_query->max_num_pages,
                    'prev_text'    => '&larr;',
                    'next_text'    => '&rarr;',
                    'type'         => 'list',
                    'end_size'     => 3,
                    'mid_size'     => 3,
                ) ) );
                ?>
            </div>

        </div>
    </div>

<?php get_footer(); ?>