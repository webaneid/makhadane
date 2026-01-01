<?php
/**
 * Default News Layout Template Part
 *
 * @package makhadane
 * @since 4.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$homekategori  = get_sub_field('pilih_kategori');
$judul         = get_sub_field('custom_title');
$category_id   = $homekategori->term_id ?? 0;
$category_name = esc_html($homekategori->name ?? '');
$category_link = $category_id ? esc_url(get_category_link($category_id)) : '#';

// Ambil 6 post sekaligus untuk menghindari banyak query
$query = new WP_Query([
    'post_type'      => 'post',
    'cat'            => $category_id,
    'posts_per_page' => 6, // 1 post utama + 5 post lainnya
    'no_found_rows'  => true, // Optimasi query
]);
?>
<section class="ane-news-default ane-blog-section">
    <div class="ane-col-column">
        <!-- Section Title -->
        <div class="section-title">
            <div class="section-title-item">
                <a href="<?php echo $category_link; ?>">
                    <h2><?php echo esc_html($judul ?: $category_name); ?></h2>
                </a>
            </div>
            <a class="lainnya" href="<?php echo $category_link; ?>">
                <?php esc_html_e('View All', 'makhadane'); ?> <i class="ane-chevron-right-alt-2"></i>
            </a>
        </div>

        <?php if ($query->have_posts()) : ?>
        <div class="ane-col-46">
            <?php $counter = 0; ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php if ($counter === 0) : ?>
                    <!-- Kolom Kiri (Post Utama) -->
                    <div class="ane-kiri">
                        <?php get_template_part('tp/content', 'overlay'); ?>
                    </div>
                <?php else : ?>
                    <!-- Kolom Kanan (Post Lainnya) -->
                    <?php if ($counter === 1) echo '<div class="ane-kanan">'; ?>
                        <?php get_template_part('tp/content', 'list'); ?>
                    <?php if ($counter === 5) echo '</div>'; ?>
                <?php endif; ?>
                <?php $counter++; ?>
            <?php endwhile; ?>
            <?php if ($counter > 1) echo '</div>'; ?>
        </div>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
</section>
