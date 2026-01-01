<?php
/**
 * Featured News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="ane-news-featured ane-blog-section">
    <div class="ane-col-column">
        <div class="ane-col-55">
            <!-- Kolom Kiri: Featured Posts -->
            <div class="ane-kiri">
                <div class="home-big-slider owl-carousel">
                    <?php
                    $featured_posts = new WP_Query([
                        'post_type'      => 'post',
                        'posts_per_page' => 5,
                        'post_status'    => 'publish',
                        'meta_key'       => 'ane_utama',
                        'meta_value'     => '1',
                    ]);

                    if ($featured_posts->have_posts()) :
                        while ($featured_posts->have_posts()) : $featured_posts->the_post();
                            get_template_part('tp/content', 'overlay');
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>

            <!-- Kolom Kanan: Recent Posts -->
            <div class="ane-kanan">
                <?php
                $recent_posts = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 5,
                    'post_status'    => 'publish',
                    'ignore_sticky_posts' => true, // Agar lebih optimal
                ]);

                if ($recent_posts->have_posts()) :
                    while ($recent_posts->have_posts()) : $recent_posts->the_post();
                        get_template_part('tp/content', 'list');
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
        </div>
    </div>
</section>