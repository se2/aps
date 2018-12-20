<?php get_header(); ?>
    <?php if( get_field('display_home_banner', 'option' ) && have_rows('banner_slider', 'option' ) ): ?>
        <div class="homepage-section no-fouc">
            <div class="flexslider">
                <ul class="slides">
                    <?php $bannerType = get_field('banner_type', 'option' ) ?>
                    <?php if( $bannerType == 'youtube' ) : ?>
                        <?php
                        $videoInfo = parse_video_uri( get_field('youtube_video', 'option' ) );
                        if( $videoInfo['type'] == 'youtube' ) :
                            $youtubeId = $videoInfo['id'];
                            $autoPlay = get_field('auto_play', 'option' ) ? '1' : '0';
                        ?>
                            <li>
                                <iframe id="home_player" width="100%" src="https://www.youtube.com/embed/<?php echo $youtubeId ?>?rel=0&showinfo=0&autoplay=<?php echo $autoPlay ?>" frameborder="0" allowfullscreen></iframe>
                            </li>

                            <script>
                                $('document').ready(function() {
                                    var windowHeight = $(window).height()
                                    $('#home_player').attr('height', windowHeight * 90 / 100);
                                })
                            </script>

                        <?php endif; ?>

                    <?php elseif ( $bannerType == 'mp4') : ?>
                        <?php $video = get_field('banner_video', 'option' ) ?>
                        <?php $autoPlay = get_field('auto_play', 'option' ) ? 'autoplay' : 'controls'; ?>
                        <?php $muteVideo = get_field('mute_video', 'option' ) ? 'muted' : ''; ?>

                        <video id="bgvid" playsinline <?php echo $autoPlay . ' ' . $muteVideo ?> loop poster="">
                            <source src="<?php echo $video['url'] ?>" type="video/mp4">
                        </video>

                        <script>
                            var video = $('#bgvid');
                            var videoWidth,
                                videoHeight;

                            video.bind('loadedmetadata', function () {
                                videoWidth = video.width();
                                videoHeight = video.height();
                            });

                            $('document').ready(function() {
                                var windowWidth = $(window).width(),
                                    windowHeight = $(window).height();

                                if (windowWidth / windowHeight < videoWidth / videoHeight) {
                                    var newWidth = windowHeight * videoWidth / videoHeight;
                                    video.css({
                                        'height': '90%',
                                        'top': 0,
                                        'left': (windowWidth - newWidth) / 2
                                    });
                                } else {
                                    var newHeight = windowWidth * videoHeight / videoWidth;
                                    video.css({
                                        'width': '100%',
                                        'height': newHeight,
                                        'top': (windowHeight - newHeight) / 2,
                                        'left': 0
                                    });
                                }
                            })
                        </script>

                    <?php elseif ( $bannerType == 'image') : ?>

                        <?php while( have_rows('banner_slider', 'option' ) ) : the_row() ?>
                            <?php $banner = get_sub_field('banner_image'); ?>
                            <?php $url = get_sub_field('banner_url'); ?>
                            <li>
                                <a target="_blank" href="<?php echo $url ?>">
                                    <img src="<?php echo $banner['url'] ?>" alt="<?php the_sub_field('banner_title') ?>"/>
                                </a>
                            </li>
                        <?php endwhile ?>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="clear"></div>
    <?php endif ?>

    <?php if( get_field('display_spotlight_banner', 'option' ) && have_rows('spotlight_banner_row', 'option') ) :?>
        <?php while( have_rows('spotlight_banner_row', 'option') ) : the_row() ?>
            <div class="row homepage-section no-fouc">
                <?php while( have_rows('spotlight_banner', 'option') ) : the_row() ?>
                    <?php $banner = get_sub_field('spotlight_banner_image'); ?>
                    <div class="homepage-promo desktop-4 tablet-2 mobile-3">
                        <a target="_blank" href="<?php the_sub_field('spotlight_banner_url') ?>">
                            <img src="<?php echo $banner['url'] ?>" alt="<?php the_sub_field('spotlight_banner_title') ?>" />
                            <div class="caption">
                                <h3><?php the_sub_field('spotlight_banner_title') ?></h3>
                                <p><?php the_sub_field('spotlight_banner_caption') ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile ?>
            </div>
        <?php endwhile ?>
    <?php endif ?>

    <?php if( get_field( 'display_instagram', 'option' ) && get_field( 'user_id', 'option' ) && get_field( 'access_token', 'option' ) ): ?>
        <section id="index-social" class="row homepage-section no-fouc">
            <?php if( get_field( 'instagram_title', 'option' ) ): ?>
                <h2 class="desktop-12 mobile-3"><?php the_field( 'instagram_title', 'option' ) ?></h2>
            <?php endif ?>
            <div id="instagram-feed">
                <div id="instafeed"></div>
                <script type="text/javascript">
                    var userFeed = new Instafeed({
                        get: 'user',
                        userId: <?php the_field( 'user_id', 'option' ) ?>,
                        accessToken: '<?php the_field( 'access_token', 'option' ) ?>',
                        template: '<a class="desktop-2 tablet-1 mobile-1" target="_blank" href="{{link}}" rel="ig" title="{{caption}}"><img class="instagram-image" src="{{image}}" /></a>'
                    });
                    userFeed.run();
                </script>
            </div>
        </section>
    <?php endif ?>

    <div class="load-wait">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
</div>

<?php get_footer(); ?>