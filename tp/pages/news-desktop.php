<?php
/**
 * News Desktop Page Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$photo = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large')[0] : ane_dummy_thumbnail();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('landing-page ane-page-blog'); ?>>
    <header class="entry-header">
		<div class="single-post-header" style="background-image: url('<?php echo esc_url($photo); ?>');"></div>
	</header>
    <?php ane_display_breadcrumbs(); ?>
    <div class="ane-container">
        <div class="ane-col-735">
            <div class="ane-kiri">
                <?php
                get_template_part('tp/news/featured');

                if (have_rows('blog_content')) :
                    while (have_rows('blog_content')) : the_row();
                        switch (get_row_layout()) {
                            case 'home_default':
                                get_template_part('tp/news/default');
                                break;
                            case 'home_sliding':
                                get_template_part('tp/news/sliding');
                                break;
                            case 'news_columns':
                                get_template_part('tp/news/column');
                                break;
                            case 'home_classic':
                                get_template_part('tp/news/classic');
                                break;
                            case 'home_banner':
                                get_template_part('tp/section/banner');
                                break;
                            case 'home_cta':
                                get_template_part('tp/section/calltoaction');
                                break;
                        }
                    endwhile;
                endif;
                ?>
            </div>

            <?php if (is_active_sidebar('blog-sidebar')) : ?>
                <aside class="ane-kanan" id="sticky-sidebar">
                    <div class="right-sidebar sticky-top">
                        <?php dynamic_sidebar('blog-sidebar'); ?>
                    </div>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</article>
