<?php
global $menuInfo, $staticContentMeta;

?>

	<footer class="site-footer">
		<div class="row footer-top flex flex-wrap">
			<div class="w-full md:w-1/2 lg:w-1/5">
				<?php $menuName = get_menu_name_by_location( 'secondary' ); ?>
				<h3>
					<span><?php echo $menuName; ?></span>
				</h3>
				<ul>
					<?php $footer1 = wp_get_nav_menu_items($menuName); ?>
					<?php foreach( $footer1 as $item) : ?>
						<?php $target = $item->type == 'custom' ? '_blank' : ''; ?>
						<li><a target="<?php echo $target ?>" href="<?php echo $item->url ?>" title=""><?php echo $item->title ?></a></li>
					<?php endforeach ?>
				</ul>
			</div>
			<div class="w-full md:w-1/2 lg:w-1/5">
				<?php $menuName = get_menu_name_by_location('tertiary') ?>
				<h3>
					<span><?php echo $menuName; ?></span>
				</h3>
				<ul>
					<?php $footer1 = wp_get_nav_menu_items($menuName); ?>
					<?php foreach( $footer1 as $item) : ?>
						<?php $target = $item->type == 'custom' ? '_blank' : ''; ?>
						<li><a target="<?php echo $target ?>" href="<?php echo $item->url ?>" title=""><?php echo $item->title ?></a></li>
					<?php endforeach ?>
				</ul>
			</div>
			<div class="w-full md:w-1/2 lg:w-1/5">
				<h3>
					<span>Contact us</span>
				</h3>
				<ul>
					<?php if ( have_rows( 'contact_email_list', 'option' ) ) : ?>
					<li class="pb-2">
						<label class="text-white">Email</label>
						<?php while( have_rows('contact_email_list', 'option') ) : the_row() ?>
						<a href="mailto:<?php the_sub_field('contact_email') ?>"><?php the_sub_field('contact_email') ?></a>
						<br>
						<?php endwhile ?>
					</li>
					<?php endif ?>

					<?php if ( have_rows( 'contact_phone_list', 'option' ) ) : ?>
					<li class="pb-2">
						<label class="text-white">Phone</label>
						<?php while( have_rows('contact_phone_list', 'option') ) : the_row() ?>
						<a href="tel:<?php echo str_replace(' ', '', get_sub_field('contact_phone')) ?>"><?php the_sub_field('contact_phone') ?></a>
						<br>
						<?php endwhile ?>
					</li>
					<?php endif ?>

					<?php if ( have_rows( 'contact_address_list', 'option' ) ) : ?>
					<li class="pb-2">
						<label class="text-white">Address</label>
						<?php while( have_rows('contact_address_list', 'option') ) : the_row() ?>
						<p class="text-white"><?php the_sub_field('contact_address') ?></p>
						<?php endwhile ?>
					</li>
					<?php endif ?>
				</ul>
		</div>
			<div class="w-full md:w-1/2 lg:w-2/5 footer-social">
				<h3>
					<span>STAY CONNECTED</span>
				</h3>
				<ul class="social-icons" id="social-icons">
					<?php if ( get_field( 'facebook', 'option' ) ) : ?>
					<li>
						<a href="<?php the_field( 'facebook', 'option' ); ?>" target="_blank">
							<i class="fa fa-facebook fa-lg"></i>
						</a>
					</li>
					<?php endif; ?>
					<?php if ( get_field( 'twitter', 'option' ) ) : ?>
					<li>
						<a href="<?php the_field( 'twitter', 'option' ); ?>" target="_blank">
							<i class="fab fa-twitter fa-lg"></i>
						</a>
					</li>
					<?php endif; ?>
					<?php if ( get_field( 'pinterest', 'option' ) ) : ?>
					<li>
						<a href="<?php the_field( 'pinterest', 'option' ); ?>" target="_blank">
							<i class="fab fa-pinterest-p fa-lg"></i>
						</a>
					</li>
					<?php endif; ?>
					<?php if ( get_field( 'tumblr', 'option' ) ) : ?>
					<li>
						<a href="<?php the_field( 'tumblr', 'option' ); ?>" target="_blank">
							<i class="fab fa-tumblr fa-lg"></i>
						</a>
					</li>
					<?php endif; ?>
					<?php if ( get_field( 'instagram', 'option' ) ) : ?>
					<li>
						<a href="<?php the_field( 'instagram', 'option' ); ?>" target="_blank">
							<i class="fab fa-instagram fa-lg"></i>
						</a>
					</li>
					<?php endif; ?>
				</ul>
				<div class="footer-newsletter">
					<h3><span><?php the_field('subscription_description', 'option') ?> 😍🌈💫</span></h3>
					<section class="newsletter">
						<?php
						if ( get_field( 'display_subscription_form', 'option' ) ) :
							if ( get_field( 'mailchimp_form_url', 'option' ) ) :
						?>
						<div id="signup" class="w-full newsletter">
							<form action="<?php the_field( 'mailchimp_form_url', 'option' ); ?>" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate input-group" target="_blank">
								<input value="" name="EMAIL" class="email inline-block input-group-field" id="mce-EMAIL" placeholder="email@example.com" required="" type="email">
								<span class="input-group-btn">
									<input value="Join" name="subscribe" id="mc-embedded-subscribe" class="small btn inline-block" type="submit">
								</span>
							</form>
						</div>
						<?php
							endif;
						endif;
						?>
					</section>
				</div>
			</div>
		</div>
		<div class="flex flex-wrap row justify-start footer-bottom">
			<div class="w-full">
				<address>© <?php echo date('Y') ?> <?php echo get_bloginfo('name'); ?>. All Rights Reserved.</address>
				<?php
				if ( have_rows( 'payment_gateway_list', 'option' ) ) :
					while ( have_rows( 'payment_gateway_list', 'option' ) ) :
						the_row();
						$logo = get_sub_field( 'payment_gateway_logo' );
				?>
				<img class="payment" src="<?php echo $logo['url'] ?>"/>
				<?php
					endwhile;
				?>
				<div class="clear"></div>
				<?php endif; ?>
			</div>
		</div>
	</footer>

</div><!-- .page-wrap -->
<nav class="shifter-navigation">
    <li class="search">
        <form action="" method="get">
            <input type="text" name="s" placeholder="SEARCH"/>
        </form>
    </li>
    <?php $mainMenu = get_menu_by_name('Main menu'); ?>
    <?php foreach($mainMenu as $item) : ?>
        <?php $subMenu = $item['children'] ?>
        <?php if( ! is_array($subMenu) ) : ?>
            <li><a href="<?php echo $item['url'] ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a></li>
        <?php else: ?>
            <li>
                <a href="<?php echo $item['url'] ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
                <ul class="sub">
                    <?php foreach($subMenu as $subItem) : ?>
                        <li><a href="<?php echo $subItem['url'] ?>" title="<?php echo $subItem['title'] ?>"><?php echo $subItem['title'] ?></a></li>
                    <?php endforeach ?>
                </ul>
            </li>
        <?php endif ?>
    <?php endforeach ?>
</nav>

<div style="display:none">
    <div id="subscribe_popup">
        <p><img src="http://cdn.shopify.com/s/files/1/0646/4381/t/6/assets/popup-image.jpg?12610055060052760844"></p>
        <h3>Join our Mailing List!</h3>
        <p>Get on our list for exclusive sales, new arrivals and more ☺</p>    <!-- BEGIN #subs-container -->
        <div id="subs-container" class="clearfix">
            <div id="mc_embed_signup">
                <form action="http://tibbsandbones.us7.list-manage.com/subscribe/post?u=36c06f248c1bdd74e6c2943ea&amp;id=de72fe87bd"
                      method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate"
                      target="_blank">
                    <input value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="HELLO@TIBBSANDBONES.COM"
                           required="" type="email">
                    <input value="Join" name="subscribe" id="mc-embedded-subscribe" class="button" type="submit">
                </form>
            </div>
        </div>
        <div class="clear"></div>
        <div class="fb-like" data-href="https://www.tibbsandbones.com" data-layout="button_count" data-action="like"
             data-show-faces="true" data-share="false"></div>
    </div>
</div>

<script type="text/javascript">
    //initiating jQuery
    jQuery(function ($) {
        if ($(window).width() >= 741) {
            $(document).ready(function () {
                //enabling stickUp on the '.navbar-wrapper' class
                $('#nav').stickUp();
            });
        }
    });
</script>

<a id="inline" href="#cart_popup" class="fancybox cart-popper hide"></a>
<div style="display:none">
    <div id="cart_popup" class="row">
        <h3>My Cart</h3>
        <div class="desktop-12 tablet-6 quick-cart-total">
            Subtotal: <span class="cart_total"><span class=money>$0.00 AUD</span></span>
        </div>
        <p class="empty-cart">
            Oh dear - there isn&#39;t any goodies in your cart yet!
        </p>
    </div>
</div>

<a href="#" class="scrollup"><i class="icon-chevron-up icon-2x"></i></a>

<script src="http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js" type="text/javascript"></script>

<script src="<?php echo THEME_URL ?>js/currencies.js" type="text/javascript"></script>
<script src="<?php echo THEME_URL ?>js/jquery.currencies.min.js" type="text/javascript"></script>

<script>
    Currency.format = 'money_with_currency_format';


    var shopCurrency = 'AUD';

    /* Sometimes merchants change their shop currency, let's tell our JavaScript file */
    Currency.moneyFormats[shopCurrency].money_with_currency_format = "${{amount}} AUD";
    Currency.moneyFormats[shopCurrency].money_format = "${{amount}} AUD";

    /* Default currency */
    var defaultCurrency = 'AUD' || shopCurrency;

    /* Cookie currency */
    var cookieCurrency = Currency.cookie.read();

    /* Fix for customer account pages */
    jQuery('span.money span.money').each(function () {
        jQuery(this).parents('span.money').removeClass('money');
    });

    /* Saving the current price */
    jQuery('span.money').each(function () {
        jQuery(this).attr('data-currency-AUD', jQuery(this).html());
    });

    // If there's no cookie.
    if (cookieCurrency == null) {
        if (shopCurrency !== defaultCurrency) {
            Currency.convertAll(shopCurrency, defaultCurrency);
        }
        else {
            Currency.currentCurrency = defaultCurrency;
        }
    }
    // If the cookie value does not correspond to any value in the currency dropdown.
    else if (jQuery('[name=currencies]').size() && jQuery('[name=currencies] option[value=' + cookieCurrency + ']').size() === 0) {
        Currency.currentCurrency = shopCurrency;
        Currency.cookie.write(shopCurrency);
    }
    else if (cookieCurrency === shopCurrency) {
        Currency.currentCurrency = shopCurrency;
    }
    else {
        Currency.convertAll(shopCurrency, cookieCurrency);
    }

    jQuery('[name=currencies]').val(Currency.currentCurrency).change(function () {
        var newCurrency = jQuery(this).val();
        Currency.convertAll(Currency.currentCurrency, newCurrency);
        jQuery('.selected-currency').text(Currency.currentCurrency);
    });

    var original_selectCallback = window.selectCallback;
    var selectCallback = function (variant, selector) {
        original_selectCallback(variant, selector);
        Currency.convertAll(shopCurrency, jQuery('[name=currencies]').val());
        jQuery('.selected-currency').text(Currency.currentCurrency);
    };

    jQuery('.selected-currency').text(Currency.currentCurrency);

</script>

</body>
</html>