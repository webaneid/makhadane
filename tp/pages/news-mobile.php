<?php
/**
 * News Mobile Page Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('landing-page ane-page-blog'); ?>>
    <?php
    $paged = get_query_var('paged') ?: get_query_var('page') ?: 1;

    if ($paged == 1) {
        get_template_part('tp/news/featured');

        if (have_rows('blog_content')) :
            while (have_rows('blog_content')) : the_row();
                if (get_row_layout() === 'home_banner') {
                    get_template_part('tp/section/banner');
                }
            endwhile;
        endif;
    }

    $mobile_query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 17,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ]);

    echo '<section class="ane-post-mobile">';

    if ($mobile_query->have_posts()) :
        $special_posts = [5, 11];
        $counter = -1;

        while ($mobile_query->have_posts()) : $mobile_query->the_post();
            $counter++;
            get_template_part('tp/content', in_array($counter, $special_posts) ? 'overlay' : 'list');
        endwhile;

        echo '</section>';

        if ($mobile_query->max_num_pages > 1) :
            $orig_query = $wp_query;
            $wp_query = $mobile_query;
            echo ane_post_pagination();
            $wp_query = $orig_query;
        endif;
    else :
        echo '<p>' . esc_html__('Sorry, no posts matched your criteria.', 'makhadane') . '</p>';
    endif;

    wp_reset_postdata();
    ?>
</article>
