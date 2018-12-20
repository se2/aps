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

                <!--<ul id="popups">
                    <li class="first"><a href="#pop-one" class="fancybox">Contact Us</a></li>
                    <li><a href="#pop-two" class="fancybox">Shipping</a></li>
                    <li><a href="#pop-three" class="fancybox">Returns</a></li>
                    <li><a href="#pop-four" class="fancybox">four</a></li>
                </ul>
            
                <div id="pop-one" style="display: none">
                    <div><span style="color: #000000;">We are always happy to help with any query you have.</span></div>
                    <div><span style="color: #000000;">Just hit us up below or use the contact form &amp; we'll get back to you within the day!</span></div>
                    <div>
                                                <span style="color: #000000;"><strong><strong><img
                                                                    src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                                                    width="109" height="23"></strong></strong></span>
                    </div>
                    <p style="text-align: left;"><span style="color: #000000;"><em>Email us!</em></span></p>
                    <p style="text-align: left;"><span style="color: #000000;"><img
                                    src="http://cdn.shopify.com/s/files/1/0646/4381/files/1a.jpg?15148654643661762449"
                                    width="17" height="17"> <a
                                    href="mailto:hello@tibbsandbones.com?subject=I%20have%20a%20question"
                                    style="color: #000000;">hello@tibbsandbones.com</a></span></p>
                    <p style="text-align: left;"><span style="color: #000000;"><em>Message us! (we usually reply in minutes)</em></span>
                    </p>
                    <p style="text-align: left;"><span style="color: #000000;"><img
                                    src="http://cdn.shopify.com/s/files/1/0646/4381/files/1aa.png?11487659755958618381"
                                    width="17" height="17"> <a href="https://www.facebook.com/TibbsAndBones"
                                                               target="_blank" style="color: #000000;">facebook.com/tibbsandbones</a></span>
                    </p>
                    <meta charset="utf-8">
                    <p><span style="color: #000000;"><em>Text us!</em></span></p>
                    <p><span style="color: #000000;"><img
                                    src="http://cdn.shopify.com/s/files/1/0646/4381/files/phone_a5ef87a2-1576-4a0f-9e7c-0bf13cc6918d_large.jpg?3050915820611702544"
                                    alt="" width="15" height="15"> +61 466 972 876</span></p>
                    <p style="text-align: left;"><span style="color: #000000;"><em>Tweet us!</em></span></p>
                    <p style="text-align: left;"><span style="color: #000000;"><img
                                    src="http://cdn.shopify.com/s/files/1/0646/4381/files/1aaa.png?8778773812600845958"
                                    width="17" height="17"> <a href="https://twitter.com/Tibbsandbones"
                                                               target="_blank" style="color: #000000;">/tibbsandbones</a></span>
                    </p>
                    <p style="text-align: left;"><span style="color: #000000;"><em>DM us!</em></span></p>
                    <p style="text-align: left;"><span style="color: #000000;"><img
                                    src="http://cdn.shopify.com/s/files/1/0646/4381/files/1aaaa.png?1573672082066933516"
                                    width="16" height="16"> <a href="https://instagram.com/tibbsandbones"
                                                               target="_blank" style="color: #000000;">@tibbsandbones</a> <a
                                    href="https://instagram.com/tibbsandbones_dudes" target="_blank"
                                    style="color: #000000;">@tibbsandbones_dudes</a></span></p>
                    <meta charset="utf-8">
                    <p class="p1" style="text-align: left;"><span
                                style="color: #000000;"><strong><strong><img
                                            src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                            width="109" height="23"></strong></strong></span></p>
                    <div style="text-align: center;"></div>
                    <div style="text-align: center;"></div>
                    <p><span style="color: #000000;"> </span></p>
                    <p><span style="color: #000000;"> </span></p>
                    <p><span style="color: #000000;"> </span></p></div>
                <div id="pop-two" style="display: none">
                    <p><img src="https://cdn.shopify.com/s/files/1/0646/4381/files/SHIPPING_ca876deb-644b-40fe-8704-627ff8278cda.jpg?1098390445354554762"></p>
                    <p class="p1" style="text-align: left;">We ship out from our warehouse in Melbourne,
                        Australia - right around the globe!</p>
                    <p class="p1" style="text-align: left;">The order cut off is 2pm each day. Please ensure
                        you order before this time if you would like same day dispatch. </p>
                    <p class="p1" style="text-align: left;">Once your order has been dispatched, you will
                        receive a notification via email. </p>
                    <p class="p1" style="text-align: left;"><em>MELBOURNE CUSTOMERS have the option of
                            buying now &amp; picking from our Melbourne Store at 394 High Street, Northcote,
                            Victoria, 3070, Australia (Thursday - Sunday only) </em></p>
                    <p class="p1" style="text-align: left;"><strong>AUSTRALIAN CUSTOMERS:</strong></p>
                    <p class="p1" style="text-align: left;">Items are sent safe &amp; sound with trackable
                        Australia Post "Express" service.</p>
                    <meta charset="utf-8">
                    <p class="p1">Take note that this is NO SIGNATURE service, meaning parcels are left at
                        your premises, provided it is safe to do so. If the delivery driver feels that it is
                        not a safe place to leave your items, a card will be left &amp; it will be taken to
                        your local post office.</p>
                    <p class="p1"><span style="text-decoration: underline;">EXPRESS SHIPPING</span></p>
                    <p class="p1">1-3 Business Days*</p>
                    <p class="p1">$9.95</p>
                    <p class="p1">* Please note shipments to Western Australia, Northern Territory &amp;
                        rural areas may take additional time.</p>
                    <p class="p1"> </p>
                    <p class="p1"><span style="text-decoration: underline;">FREE IN STORE PICK UP</span></p>
                    <meta charset="utf-8">
                    <p class="p1">FREE for our Melbourne customers</p>
                    <p class="p1">Available to collect from our Melbourne store during our shop business
                        hours below</p>
                    <p class="p1">Please ensure you wait until you have received your 'shipment
                        notification' via email to ensure your items are ready for collection. Please show
                        this confirmation upon pick up.</p>
                    <div>OPENING HOURS:</div>
                    <div>Monday/Tuesday: CLOSED</div>
                    <div>Wednesday/Thursday/Friday/Saturday: 11am - 5pm</div>
                    <div></div>
                    <div>Sunday: 11am - 3pm</div>
                    <div>
                        <meta charset="utf-8">
                        <em>394 HIGH STREET, NORTHCOTE, AUSTRALIA 3071</em>
                    </div>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1"><strong>NEW ZEALAND CUSTOMERS:</strong></p>
                    <meta charset="utf-8">
                    <p><strong></strong>Items are sent safe &amp; sound with Australia Post - PLUS we have
                        an Express Shipping option coming soon!</p>
                    <p class="p1"><span style="text-decoration: underline;">NON TRACKABLE SHIPPING:</span>
                    </p>
                    <p class="p1">5-15 Business Days</p>
                    <p class="p1">$12.00 AUD</p>
                    <p class="p1"><span style="text-decoration: underline;">TRACKABLE SHIPPING:</span></p>
                    <p class="p1">5-15 Business Days</p>
                    <p class="p1">$18.00 AUD</p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1"><strong>USA CUSTOMERS:</strong></p>
                    <meta charset="utf-8">
                    <p class="p1">Items are sent safe &amp; sound with Australia Post.</p>
                    <p><span style="text-decoration: underline;">NON TRACKABLE SHIPPING</span></p>
                    <p>5-15 Business Days</p>
                    <p>$12.00 AUD</p>
                    <p><span style="text-decoration: underline;">TRACKABLE SHIPPING</span></p>
                    <p>5-15 Business Days</p>
                    <p>$18.00 AUD</p>
                    <meta charset="utf-8">
                    <p class="p1"><span
                                style="text-decoration: underline;">FREE NON-TRACKABLE SHIPPING</span></p>
                    <p class="p1">5-15 Business Days</p>
                    <p class="p1">FREE when you spend $100 AUD </p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1"><strong>UK CUSTOMERS:</strong></p>
                    <meta charset="utf-8">
                    <p>Items are sent safe &amp; sound with Australia Post - PLUS we have an Express
                        Shipping option coming soon! PLEASE NOTE that we are not responsible for import
                        taxes that may be applied by your local customs agent...</p>
                    <meta charset="utf-8">
                    <p><span style="text-decoration: underline;">NON TRACKABLE SHIPPING</span></p>
                    <p>5-15 Business Days</p>
                    <p>$14.00 AUD</p>
                    <p><span style="text-decoration: underline;">TRACKABLE SHIPPING</span></p>
                    <p>5-15 Business Days</p>
                    <p>$20.00 AUD</p>
                    <meta charset="utf-8">
                    <p><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1"><strong>"REST OF THE WORLD" CUSTOMERS:</strong></p>
                    <p class="p1">Items are sent safe &amp; sound with Australia Post. </p>
                    <meta charset="utf-8">
                    <p><span style="text-decoration: underline;">NON TRACKABLE</span></p>
                    <p>5-15 Business Days</p>
                    <p>$18.00 AUD</p>
                    <p><span style="text-decoration: underline;">TRACKABLE SHIPPING </span></p>
                    <p>"Pack &amp; Track" or "Registered Post" depending on your location</p>
                    <p>5-15 Business Days</p>
                    <p>$25.00 AUD</p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1"><strong>INTERNATIONAL SHIPPING INFORMATION:</strong></p>
                    <p class="p1">PACK &amp; TRACK is a completely trackable service which is "Authority To
                        Leave", meaning that it can be left at your premise without a signature. Pack &amp;
                        Track is only available to the below countries. All other countries will be shipped
                        their goods via "Registered Post". This is a non-trackable service which requires a
                        signature upon delivery. Tracking information may sometimes be available within your
                        destination country.</p>
                    <p class="p1">BELGIUM / CANANA / CHINA / CROATIA / DENMARK / ESTONIA / FRANCE / GERMANY
                        / HONG KONG / HUNGARY / IRELAND / ISRAEL / KOREA / LITHUANIA / MALAYSIA / MALTA /
                        NETHERLANDS / NEW ZEALAND / POLAND / PORTUGAL / SINGAPORE / SLOVENIA / SPAIN /
                        SWEDEN / UK / USA</p>
                    <meta charset="utf-8">
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1">Please note that all payments of taxes &amp; duties are soley the
                        responsibility of the customer. Please contact your local Customs Office or
                        government website for further information regarding import taxes which may be
                        applicable to you. We DO NOT take responsibility for charges which may be incurred
                        &amp; we are not able to mark orders with a lower dollar value.</p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1"><strong>SHIPPING TERMS &amp; CONDITIONS</strong></p>
                    <p class="p1">During times of "FREE SHIPPING PROMOTIONS" all items will be sent via
                        Regular Post (non trackable). If you would like your items to be shipped Express (in
                        Australia) or trackable (International) you will need to pay the appropriate
                        shipping costs.</p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1">Take note that once your order has been placed it has therefore begun to
                        be processed &amp; no amendments/cancellations are able to be made. </p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1">There may be a shipping delay due to customs clearance or holiday periods
                        - these delays are outside of our control. </p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1">We take great care of your order, however once it leaves our warehouse it
                        is out of our control. We do not take responsibility for orders which are deemed as
                        ‘Successfully Delivered’, lost in the mail or the incorrect address was entered -
                        You will need to take the matter up with your local postal authorities.</p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong><img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <p class="p1">If your order has been returned to us due to an insufficient address, or
                        being 'unclaimed', you will be required to pay shipping costs for the items to be
                        resent out. If you do not wish for the items to be resent out, we will not be able
                        to return your original shipping cost paid.</p>
                    <p class="p1"> </p>
                    <meta charset="utf-8">
                    <p class="p1"><strong><strong>If you have any further queries regarding our
                                Shipping Policy - feel free to contact us below. <img
                                        src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                        width="109" height="23"></strong></strong></p>
                    <meta charset="utf-8">
                    <meta charset="utf-8">
                    <p class="p1"><img
                                src="https://cdn.shopify.com/s/files/1/0646/4381/files/1a.jpg?15148654643661762449"
                                width="17" height="17"> <a href="../../../pages/contact-us.html"
                                                           target="_blank">hello@tibbsandbones.com</a></p>
                    <p class="p1"><img
                                src="https://cdn.shopify.com/s/files/1/0646/4381/files/1aa.png?11487659755958618381"
                                width="18" height="18"> <a href="https://www.facebook.com/TibbsAndBones"
                                                           target="_blank">facebook.com/tibbsandbones</a></p>
                    <p class="p1"><img
                                src="https://cdn.shopify.com/s/files/1/0646/4381/files/1aaa.png?8778773812600845958"
                                width="17" height="17"> <a href="https://twitter.com/Tibbsandbones"
                                                           target="_blank">/tibbsandbones</a></p>
                    <p class="p1"><img
                                src="https://cdn.shopify.com/s/files/1/0646/4381/files/1aaaa.png?1573672082066933516"
                                width="16" height="16">  <a href="https://instagram.com/tibbsandbones"
                                                            target="_blank">@tibbsandbones</a> <a
                                href="https://instagram.com/tibbsandbones_dudes" target="_blank">@tibbsandbones_dudes</a>
                    </p>
                    <div></div>
                </div>
                <div id="pop-three" style="display: none">
                    <p><span style="color: #000000;"><img
                                    src="https://cdn.shopify.com/s/files/1/0646/4381/files/RETURNS_47fc3144-63f7-43fb-9d8d-47c034f1848b.jpg?16697163755161301961"
                                    style="width: 100vw;"></span></p>
                    <p class="p1"><span style="color: #000000;">We want you to LOVE your magical goodies, that is why we offer a 14 day returns period for our Australian customers &amp; a 30 day returns period for our International customers. </span>
                    </p>
                    <meta charset="utf-8">
                    <p class="p1"><span style="color: #000000;"><strong></strong><strong>HOW TO RETURN AN ITEM:</strong></span>
                    </p>
                    <ul>
                        <li><span style="color: #000000;">Email hello@tibbsandbones.com with your name, order number &amp; the items you wish you return.</span>
                        </li>
                        <li><span style="color: #000000;">You will then be given instructions on how to return your item. You may receive a credit note, exchange or refund, depending on what you request.</span>
                        </li>
                        <li><span style="color: #000000;">Returns <strong>must</strong> be posted back within 5 business days, otherwise they may be rejected.</span>
                        </li>
                        <li><span style="color: #000000;">Returns are processed within 2 business days of receiving.</span>
                        </li>
                    </ul>
                    <meta charset="utf-8">
                    <p class="p1"><span style="color: #000000;"><strong><strong><img
                                            src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                            width="109" height="23"></strong></strong></span></p>
                    <p class="p1"><span style="color: #000000;"><strong>RETURNS CONDITIONS:</strong></span>
                    </p>
                    <ul>
                        <li><span style="line-height: 1.4; color: #000000;">Items must be received in original condition. </span>
                        </li>
                        <li><span style="color: #000000;">Returns must be received within their 14 day &amp; 30 day returns period.</span>
                        </li>
                        <li><span style="color: #000000;">We do not reimburse original shipping costs, unless the item was faulty.</span>
                        </li>
                        <li><span style="color: #000000;">We do not take responsibility for the return postage costs, unless the item was faulty or there was a shipment mistake.</span>
                        </li>
                        <li><span style="color: #000000;">Jewellery, swimwear, glitter, nipple pasties, beauty products, accessories &amp; SALE items are not able to be returned.</span>
                        </li>
                    </ul>
                    <meta charset="utf-8">
                    <p><span style="color: #000000;"><strong><strong><img
                                            src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                            width="109" height="23"></strong></strong></span></p>
                    <p class="p1"><span style="color: #000000;"><b>FAULTY GARMENTS </b></span></p>
                    <p class="p1"><span style="color: #000000;">All items are assessed before being packed up safe &amp; sound and sent to their new home. If you believe that your item is faulty, please contact us at hello@tibbsandbones.com within 24 hours of receiving.</span>
                    </p>
                    <meta charset="utf-8">
                    <p class="p1"><span style="color: #000000;"><strong><strong><img
                                            src="https://cdn.shopify.com/s/files/1/0646/4381/files/White_Space_large.png?14005849721234723340"
                                            width="109" height="23"></strong></strong></span></p>
                    <p class="p1"><span style="color: #000000;"><b>KEEP IN MIND </b></span></p>
                    <p class="p1"><span style="color: #000000;">Please be sure to read item descriptions thoroughly before purchasing. If you require further information on an item, please be sure to contact us <a
                                    href="../../../pages/contact-us.html" target="_blank" style="color: #000000;">HERE</a>. Keep in mind that colours of items may vary slightly on different computer screen displays. </span>
                    </p></div>
                <div id="pop-four" style="display: none">
                    <form method="post" action="https://www.tibbsandbones.com/contact#contact_form"
                          id="contact_form" class="contact-form" accept-charset="UTF-8"><input type="hidden"
                                                                                               value="contact"
                                                                                               name="form_type"/><input
                                type="hidden" name="utf8" value="✓"/>
            
            
                        <div id="contactFormWrapper">
                            <p>
                                <label>Name</label>
                                <input type="text" id="contactFormName" name="contact[name]"
                                       placeholder="Name"/>
                            </p>
                            <p>
                                <label>Email</label>
                                <input type="email" id="contactFormEmail" name="contact[email]"
                                       placeholder="Email"/>
                            </p>
                            <p>
                                <label>Phone Number</label>
                                <input type="text" id="contactFormTelephone" name="contact[phone]"
                                       placeholder="Phone Number"/>
                            </p>
            
                            <input type="hidden" name="contact[product]"
                                   value="CHUNKY PINK BIODEGRADABLE LOOSE GLITTER">
                            <input type="hidden" name="contact[producturl]"
                                   value="../../../products/chunky-pink-biodegradable-loose-glitter.html">
            
                            <p>
                                <label>Message</label>
                                <textarea rows="15" cols="90" id="contactFormMessage" name="contact[body]"
                                          placeholder="Message"></textarea>
                            </p>
                            <p>
                                <input type="submit" id="contactFormSubmit" class="secondary button"
                                       value="Send"/>
                            </p>
                        </div>
            
                    </form>
                </div>-->
            </div>

            <?php echo apply_filters( 'woocommerce_short_description', $product->description ) ?>

            <div class="desc">
                <div class="share-icons">
                    <a href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink() ?>" class="facebook" target="_blank"><i class="fa fa-facebook fa-2x"></i></a>
                    <a href="http://twitter.com/home?status=<?php echo get_permalink() ?>" title="Share on Twitter" target="_blank" class="twitter"><i class="fa fa-twitter fa-2x"></i></a>
                    <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( ) );?>
                    <a target="blank" href="http://pinterest.com/pin/create/button/?url=<?php echo get_permalink() ?>&amp;media=<?php echo $image[0] ?>" title="Pin This Product" class="pintrest"><i class="fa fa-pinterest fa-2x"></i></a>
                </div>
            </div>

        </div>
    </div>

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
</div>
<div class="clear"></div>

<div id="looked-at" class="desktop-12 mobile-hide">
    <div id="recently-viewed-products" class="collection clearfix">
        <h4>You also Viewed</h4>

        <?php echo do_shortcode('[woocommerce_recently_viewed_products per_page="6"]') ?>
    </div>
</div>