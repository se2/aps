<?php

global $curLang, $menuInfo, $staticContentMeta;

global $woocommerce, $userInfo;

// Common
$curLang = defined(ICL_LANGUAGE_CODE) ? ICL_LANGUAGE_CODE : 'en';

?>
<!doctype html>
<!--[if IE 7]> <html class="no-js ie7" lang="<?php echo $curLang ?>"> <![endif]-->
<!--[if IE 8]> <html class="no-js ie8" lang="<?php echo $curLang ?>"> <![endif]-->
<!--[if IE 9]> <html class="no-js ie9" lang="<?php echo $curLang ?>"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="<?php echo $curLang ?>"> <!--<![endif]-->
<head>
    <title><?php wp_title( '|', true, 'right' ); ?><?php echo get_bloginfo('name'); ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="<?php echo $curLang ?>" />

    <!--[if lte IE 8]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <![endif]-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <meta name="title" content="<?php wp_title( '|', true, 'right' ); ?><?php echo get_bloginfo('name'); ?>" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <?php if( get_field( 'favicon', 'option' ) ): ?>
        <?php $favicon = get_field( 'favicon', 'option' ) ?>
        <link href="<?php echo $favicon['url'] ?>" rel="image_src" />
        <link href="<?php echo $favicon['url'] ?>" rel="icon" type="image/png" />
    <?php endif ?>

    <?php wp_head() ?>

    <script>
        var site_url = '<?php echo SITE_URL ?>';

        $(window).load(function () {
            $('.prod-container').matchHeight();

        });
    </script>
</head>
<body class="gridlock shifter index">

    <div id="mobile-only">
        <div class="row">
            <ul id="mobile-menu" class="mobile-3">
                <li><a href="<?php echo SITE_URL ?>"><i class="fa fa-home"></i></a></li>
                <!--<li><a href="account/login.html"><i class="fa fa-user"></i></a></li>
                <li class="curr">
                    <select id="currencies" name="currencies">
                        <option value="AUD" selected="selected">AUD</option>
                        <option value="GBP">GBP</option>
                        <option value="CAD">CAD</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="BRL">BRL</option>
                        <option value="JPY">JPY</option>
                        <option value="HKD">HKD</option>
                    </select>
                </li>-->
                <li><a href="<?php echo wc_get_cart_url() ?>"><i class="fa fa-shopping-cart"></i> <span class="item_count"><?php echo WC()->cart->get_cart_contents_count() ?></span></a></li>
                <li class="shifter-handle"></li>
            </ul>
        </div>
    </div>

    <div class="shifter-page">
        <div class="page-wrap">
            <header>
							<div id="identity" class="row flex flex-wrap justify-between ">
								<?php
								if ( get_field( 'logo', 'option' ) ) :
									$logo = get_field('logo', 'option');
								?>
								<div id="logo" class="desktop-4 tablet-3 mobile-3">
									<a href="<?php echo SITE_URL ?>">
										<img src="<?php echo $logo['url'] ?>" alt="<?php echo get_bloginfo('name'); ?>" style="border: 0;"/>
									</a>
								</div>
								<?php endif ?>
								<div class="desktop-8 tablet-3 mobile-3 text-right flex flex-wrap justify-end">
									<div class="search-section">
										<p class="text_order inline-block text-xs">Need help? Email us at help@tibbsandbones.com ☺</p>
										<p class="inline-block px-2">
											<select id="currencies" name="currencies">
												<option value="AUD" selected="selected">AUD</option>
												<option value="GBP">GBP</option>
												<option value="CAD">CAD</option>
												<option value="USD">USD</option>
												<option value="EUR">EUR</option>
												<option value="BRL">BRL</option>
												<option value="JPY">JPY</option>
												<option value="HKD">HKD</option>
											</select>
										</p>
										<form action="<?php echo SITE_URL ?>" method="get" class="inline-block relative">
											<input type="text" name="s" id="q" placeholder="search"/>
											<span class="input-group-btn">
												<button type="submit" class="outline-none btn btn-search"><i class="fa fa-search" aria-hidden="true"></i></button>
											</span>
										</form>
									</div>
									<p class="free_shipping site-notice text-xs font-bold ff-montserrat">AUS WIDE EXPRESS SHIPPING 💌 SAME DAY MELB SHIPPING AVAILABLE</p>
									<div class="flex flex-wrap items-center justify-end desktop-12">
										<ul id="cart" class="inline-block">
											<li class="cart-overview">
												<a href="<?php echo wc_get_cart_url() ?>"><i class="fa fa-shopping-cart"></i> Shopping Cart
													<span class="item_count"><?php echo WC()->cart->get_cart_contents_count() ?></span>
												</a>
											</li>
										</ul>
										<ul id="social-links" class="inline-block list-reset">
											<?php if( get_field( 'facebook', 'option' ) ): ?>
											<li>
												<a href="<?php the_field( 'facebook', 'option' ) ?>" target="_blank"><i class="fa fa-facebook"></i></a>
											</li>
											<?php endif ?>

											<?php if( get_field( 'twitter', 'option' ) ): ?>
											<li>
												<a href="<?php the_field( 'twitter', 'option' ) ?>" target="_blank"><i class="fa fa-twitter"></i></a>
											</li>
											<?php endif ?>

											<?php if( get_field( 'pinterest', 'option' ) ): ?>
											<li>
												<a href="<?php the_field( 'pinterest', 'option' ) ?>" target="_blank"><i class="fa fa-pinterest"></i></a>
											</li>
											<?php endif ?>

											<?php if( get_field( 'tumblr', 'option' ) ): ?>
											<li>
												<a href="<?php the_field( 'tumblr', 'option' ) ?>" target="_blank"><i class="fa fa-tumblr"></i></a>
											</li>
											<?php endif ?>

											<?php if( get_field( 'instagram', 'option' ) ): ?>
											<li>
												<a href="<?php the_field( 'instagram', 'option' ) ?>" target="_blank"><i class="fa fa-instagram"></i></a>
											</li>
											<?php endif ?>
										</ul>
									</div>
								</div>
							</div>
            </header>

            <nav id="nav" role="navigation">
                <div id="navigation" class="row">
                    <ul id="nav" class="desktop-12 mobile-3">
                        <?php $mainMenu = get_menu_by_name('Main menu'); ?>
                        <?php foreach($mainMenu as $item) : ?>
                            <?php $subMenu = $item['children'] ?>
                            <?php if( ! is_array($subMenu) ) : ?>
                                <li><a href="<?php echo $item['url'] ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a></li>
                            <?php else: ?>
                                <li class="dropdown">
                                    <a href="<?php echo $item['url'] ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
                                    <ul class="submenu">
                                        <?php foreach($subMenu as $subItem) : ?>
                                            <li><a href="<?php echo $subItem['url'] ?>" title="<?php echo $subItem['title'] ?>"><?php echo $subItem['title'] ?></a></li>
                                        <?php endforeach ?>
                                    </ul>
                                </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                </div>
            </nav>
            <div class="clear"></div>

