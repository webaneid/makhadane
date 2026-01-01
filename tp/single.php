<?php
/**
 * Single Post Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$thumbImg = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large');
$photo = esc_url($thumbImg[0] ?? ane_dummy_thumbnail());
$tanggal_iso = esc_attr(get_the_date('c', $post_id));
$tanggal_format = esc_html(get_the_date('l, j F Y', $post_id));
$tanggal_modified = esc_attr(get_the_modified_date('c', $post_id));
$author_name = esc_html(get_the_author());
$categories = get_the_category();

$category_names = array_map(fn($cat) => esc_html($cat->cat_name), $categories);
$category_links = array_map(fn($cat) => '<a class="post-cat" href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->cat_name) . '</a>', $categories);
$category_markup = implode(', ', $category_links);
$tag_list = get_the_tag_list('<ul><li>', '</li><li>', '</li></ul>');
?>

<article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('ane-post-single'); ?> itemscope itemtype="https://schema.org/Article">
    <meta itemprop="mainEntityOfPage" content="<?php echo esc_url(get_permalink()); ?>">
    <meta itemprop="author" content="<?php echo esc_attr($author_name); ?>">
    <meta itemprop="datePublished" content="<?php echo esc_attr($tanggal_iso); ?>">
    <meta itemprop="dateModified" content="<?php echo esc_attr($tanggal_modified); ?>">
    <meta itemprop="publisher" content="<?php bloginfo('name'); ?>">
    <meta itemprop="image" content="<?php echo esc_url($photo); ?>">

    <header class="entry-header">
        <div class="single-post-header" style="background-image: url('<?php echo $photo; ?>');"></div>
    </header>

    <?php ane_display_breadcrumbs(); ?>

    <div class="entry-content">
        <div class="ane-single-title">
            <div class="ane-kategori hidden-mobile"><?php echo $category_markup; ?></div>
            <?php
            echo ane_get_title();
            echo ane_single_meta();
            ?>
        </div>

        <?php ane_get_featured_image(); ?>

        <div class="ane-content" itemprop="articleBody">

            <div class="share-this">
                <?php echo ane_social_share(); ?>
            </div>

            <?php the_content(); ?>

            <?php if ($tag_list) : ?>
                <div class="post-tags">
                    <div class="post-tags-wrapper">
                        <h2><?php esc_html_e('Tags', 'makhadane'); ?></h2>
                        <?php echo $tag_list; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="post-nav"><?php echo ane_prev_next_post(); ?></div>

            <div class="terkait-newest">
                <div class="ane-col-55">
                    <div class="ane-kiri">
                        <div class="related-post"><?php echo ane_related_post(); ?></div>
                    </div>
                    <div class="ane-kanan">
                        <div class="newest-post"><?php echo ane_newest_posts(); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
